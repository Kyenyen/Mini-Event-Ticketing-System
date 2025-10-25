<?php

namespace App\Http\Controllers;

use App\Models\Event;

class SeatController extends Controller
{
    public function index(Event $event)
    {
        return $event->seats()->select('id', 'label', 'status')->get();
    }
}
