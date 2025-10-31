<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\RsvpConfirmedMail;
use App\Models\Rsvp;
use App\Models\Event;
use App\Models\Seat;

class RsvpController extends Controller
{
    public function rsvp(Request $request, Event $event)
    {
        $request->validate([
            'seat_id' => 'required|exists:seats,id',
        ]);

        // 🔒 Get the selected seat for this event
        $seat = Seat::where('id', $request->seat_id)
            ->where('event_id', $event->id)
            ->firstOrFail();

        // ❌ Prevent booking if seat already taken
        if ($seat->status !== 'available') {
            return response()->json(['message' => 'Seat already taken'], 400);
        }

        // 🧾 Mark seat as processing temporarily to prevent race conditions
        $seat->update(['status' => 'processing']);

        try {
            if (Auth::check()) {
                // 🧍‍♂️ Logged-in user RSVP
                $user = Auth::user(); // ✅ define the variable first

                $rsvp = Rsvp::updateOrCreate(
                    ['event_id' => $event->id, 'user_id' => $user->id],
                    [
                        'seat_id' => $seat->id,
                        'email' => $user->email, // ✅ store user’s email
                        'status' => 'confirmed',
                    ]
                );
            } else {
                // 🧍 Guest RSVP
                $request->validate([
                    'guest_name' => 'required|string|max:255',
                    'guest_email' => 'required|email|max:255',
                ]);

                $rsvp = Rsvp::updateOrCreate(
                    ['event_id' => $event->id, 'guest_email' => $request->guest_email],
                    [
                        'guest_name' => $request->guest_name,
                        'seat_id' => $seat->id,
                        'status' => 'confirmed',
                    ]
                );
            }

            // ✅ Mark seat as booked once RSVP succeeds
            $seat->update(['status' => 'booked']);

            // 📧 Send confirmation email
            try {
                Mail::to($rsvp->guest_email ?? $rsvp->email)
                    ->send(new RsvpConfirmedMail($rsvp));
            } catch (\Throwable $e) {
                Log::error('RSVP Mail failed: ' . $e->getMessage());
            }

            return response()->json([
                'message' => 'RSVP confirmed and seat booked',
                'rsvp' => $rsvp,
                'seat' => $seat,
            ]);

        } catch (\Throwable $e) {
            // Rollback seat if anything fails
            $seat->update(['status' => 'available']);
            Log::error('RSVP failed: ' . $e->getMessage());

            return response()->json(['message' => 'RSVP failed, please try again'], 500);
        }
    }

    public function guestRsvp(Request $request, Event $event)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
        ]);

        // Check if event is full
        $bookedCount = $event->rsvps()->count();
        if ($bookedCount >= $event->capacity) {
            return response()->json(['message' => 'Event is full'], 400);
        }

        // Ensure guest not already RSVP'd
        // rsvps table stores guest emails in 'guest_email'
        $existing = $event->rsvps()->where('guest_email', $validated['email'])->first();
        if ($existing) {
            return response()->json(['message' => 'You already RSVP’d for this event'], 400);
        }

        // Find first available seat
        $seat = $event->seats()
        ->where('status', 'available')
        ->inRandomOrder()
        ->first();

        if (!$seat) {
            return response()->json(['message' => 'No available seats left'], 400);
        }

        // Reserve the seat
        $seat->update(['status' => 'booked']);

        // Create RSVP and attach seat_id
        $rsvp = $event->rsvps()->create([
            'guest_name' => $validated['name'],
            'guest_email' => $validated['email'],
            'seat_id' => $seat->id,
            'status' => 'confirmed',
        ]);

        // make sure seat relation is available immediately
        $rsvp->load('seat');

        // Send confirmation email
        try {
            Mail::to($validated['email'])->send(new RsvpConfirmedMail($rsvp));
        } catch (\Throwable $e) {
            Log::error('Guest RSVP Mail failed: ' . $e->getMessage());
        }

        return response()->json([
            'message' => 'RSVP successful! Seat assigned automatically.',
            'seat' => $rsvp->seat,
            'seat_number' => $rsvp->seat->label,
        ], 201);
    }

    public function index(Request $request)
    {
        $user = $request->user();

        // Admin sees all RSVPs
        if ($user->role === 'admin') {
            $rsvps = Rsvp::with(['event', 'seat', 'user'])->get();
        } else {
            // User sees only their own RSVPs
            $rsvps = Rsvp::with(['event', 'seat'])
                ->where('user_id', $user->id)
                ->get();
        }

        // Map results so that frontend always has a 'name' and 'email'
        $rsvps = $rsvps->map(function ($rsvp) {
            return [
                'id' => $rsvp->id,
                'event' => [
                    'id' => $rsvp->event->id ?? null,
                    'title' => $rsvp->event->title ?? 'Unknown Event',
                    'date' => $rsvp->event->date ?? null,
                    'location' => $rsvp->event->location ?? null,
                ],
                'seat' => [
                    'id' => $rsvp->seat->id ?? null,
                    'label' => $rsvp->seat->label ?? 'N/A',
                ],
                'status' => $rsvp->status,
                'name' => $rsvp->guest_name ?? $rsvp->user->name ?? 'N/A',
                'email' => $rsvp->guest_email ?? $rsvp->user->email ?? 'N/A',
            ];
        });

        return response()->json($rsvps);
    }

    public function show(Rsvp $rsvp)
    {
        // Authorization check
        if (Auth::user()->role !== 'admin' && $rsvp->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $rsvp->load(['event', 'seat']);

        return response()->json([
            'rsvp' => $rsvp,
            'event' => [
                'name' => $rsvp->event->name,
                'location' => $rsvp->event->location,
                'date' => $rsvp->event->date,
            ],
            'seat' => $rsvp->seat->label ?? 'N/A',
            'status' => $rsvp->status,
        ]);
    }

    public function cancel(Rsvp $rsvp)
    {
        // Only admin or the user who owns it can cancel
        if (Auth::user()->role !== 'admin' && $rsvp->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        // Release the seat
        if ($rsvp->seat) {
            $rsvp->seat->update(['status' => 'available']);
        }

        // Update RSVP status
        $rsvp->update(['status' => 'canceled']);

        // Send cancellation email
        try {
            Mail::to($rsvp->guest_email ?? $rsvp->user->email)
                ->send(new \App\Mail\RsvpCancelledMail($rsvp));
        } catch (\Throwable $e) {
            Log::error('RSVP Cancel Mail failed: ' . $e->getMessage());
        }

        return response()->json(['message' => 'RSVP cancelled successfully']);
    }
}
