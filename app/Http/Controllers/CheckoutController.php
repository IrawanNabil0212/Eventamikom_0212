<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    public function create(Event $event)
    {
        // ============================================================
        // GUARD: Wajib login (via Google SSO) sebelum bisa checkout.
        // Kalau belum login, langsung lempar ke Google, dan setelah
        // berhasil login akan diarahkan BALIK ke halaman checkout ini
        // (lihat `redirect_to` yang ditangkap BuyerAuthController).
        // ============================================================
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
        // Guard yang sama juga dipasang di store(), untuk jaga-jaga kalau
        // ada yang coba submit form langsung tanpa lewat create() dulu
        // (misal lewat Postman/cURL manual).
        if (!Auth::check()) {
            return redirect()->route('buyer.google.redirect', [
                'redirect_to' => route('checkout.create', $event->id),
            ]);
        }

        // 1. Validasi Input Kredensial Pelanggan
        $request->validate([
            'customer_name'  => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|string|max:20',
        ]);

        // 2. Cegah Check-out Jika Tiket Habis
        if ($event->stock <= 0) {
            return back()->with('error', 'Mohon maaf, tiket untuk acara ini sudah habis.');
        }

        // 3. Generate Kode TRX (Unik)
        $orderId    = 'TRX-' . time() . '-' . Str::random(5);
        $totalPrice = $event->price + 5000;

        // 4. Merekam Transaksi ke Database
        //    `user_id` diisi otomatis dari buyer yang sedang login (SSO).
        //    Ini KUNCI supaya nanti di Tahap 5 (Rating & Review) sistem
        //    tahu persis siapa yang berhak kasih review untuk event ini.
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

        // --- INTEGRASI SNAP MIDTRANS ---

        // Konfigurasi Kredensial Environment Midtrans
        \Midtrans\Config::$serverKey    = env('MIDTRANS_SERVER_KEY');
        \Midtrans\Config::$isProduction = false; // Mode Sandbox!
        \Midtrans\Config::$isSanitized  = true;
        \Midtrans\Config::$is3ds        = true;

        // Susun Paket Array Data Transaksi
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
            // Generate Snap Token dari Midtrans
            $snapToken = \Midtrans\Snap::getSnapToken($params);

            // Simpan snap_token ke transaksi yang sudah dibuat
            $transaction->update(['snap_token' => $snapToken]);

            // Redirect ke halaman pembayaran
            return redirect()->route('checkout.payment', $transaction->order_id);

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memproses pembayaran: ' . $e->getMessage());
        }
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

        // Validasi status pembayaran asli dari Midtrans (Mencegah manipulasi URL)
        \Midtrans\Config::$serverKey    = env('MIDTRANS_SERVER_KEY');
        \Midtrans\Config::$isProduction = false;

        try {
            // Cek status transaksi langsung ke server Midtrans
            $midtransStatus = \Midtrans\Transaction::status($order_id);

            // Cast ke object jika Midtrans mengembalikan array
            if (is_array($midtransStatus)) {
                $midtransStatus = (object) $midtransStatus;
            }

            // Hanya ubah status jika Midtrans konfirmasi pembayaran lunas
            if (in_array($midtransStatus->transaction_status, ['capture', 'settlement'])) {
                $transaction->update(['status' => 'success']);
            }

        } catch (\Exception $e) {
            return redirect()->route('home')->with('error', 'Transaksi tidak ditemukan atau gagal diproses oleh sistem pembayaran.');
        }

        return view('checkout.success', compact('transaction', 'categories'));
    }
}