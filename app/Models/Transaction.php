<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'event_id',
        'user_id',
        'order_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'total_price',
        'status',
        'expires_at',
        'checked_in_at',
        'certificate_path',
        'certificate_sent_at',
        'snap_token',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'checked_in_at' => 'datetime',
        'certificate_sent_at' => 'datetime',
    ];

    /**
     * PENTING: supaya $transaction->is_reviewable bisa langsung dipanggil
     * di Blade (ticket.blade.php) tanpa perlu di-load manual di controller.
     */
    protected $appends = ['is_reviewable'];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function review()
    {
        return $this->hasOne(Review::class);
    }

    /**
     * Cek apakah transaksi ini SUDAH BOLEH diberi ulasan.
     * Syarat (semua harus terpenuhi):
     * 1. Status transaksi 'success' (sudah lunas).
     * 2. Event-nya masih ada (tidak dihapus).
     * 3. Event sudah lewat jeda waktu Review::REVIEW_DELAY_MINUTES
     *    (H+1 sejak tanggal event, sesuai spesifikasi).
     * 4. Belum pernah direview sebelumnya.
     */
    public function getIsReviewableAttribute(): bool
    {
        if ($this->status !== 'success') {
            return false;
        }

        if (!$this->event) {
            return false;
        }

        // Kalau sudah pernah direview, tidak perlu tampilkan form lagi
        // (biar tidak query berulang, cek relasi yang sudah di-load dulu
        // kalau ada; kalau belum di-load, baru query exists()).
        $alreadyReviewed = $this->relationLoaded('review')
            ? $this->review !== null
            : $this->review()->exists();

        if ($alreadyReviewed) {
            return false;
        }

        $eligibleAt = Carbon::parse($this->event->date)
            ->addMinutes(Review::REVIEW_DELAY_MINUTES);

        return now()->gte($eligibleAt);
    }
}