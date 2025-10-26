<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    // ✅ Admin creates an event
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date' => 'required|date',
            'location' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
        ]);

        $event = Event::create($request->all());

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
}
