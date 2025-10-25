<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\RsvpConfirmedMail;
use App\Models\Rsvp;
use App\Models\Event;

class RsvpController extends Controller
{
    public function rsvp(Request $request, Event $event)
    {
        // Check if event is full (implement isFull() in Event model)
        if (method_exists($event, 'isFull') && $event->isFull()) {
            return response()->json(['message' => 'Event capacity is full'], 400);
        }

        // Logged-in user
        if (Auth::check()) {
            $rsvp = Rsvp::updateOrCreate(
                ['event_id' => $event->id, 'user_id' => Auth::id()],
                ['status' => 'confirmed']
            );
        } 
        // Guest RSVP
        else {
            $request->validate([
                'guest_name' => 'required|string|max:255',
                'guest_email' => 'required|email|max:255',
            ]);

            $rsvp = Rsvp::updateOrCreate(
                ['event_id' => $event->id, 'guest_email' => $request->guest_email],
                ['guest_name' => $request->guest_name, 'status' => 'confirmed']
            );
        }

        // Send confirmation email
        try {
            Mail::to($rsvp->guest_email ?? $rsvp->user->email)
                ->send(new RsvpConfirmedMail($rsvp));
        } catch (\Throwable $e) {
            // Don’t crash if mail fails
            Log::error('RSVP Mail failed: ' . $e->getMessage());
        }

        return response()->json([
            'message' => 'RSVP confirmed',
            'rsvp' => $rsvp
        ]);
    }
}
