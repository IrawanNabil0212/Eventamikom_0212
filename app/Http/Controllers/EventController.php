<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Review;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    public function show($id)
    {
        $event = Event::with(['category', 'organization'])->findOrFail($id);

        // Data rating & review untuk ditampilkan di halaman detail event
        $averageRating = $event->reviews()->avg('rating');
        $reviewCount = $event->reviews()->count();
        $reviews = $event->reviews()->with('user')->latest()->take(10)->get();

        return view('events.show', compact('event', 'averageRating', 'reviewCount', 'reviews'));
    }

    /**
     * Halaman "Tiket Saya" - list semua transaksi milik buyer yang
     * sedang login, lengkap dengan status apakah tiket itu sudah
     * bisa/sudah direview.
     */
    public function ticket()
    {
        // Wajib login (SSO Google) untuk lihat tiket sendiri
        if (!Auth::check()) {
            return redirect()->route('buyer.google.redirect', [
                'redirect_to' => route('tickets.my'),
            ]);
        }

        $transactions = Transaction::with(['event', 'review'])
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        // Untuk masing-masing transaksi, tandai apakah sudah "eligible"
        // untuk direview (event sudah lewat H+1 & status sukses)
        $transactions->getCollection()->transform(function ($transaction) {
            $transaction->is_reviewable =
                $transaction->status === 'success'
                && $transaction->event
                && now()->gte(Carbon::parse($transaction->event->date)->addMinutes(Review::REVIEW_DELAY_MINUTES))
                && !$transaction->review;

            return $transaction;
        });

        return view('ticket', compact('transactions'));
    }

    public function checkout()
    {
        return view('checkout');
    }
}