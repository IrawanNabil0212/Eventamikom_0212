<?php

namespace App\Models;

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
        'snap_token',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Buyer yang login (via Google SSO) yang melakukan transaksi ini.
     * Nullable karena transaksi lama sebelum SSO ada tidak punya ini.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Review yang sudah dibuat untuk transaksi ini (kalau ada).
     * Dipakai untuk cek "apakah tiket ini sudah direview?" di halaman
     * Tiket Saya, tanpa perlu query terpisah.
     */
    public function review()
    {
        return $this->hasOne(Review::class);
    }
}