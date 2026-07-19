<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;

class OrganizerDashboardController extends Controller
{
    /**
     * Dashboard utama organizer: total pendapatan, jumlah tiket
     * terjual, jumlah event, dan daftar event terbaru.
     *
     * CATATAN PENTING soal isolasi data:
     * - `Event::` otomatis ke-filter oleh Global Scope (OrganizationScope)
     *   karena user yang login role-nya 'organizer'.
     * - Tapi `Transaction::` TIDAK punya Global Scope (karena Transaction
     *   dipakai juga oleh buyer & admin untuk keperluan lain), jadi di
     *   sini kita filter transaksi MANUAL lewat relasi ke event yang
     *   organization_id-nya sama dengan organizer yang login.
     */
    public function index()
    {
        $totalEvents = Event::count(); // sudah otomatis ke-filter oleh Global Scope

        // whereHas('event', ...) membuat query baru ke model Event di
        // baliknya, yang OTOMATIS ikut kena Global Scope juga - jadi
        // transaksi yang dihitung sudah pasti cuma dari event milik
        // organizer yang sedang login, tanpa perlu filter manual lagi.
        $totalRevenue = Transaction::whereHas('event')
            ->where('status', 'success')
            ->sum('total_price');

        $totalTicketsSold = Transaction::whereHas('event')
            ->where('status', 'success')
            ->count();

        $recentEvents = Event::withCount('transactions')
            ->latest()
            ->take(5)
            ->get();

        return view('organizer.dashboard', compact(
            'totalEvents',
            'totalRevenue',
            'totalTicketsSold',
            'recentEvents'
        ));
    }
}