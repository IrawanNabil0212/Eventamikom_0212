<?php

namespace App\Http\Controllers;

use App\Jobs\GenerateCertificateJob;
use App\Models\Transaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CheckinController extends Controller
{
    /**
     * Tampilkan halaman scan check-in.
     * Panitia tinggal arahkan cursor ke input field, lalu scan QR
     * pakai alat scanner fisik (bekerja seperti keyboard + auto Enter).
     */
    public function index(): View
    {
        return view('checkin.scan');
    }

    /**
     * Proses hasil scan / input order_id.
     * Dipanggil setiap kali panitia scan QR peserta di pintu masuk.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'order_id' => 'required|string',
        ]);

        $transaction = Transaction::where('order_id', $request->order_id)->first();

        // Kasus 1: order_id tidak ditemukan sama sekali
        if (! $transaction) {
            return back()->with('checkin_status', 'error')
                ->with('checkin_message', 'Tiket tidak ditemukan. Order ID tidak valid.');
        }

        // Kasus 2: tiket ditemukan tapi belum lunas (belum bayar)
        if ($transaction->status !== 'Paid' && $transaction->status !== 'Lunas') {
            return back()->with('checkin_status', 'error')
                ->with('checkin_message', "Tiket atas nama {$transaction->customer_name} belum lunas.");
        }

        // Kasus 3: tiket sudah pernah di-check-in sebelumnya (cegah re-entry / tiket ganda)
        if ($transaction->checked_in_at !== null) {
            return back()->with('checkin_status', 'warning')
                ->with('checkin_message', "Tiket atas nama {$transaction->customer_name} SUDAH check-in sebelumnya pada {$transaction->checked_in_at->format('d M Y, H:i')}.");
        }

        // Kasus 4: valid, belum pernah check-in -> catat kehadiran
        $transaction->update(['checked_in_at' => now()]);

        // Trigger otomatis: generate sertifikat + kirim email, dijalankan di background (queue)
        GenerateCertificateJob::dispatch($transaction);

        return back()->with('checkin_status', 'success')
            ->with('checkin_message', "Check-in berhasil! Selamat datang, {$transaction->customer_name}.");
    }
}