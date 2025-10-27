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
}
