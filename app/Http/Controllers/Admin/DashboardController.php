<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Category;
use App\Models\Partner;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Berapa bulan ke belakang yang ditampilkan di grafik pertumbuhan.
     */
    private const GROWTH_MONTHS = 6;

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

        // ====================================================================
        // DATA GRAFIK: Pertumbuhan Pengguna & Event (6 bulan terakhir)
        // ====================================================================
        $userGrowth = $this->monthlyGrowth(User::query());
        $eventGrowth = $this->monthlyGrowth(Event::query());

        return view('admin.dashboard', compact(
            'totalEvents',
            'totalCategories',
            'totalPartners',
            'totalStock',
            'latestEvents',
            'topCategories',
            'userGrowth',
            'eventGrowth'
        ));
    }

    /**
     * Hitung jumlah record baru per bulan untuk N bulan terakhir (lihat
     * const GROWTH_MONTHS), dan isi 0 untuk bulan yang tidak ada datanya
     * sama sekali - supaya grafik tetap rapi/berurutan tanpa bulan yang
     * "hilang" begitu saja.
     *
     * Dipakai untuk model apapun yang punya kolom created_at standar
     * (User, Event, dll) - jadi tidak perlu tulis query serupa 2x.
     */
    private function monthlyGrowth($query): array
    {
        $raw = $query
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as total")
            ->where('created_at', '>=', now()->subMonths(self::GROWTH_MONTHS - 1)->startOfMonth())
            ->groupBy('month')
            ->pluck('total', 'month');

        $labels = [];
        $data = [];

        for ($i = self::GROWTH_MONTHS - 1; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $key = $date->format('Y-m');

            $labels[] = $date->translatedFormat('M Y');
            $data[] = (int) ($raw[$key] ?? 0);
        }

        return ['labels' => $labels, 'data' => $data];
    }
}