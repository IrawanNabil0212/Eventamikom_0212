<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\GenerateCertificateJob;
use App\Models\Event;
use App\Models\Transaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminCheckinController extends Controller
{
    public function index(): View
    {
        return view('admin.checkin.scan');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'order_id' => 'required|string',
        ]);

        $transaction = Transaction::where('order_id', $request->order_id)->first();

        if (! $transaction) {
            return back()->with('checkin_status', 'error')
                ->with('checkin_message', 'Tiket tidak ditemukan. Order ID tidak valid.');
        }

        // ====================================================================
        // VALIDASI KEPEMILIKAN: halaman check-in Admin ini KHUSUS untuk
        // event yang dibuat langsung oleh Admin (organization_id NULL),
        // BUKAN untuk event milik organizer manapun - supaya sejalan
        // dengan prinsip "cuma pemilik event yang bisa check-in tiketnya".
        //
        // Kalau ingin Superadmin bisa override/check-in SEMUA event
        // (termasuk milik organizer), tinggal hapus blok pengecekan ini -
        // tapi defaultnya dibuat ketat dulu sesuai isolasi multi-tenant.
        // ====================================================================
        $event = Event::find($transaction->event_id);

        if (! $event || $event->organization_id !== null) {
            return back()->with('checkin_status', 'error')
                ->with('checkin_message', 'Tiket ini milik event yang dikelola organizer, silakan check-in lewat panel organizer terkait.');
        }

        if ($transaction->status !== 'success') {
            return back()->with('checkin_status', 'error')
                ->with('checkin_message', "Tiket atas nama {$transaction->customer_name} belum lunas.");
        }

        if ($transaction->checked_in_at !== null) {
            return back()->with('checkin_status', 'warning')
                ->with('checkin_message', "Tiket atas nama {$transaction->customer_name} SUDAH check-in sebelumnya pada {$transaction->checked_in_at->format('d M Y, H:i')}.");
        }

        $transaction->update(['checked_in_at' => now()]);

        GenerateCertificateJob::dispatch($transaction);

        return back()->with('checkin_status', 'success')
            ->with('checkin_message', "Check-in berhasil! Selamat datang, {$transaction->customer_name}.");
    }
}