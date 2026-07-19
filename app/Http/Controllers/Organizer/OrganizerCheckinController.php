<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Jobs\GenerateCertificateJob;
use App\Models\Event;
use App\Models\Transaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrganizerCheckinController extends Controller
{
    public function index(): View
    {
        return view('organizer.checkin.scan');
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
        // VALIDASI KEPEMILIKAN: pastikan tiket ini milik event yang dikelola
        // organizer yang sedang login.
        // ====================================================================
        // Kenapa pakai Event::find() (bukan $transaction->event langsung)?
        // Karena route ini berada di bawah prefix 'organizer.', Global Scope
        // (OrganizationScope) OTOMATIS aktif untuk setiap query ke model
        // Event di sini - termasuk Event::find(). Efeknya:
        //
        // - Kalau event ini MILIK organizer yang login -> ditemukan normal.
        // - Kalau event ini milik ORGANIZER LAIN (atau dibuat Admin) ->
        //   Global Scope akan membuat query ini TIDAK MENEMUKAN apa-apa
        //   (null), padahal secara teknis baris eventnya ada di database.
        //
        // Jadi organizer TIDAK BISA check-in tiket dari event yang bukan
        // miliknya, walau tahu persis order_id-nya.
        $event = Event::find($transaction->event_id);

        if (! $event) {
            return back()->with('checkin_status', 'error')
                ->with('checkin_message', 'Tiket ini bukan untuk event yang Anda kelola.');
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