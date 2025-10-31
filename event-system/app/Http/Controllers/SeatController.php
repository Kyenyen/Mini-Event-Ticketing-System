<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Seat;
use Illuminate\Http\Request;

class SeatController extends Controller
{
    public function index(Event $event)
    {
        $seats = $event->seats()
            ->select('id', 'label', 'status')
            ->get();

        return response()->json($seats);
    }

    // ✅ Admin: block a seat
    public function block(Request $request, Seat $seat)
    {
        $seat->status = 'blocked';
        $seat->save();

        return response()->json(['message' => 'Seat blocked successfully', 'seat' => $seat]);
    }

    // ✅ Admin: unblock a seat
    public function unblock(Request $request, Seat $seat)
    {
        $seat->status = 'available';
        $seat->save();

        return response()->json(['message' => 'Seat unblocked successfully', 'seat' => $seat]);
    }

    public function updateStatus(Request $request, Seat $seat)
    {
        $validated = $request->validate([
            'status' => 'required|in:available,blocked,booked,processing',
        ]);

        $seat->update(['status' => $validated['status']]);
        return response()->json(['message' => 'Seat updated', 'seat' => $seat]);
    }
}