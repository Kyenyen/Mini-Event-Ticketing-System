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

        // 🔒 Get the selected seat
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
            // 🧍‍♂️ Handle RSVP based on login status
            if (Auth::check()) {
                $rsvp = Rsvp::updateOrCreate(
                    ['event_id' => $event->id, 'user_id' => Auth::id()],
                    ['seat_id' => $seat->id, 'status' => 'confirmed']
                );
            } else {
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
                Mail::to($rsvp->guest_email ?? $rsvp->user->email)
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
}
