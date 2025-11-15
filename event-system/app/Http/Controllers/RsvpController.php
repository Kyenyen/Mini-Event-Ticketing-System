<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
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

        // Mark seat as processing to avoid races
        $seat->update(['status' => 'processing']);

        DB::beginTransaction();
        try {
            if (Auth::check()) {
                // Authenticated user flow — ensure only one RSVP per event per user
                $user = Auth::user();

                // Try to find existing RSVP for this user+event (including canceled)
                $existing = Rsvp::where('event_id', $event->id)
                    ->where('user_id', $user->id)
                    ->first();

                if ($existing) {
                    // If user already has a confirmed RSVP for this event, do not change it.
                    if ($existing->status === 'confirmed') {
                        // Release temporary processing flag and abort
                        $seat->update(['status' => 'available']);
                        DB::rollBack();
                        return response()->json(['message' => 'You already have an RSVP for this event'], 400);
                    }

                    // If RSVP exists but is canceled (or not confirmed), we will reuse that record and confirm it.
                    // Release any previously assigned seat for that RSVP (if present)
                    if ($existing->seat) {
                        $existing->seat->update(['status' => 'available']);
                    }

                    $existing->update([
                        'seat_id' => $seat->id,
                        'email' => $user->email,
                        'status' => 'confirmed',
                    ]);

                    // mark seat booked
                    $seat->update(['status' => 'booked']);

                    $rsvp = $existing->fresh();
                } else {
                    // Create a new RSVP for the user
                    $rsvp = Rsvp::create([
                        'event_id' => $event->id,
                        'user_id' => $user->id,
                        'seat_id' => $seat->id,
                        'email' => $user->email,
                        'status' => 'confirmed',
                    ]);

                    // mark seat booked
                    $seat->update(['status' => 'booked']);
                }

                // Optionally send confirmation email for logged-in users
                try {
                    Mail::to($rsvp->email)->send(new RsvpConfirmedMail($rsvp));
                } catch (\Throwable $e) {
                    Log::error('RSVP Mail failed: ' . $e->getMessage());
                }

                DB::commit();

                return response()->json([
                    'message' => 'RSVP confirmed and seat booked',
                    'rsvp' => $rsvp,
                    'seat' => $seat,
                ]);
            } else {
                // Guest flow (kept as-is / reuse canceled rows handled elsewhere)
                $request->validate([
                    'guest_name' => 'required|string|max:255',
                    'guest_email' => 'required|email|max:255',
                ]);

                // Look for an existing RSVP for this guest (may be canceled)
                $existingGuest = $event->rsvps()->where('guest_email', $request->guest_email)->first();
                if ($existingGuest && $existingGuest->status === 'confirmed') {
                    // Release processing flag
                    $seat->update(['status' => 'available']);
                    DB::rollBack();
                    return response()->json(['message' => 'You already RSVP’d for this event'], 400);
                }

                if ($existingGuest && $existingGuest->status === 'canceled') {
                    // reuse canceled row
                    if ($existingGuest->seat) {
                        $existingGuest->seat->update(['status' => 'available']);
                    }

                    $existingGuest->update([
                        'guest_name' => $request->guest_name,
                        'seat_id' => $seat->id,
                        'status' => 'confirmed',
                    ]);
                    $seat->update(['status' => 'booked']);
                    $rsvp = $existingGuest->fresh();
                } else {
                    $rsvp = $event->rsvps()->create([
                        'guest_name' => $request->guest_name,
                        'guest_email' => $request->guest_email,
                        'seat_id' => $seat->id,
                        'status' => 'confirmed',
                    ]);
                    $seat->update(['status' => 'booked']);
                }

                try {
                    Mail::to($rsvp->guest_email)->send(new RsvpConfirmedMail($rsvp));
                } catch (\Throwable $e) {
                    Log::error('Guest RSVP Mail failed: ' . $e->getMessage());
                }

                DB::commit();

                return response()->json([
                    'message' => 'RSVP successful! Seat assigned automatically.',
                    'seat' => $rsvp->seat,
                    'seat_number' => $rsvp->seat->label,
                ], 201);
            }
        } catch (\Throwable $e) {
            DB::rollBack();
            // restore seat if failed
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
        $bookedCount = $event->rsvps()->where('status', 'confirmed')->count();
        if ($bookedCount >= $event->capacity) {
            return response()->json(['message' => 'Event is full'], 400);
        }

        // Look for an existing RSVP for this guest (may be canceled)
        $existing = $event->rsvps()->where('guest_email', $validated['email'])->first();
        if ($existing && $existing->status === 'confirmed') {
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

    DB::beginTransaction();
        try {
            // Reserve the seat
            $seat->update(['status' => 'booked']);

            if ($existing && $existing->status === 'canceled') {
                // Reuse canceled RSVP record (avoids DB unique constraint)
                $existing->update([
                    'guest_name' => $validated['name'],
                    'seat_id' => $seat->id,
                    'status' => 'confirmed',
                ]);
                $rsvp = $existing->fresh();
            } else {
                // Create new RSVP
                $rsvp = $event->rsvps()->create([
                    'guest_name' => $validated['name'],
                    'guest_email' => $validated['email'],
                    'seat_id' => $seat->id,
                    'status' => 'confirmed',
                ]);
            }

            // make sure seat relation is available immediately
            $rsvp->load('seat');

            // Send confirmation email
            try {
                Mail::to($validated['email'])->send(new RsvpConfirmedMail($rsvp));
            } catch (\Throwable $e) {
                Log::error('Guest RSVP Mail failed: ' . $e->getMessage());
            }

            DB::commit();

            return response()->json([
                'message' => 'RSVP successful! Seat assigned automatically.',
                'seat' => $rsvp->seat,
                'seat_number' => $rsvp->seat->label,
            ], 201);
        } catch (\Throwable $e) {
            DB::rollBack();
            // restore seat if failed
            $seat->update(['status' => 'available']);
            Log::error('Guest RSVP transaction failed: ' . $e->getMessage());
            return response()->json(['message' => 'RSVP failed, please try again'], 500);
        }
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

    public function manage()
    {
        $rsvps = Rsvp::with(['event', 'seat', 'user'])
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($rsvp) {
                return [
                    'id' => $rsvp->id,
                    'event' => [
                        'id' => $rsvp->event->id ?? null,
                        'title' => $rsvp->event->title ?? 'Unknown Event',
                        'date' => $rsvp->event->date ?? null,
                        'location' => $rsvp->event->location ?? null,
                    ],
                    'user' => [
                        'name' => $rsvp->user->name ?? $rsvp->guest_name ?? 'Guest',
                        'email' => $rsvp->user->email ?? $rsvp->guest_email ?? 'N/A',
                    ],
                    'seat' => [
                        'label' => $rsvp->seat->label ?? 'N/A',
                    ],
                    'status' => $rsvp->status ?? 'active',
                    'created_at' => $rsvp->created_at->toDateTimeString(),
                ];
            });

        return response()->json($rsvps);
    }
}
