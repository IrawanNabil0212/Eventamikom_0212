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
        'expires_at',
        'checked_in_at',
        'certificate_path',
        'certificate_sent_at',
        'snap_token',
    ];

    /**
     * PENTING: tanpa $casts ini, kolom timestamp seperti expires_at dan
     * checked_in_at akan tersimpan sebagai TEKS biasa dari database,
     * BUKAN objek Carbon - akibatnya method seperti ->format(), ->gt(),
     * ->addMinutes() akan ERROR ("Call to a member function ... on
     * string") begitu dipanggil.
     */
    protected $casts = [
        'expires_at' => 'datetime',
        'checked_in_at' => 'datetime',
        'certificate_sent_at' => 'datetime',
    ];

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
}