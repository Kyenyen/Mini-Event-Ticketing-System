<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Seat;
use Illuminate\Http\Request;

class EventController extends Controller
{
    // ✅ Admin creates an event
    public function store(Request $request)
    {
        $validated =$request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date' => 'required|date',
            'location' => 'required|string|max:255',
            'capacity' => 'required|integer|min:10|max:100',
        ]);

        $event = Event::create($validated);

        // 🪑 Auto-generate seats based on capacity
        $this->generateSeats($event);

        return response()->json(['message' => 'Event created successfully', 'event' => $event], 201);
    }

    // ✅ List all events (for everyone)
    public function index()
    {
        return Event::all();
    }

    // ✅ Show single event details
    public function show(Event $event)
    {
        return $event;
    }

    // ✅ Admin updates an event
    public function update(Request $request, Event $event)
    {
        $event->update($request->all());
        return response()->json(['message' => 'Event updated', 'event' => $event]);
    }

    // ✅ Admin deletes an event
    public function destroy(Event $event)
    {
        $event->delete();
        return response()->json(['message' => 'Event deleted']);
    }

    private function generateSeats(Event $event)
    {
        $capacity = min($event->capacity, 100);

        // Define seat rows (A, B, C...) dynamically
        $rows = range('A', 'Z');
        $currentRow = 0;
        $seatNum = 1;

        for ($i = 1; $i <= $capacity; $i++) {
            // move to next row after every 10 seats (you can adjust)
            if ($seatNum > 10) {
                $seatNum = 1;
                $currentRow++;
            }

            Seat::create([
                'event_id' => $event->id,
                'label' => $rows[$currentRow] . $seatNum,
                'status' => 'available',
            ]);

            $seatNum++;
        }
    }
}
