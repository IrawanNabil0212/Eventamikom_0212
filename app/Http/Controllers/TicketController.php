<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TicketController extends Controller
{
    // Untuk halaman tiket
    public function show()
    {
        return view('ticket');
    }
}