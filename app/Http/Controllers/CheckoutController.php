<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
  
    private const RESERVATION_MINUTES = 15;

    public function create(Event $event)
    {
        if (!Auth::check()) {
            return redirect()->route('buyer.google.redirect', [
                'redirect_to' => route('checkout.create', $event->id),
            ]);
        }

        if (Carbon::parse($event->date)->isPast()) {
            return redirect()->route('home')
                ->with('error', 'Mohon maaf, acara ini sudah berlalu dan tidak bisa dipesan lagi.');
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

    
        if (Carbon::parse($event->date)->isPast()) {
            return back()->with('error', 'Mohon maaf, acara ini sudah berlalu dan tidak bisa dipesan lagi.');
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
        if ($event->price == 0) {
            return $this->handleFreeEvent($request, $event);
        }

        $transaction = null;
        $errorMessage = null;

        DB::transaction(function () use ($event, $request, &$transaction, &$errorMessage) {
            $lockedEvent = Event::where('id', $event->id)->lockForUpdate()->first();

            if (!$lockedEvent || $lockedEvent->stock <= 0) {
                $errorMessage = 'Mohon maaf, tiket untuk acara ini sudah habis.';
                return;
            }

            $orderId    = 'TRX-' . time() . '-' . Str::random(5);
            $totalPrice = $lockedEvent->price + 5000;

            $transaction = Transaction::create([
                'event_id'       => $lockedEvent->id,
                'user_id'        => Auth::id(),
                'order_id'       => $orderId,
                'customer_name'  => $request->customer_name,
                'customer_email' => $request->customer_email,
                'customer_phone' => $request->customer_phone,
                'total_price'    => $totalPrice,
                'status'         => 'Pending',
                'expires_at'     => now()->addMinutes(self::RESERVATION_MINUTES),
            ]);

            // Stok LANGSUNG dikurangi di sini (reserve), BUKAN menunggu
            // pembayaran lunas dulu - inilah inti dari "Reserved Ticket".
            $lockedEvent->decrement('stock');
        });

        if ($errorMessage) {
            return back()->with('error', $errorMessage);
        }

        \Midtrans\Config::$serverKey    = env('MIDTRANS_SERVER_KEY');
        \Midtrans\Config::$isProduction = false;
        \Midtrans\Config::$isSanitized  = true;
        \Midtrans\Config::$is3ds        = true;

        $params = [
            'transaction_details' => [
                'order_id'     => $transaction->order_id,
                'gross_amount' => $transaction->total_price,
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
            // PENTING: kalau gagal generate Snap Token, WAJIB lepas lagi
            // reservasi stoknya - jangan sampai stok "hilang" padahal
            // transaksinya gagal dibuat sama sekali.
            $transaction->event()->increment('stock');
            $transaction->update(['status' => 'failed']);

            return back()->with('error', 'Gagal memproses pembayaran: ' . $e->getMessage());
        }
    }

    /**
     * Alur khusus event gratis: skip Midtrans total, langsung sukses.
     * Tetap pakai lockForUpdate() untuk cegah race condition di tiket
     * gratis terakhir.
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
                'status'         => 'success',
            ]);

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

        // Kalau ternyata sudah kadaluarsa (misal user buka tab lama yang
        // sudah lewat 15 menit), jangan tampilkan halaman bayar lagi.
        if ($transaction->status === 'Pending' && $transaction->expires_at && now()->gt($transaction->expires_at)) {
            return redirect()->route('home')
                ->with('error', 'Waktu pembayaran sudah habis, silakan pesan ulang.');
        }

        return view('checkout.payment', compact('transaction', 'categories'));
    }

    public function success($order_id)
    {
        $categories  = \App\Models\Category::all();
        $transaction = Transaction::where('order_id', $order_id)->firstOrFail();

        // Event gratis sudah 'success' sejak dibuat, tidak perlu cek
        // Midtrans sama sekali.
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

            // PENTING: stok TIDAK dikurangi lagi di sini - sudah dikurangi
            // (di-reserve) sejak checkout awal di store(). Di sini cukup
            // update status transaksinya saja jadi 'success' final, dan
            // hapus expires_at (karena sudah tidak relevan lagi).
            if (in_array($midtransStatus->transaction_status, ['capture', 'settlement'])) {
                if ($transaction->status !== 'success') {
                    $transaction->update(['status' => 'success', 'expires_at' => null]);
                }
            }

        } catch (\Exception $e) {
            return redirect()->route('home')->with('error', 'Transaksi tidak ditemukan atau gagal diproses oleh sistem pembayaran.');
        }

        return view('checkout.success', compact('transaction', 'categories'));
    }

    /**
     * Webhook Payment Notification dari Midtrans.
     *
     * Dipanggil otomatis oleh SERVER Midtrans (bukan oleh browser user)
     * setiap kali status transaksi berubah - termasuk saat pembayaran
     * dibatalkan (cancel), kadaluarsa dari sisi Midtrans (expire), atau
     * ditolak (deny). Tanpa ini, stok yang sudah di-reserve hanya akan
     * kembali lewat cron tickets:release-expired (menunggu penuh 15 menit),
     * bukan seketika saat user membatalkan pembayaran di popup Snap.
     *
     * PENTING: route ini dikecualikan dari CSRF protection di
     * bootstrap/app.php, karena request datang dari server Midtrans,
     * bukan dari form/browser yang punya CSRF token.
     */
    public function notification(Request $request)
    {
        \Midtrans\Config::$serverKey    = env('MIDTRANS_SERVER_KEY');
        \Midtrans\Config::$isProduction = false;

        try {
            $notif = new \Midtrans\Notification();
        } catch (\Exception $e) {
            return response()->json(['message' => 'Notifikasi tidak valid: ' . $e->getMessage()], 400);
        }

        $transaction = Transaction::where('order_id', $notif->order_id)->first();

        if (!$transaction) {
            return response()->json(['message' => 'Transaksi tidak ditemukan'], 404);
        }

        // Kalau status transaksi sudah final (bukan 'Pending' lagi), jangan
        // diproses ulang - mencegah stok bertambah dobel kalau Midtrans
        // mengirim notifikasi yang sama lebih dari sekali.
        if ($transaction->status !== 'Pending') {
            return response()->json(['message' => 'Transaksi sudah diproses sebelumnya, notifikasi diabaikan.']);
        }

        $status = $notif->transaction_status;

        if (in_array($status, ['cancel', 'expire', 'deny'])) {
            DB::transaction(function () use ($transaction, $status) {
                $transaction->update([
                    'status'     => $status,
                    'expires_at' => null,
                ]);

                if ($transaction->event) {
                    $transaction->event()->increment('stock');
                }
            });

        } elseif (in_array($status, ['capture', 'settlement'])) {
            $transaction->update([
                'status'     => 'success',
                'expires_at' => null,
            ]);
        }

        return response()->json(['message' => 'Notifikasi berhasil diproses']);
    }
}