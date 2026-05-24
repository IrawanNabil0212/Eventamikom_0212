<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Category;
use App\Models\Partner;

class DashboardController extends Controller
{
    public function index()
    {
        // Statistik
        $totalEvents     = Event::count();
        $totalCategories = Category::count();
        $totalPartners   = Partner::count();
        $totalStock      = Event::sum('stock');

        // Event terbaru
        $latestEvents = Event::with('category')->latest()->take(5)->get();

        // Kategori terpopuler
        $topCategories = Category::withCount('events')
                            ->orderByDesc('events_count')
                            ->take(5)
                            ->get();

        return view('admin.dashboard', compact(
            'totalEvents',
            'totalCategories',
            'totalPartners',
            'totalStock',
            'latestEvents',
            'topCategories'
        ));
    }
}