<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index()
    {
        // Memanggil resources/views/admin/events.blade.php
        return view('admin.events'); 
    }
}