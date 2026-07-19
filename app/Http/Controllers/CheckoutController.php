<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function create(Event $event)
    {
        if (!Auth::check()) {
            return redirect()->route('buyer.google.redirect', [
                'redirect_to' => route('checkout.create', $event->id),
            ]);
        }

        $categories = \App\Models\Category::all();
        return view('checkout.create', compact('event', 'categories'));
    }

    public function store(Request $request, Event $event)
    {
        if (!Auth::check()) {
            return redirect()->route('buyer.google.redirect', [
                'redirect_to' => route('checkout.create', $event->id),
            ]);
        }

        $request->validate([
            'customer_name'  => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|string|max:20',
        ]);

        if ($event->stock <= 0) {
            return back()->with('error', 'Mohon maaf, tiket untuk acara ini sudah habis.');
        }

        // ====================================================================
        // BYPASS TRANSAKSI UNTUK EVENT GRATIS
        // ====================================================================
        // Kalau harga tiket Rp 0, TIDAK PERLU sama sekali lewat Midtrans -
        // tidak ada uang yang perlu dibayar, jadi langsung ditandai sukses,
        // stok langsung dikurangi saat itu juga, dan buyer langsung
        // diarahkan ke halaman sukses (E-Ticket langsung "terbit").
        if ($event->price == 0) {
            return $this->handleFreeEvent($request, $event);
        }

        // ====================================================================
        // EVENT BERBAYAR: alur normal lewat Midtrans seperti biasa
        // ====================================================================
        $orderId    = 'TRX-' . time() . '-' . Str::random(5);
        $totalPrice = $event->price + 5000;

        $transaction = Transaction::create([
            'event_id'       => $event->id,
            'user_id'        => Auth::id(),
            'order_id'       => $orderId,
            'customer_name'  => $request->customer_name,
            'customer_email' => $request->customer_email,
            'customer_phone' => $request->customer_phone,
            'total_price'    => $totalPrice,
            'status'         => 'Pending',
        ]);

        \Midtrans\Config::$serverKey    = env('MIDTRANS_SERVER_KEY');
        \Midtrans\Config::$isProduction = false;
        \Midtrans\Config::$isSanitized  = true;
        \Midtrans\Config::$is3ds        = true;

        $params = [
            'transaction_details' => [
                'order_id'     => $orderId,
                'gross_amount' => $totalPrice,
            ],
            'customer_details' => [
                'first_name' => $request->customer_name,
                'email'      => $request->customer_email,
                'phone'      => $request->customer_phone,
            ],
        ];

        try {
            $snapToken = \Midtrans\Snap::getSnapToken($params);
            $transaction->update(['snap_token' => $snapToken]);

            return redirect()->route('checkout.payment', $transaction->order_id);

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memproses pembayaran: ' . $e->getMessage());
        }
    }

    /**
     * Alur khusus event gratis: skip Midtrans total, langsung sukses.
     *
     * Tetap pakai DB::transaction() + lockForUpdate() supaya aman kalau
     * ada 2 orang klaim tiket gratis TERAKHIR di detik yang sama - tanpa
     * ini, keduanya bisa sama-sama "lolos" cek stok sebelum salah satu
     * sempat mengurangi duluan (overselling), sama seperti prinsip race
     * condition yang perlu dijaga di alur berbayar.
     */
    private function handleFreeEvent(Request $request, Event $event)
    {
        $transaction = null;
        $errorMessage = null;

        DB::transaction(function () use ($event, $request, &$transaction, &$errorMessage) {
            $lockedEvent = Event::where('id', $event->id)->lockForUpdate()->first();

            if (!$lockedEvent || $lockedEvent->stock <= 0) {
                $errorMessage = 'Mohon maaf, tiket gratis untuk acara ini sudah habis.';
                return;
            }

            $orderId = 'TRX-' . time() . '-' . Str::random(5);

            $transaction = Transaction::create([
                'event_id'       => $lockedEvent->id,
                'user_id'        => Auth::id(),
                'order_id'       => $orderId,
                'customer_name'  => $request->customer_name,
                'customer_email' => $request->customer_email,
                'customer_phone' => $request->customer_phone,
                'total_price'    => 0,
                'status'         => 'success', // langsung sukses, tidak ada pembayaran yang ditunggu
            ]);

            // Stok dikurangi SAAT ITU JUGA, bukan lewat webhook/Midtrans
            $lockedEvent->decrement('stock');
        });

        if ($errorMessage) {
            return back()->with('error', $errorMessage);
        }

        return redirect()->route('checkout.success', $transaction->order_id)
            ->with('success', 'Tiket gratis berhasil diklaim!');
    }

    public function payment($order_id)
    {
        $categories  = \App\Models\Category::all();
        $transaction = Transaction::with('event')->where('order_id', $order_id)->firstOrFail();
        return view('checkout.payment', compact('transaction', 'categories'));
    }

    public function success($order_id)
    {
        $categories  = \App\Models\Category::all();
        $transaction = Transaction::where('order_id', $order_id)->firstOrFail();

        // Event gratis sudah 'success' sejak dibuat di handleFreeEvent(),
        // tidak perlu cek status ke Midtrans sama sekali.
        if ($transaction->total_price == 0) {
            return view('checkout.success', compact('transaction', 'categories'));
        }

        \Midtrans\Config::$serverKey    = env('MIDTRANS_SERVER_KEY');
        \Midtrans\Config::$isProduction = false;

        try {
            $midtransStatus = \Midtrans\Transaction::status($order_id);

            if (is_array($midtransStatus)) {
                $midtransStatus = (object) $midtransStatus;
            }

            if (in_array($midtransStatus->transaction_status, ['capture', 'settlement'])) {
                if ($transaction->status !== 'success') {
                    $transaction->update(['status' => 'success']);

                    if ($transaction->event) {
                        $transaction->event()->decrement('stock');
                    }
                }
            }

        } catch (\Exception $e) {
            return redirect()->route('home')->with('error', 'Transaksi tidak ditemukan atau gagal diproses oleh sistem pembayaran.');
        }

        return view('checkout.success', compact('transaction', 'categories'));
    }
}