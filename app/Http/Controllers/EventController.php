<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Event;

class EventController extends Controller
{
    public function show($id)
    {
        $event = Event::with('category')->findOrFail($id);
        return view('events.show', compact('event'));
    }

    public function ticket()
    {
        return view('ticket');
    }

    public function checkout()
    {
        return view('checkout');
    }
}