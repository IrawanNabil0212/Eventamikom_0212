<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * Simpan review baru untuk sebuah transaksi.
     *
     * Validasi berlapis di sini PENTING supaya rating & review tetap
     * kredibel/jujur - bukan cuma "modal isi form", tapi wajib benar-benar
     * ikut acaranya:
     *
     * 1. Transaksi harus milik user yang sedang login (bukan transaksi
     *    orang lain - dicegah manipulasi ID lewat URL).
     * 2. Status transaksi harus 'success' (benar-benar bayar & dapat tiket).
     * 3. Event harus SUDAH LEWAT H+1 (baru boleh kasih ulasan sehari
     *    setelah acara selesai, sesuai spesifikasi tugas).
     * 4. Transaksi ini belum pernah direview sebelumnya (1 tiket = 1 review).
     */
    public function store(Request $request, Transaction $transaction)
    {
        // 1. Wajib pemilik transaksi ini sendiri
        if ($transaction->user_id !== Auth::id()) {
            abort(403, 'Anda tidak berhak memberi ulasan untuk transaksi ini.');
        }

        // 2. Wajib transaksi sukses/lunas
        if ($transaction->status !== 'success') {
            return back()->with('error', 'Ulasan hanya bisa diberikan untuk tiket yang sudah lunas.');
        }

        // 3. Wajib event sudah lewat jeda waktu yang ditentukan
        //    (lihat Review::REVIEW_DELAY_MINUTES untuk ganti durasinya)
        $eventDate = Carbon::parse($transaction->event->date);
        if (now()->lt($eventDate->copy()->addMinutes(Review::REVIEW_DELAY_MINUTES))) {
            return back()->with('error', 'Ulasan belum bisa diberikan untuk event ini.');
        }

        // 4. Wajib belum pernah direview (jaga-jaga di level aplikasi;
        //    constraint unique di database jadi lapisan kedua)
        if ($transaction->review()->exists()) {
            return back()->with('error', 'Anda sudah memberi ulasan untuk tiket ini.');
        }

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        Review::create([
            'event_id' => $transaction->event_id,
            'user_id' => Auth::id(),
            'transaction_id' => $transaction->id,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'] ?? null,
        ]);

        return back()->with('success', 'Terima kasih atas ulasan Anda!');
    }
}