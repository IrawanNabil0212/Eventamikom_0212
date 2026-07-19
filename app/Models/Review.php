<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    /**
     * ====================================================================
     * JEDA WAKTU SEBELUM BOLEH MEMBERI REVIEW (dalam menit).
     * ====================================================================
     * Ini SATU-SATUNYA tempat yang perlu diubah untuk switch antara
     * mode testing dan mode production:
     *
     * - MODE TESTING (sekarang): 1 menit, supaya cepat dites tanpa
     *   perlu nunggu beneran H+1.
     * - MODE PRODUCTION (sesuai spesifikasi tugas "review 1 hari
     *   setelah acara tuntas"): ganti jadi 1440 (24 jam x 60 menit).
     *
     * Dipakai di ReviewController (validasi submit) dan EventController
     * (tampilkan/sembunyikan form review di halaman Tiket Saya) - jadi
     * cukup ganti angka ini saja, tidak perlu ubah kode di 2 tempat itu.
     */
    public const REVIEW_DELAY_MINUTES = 1440;
    protected $fillable = [
        'event_id',
        'user_id',
        'transaction_id',
        'rating',
        'comment',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}