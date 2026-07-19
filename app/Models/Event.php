<?php

namespace App\Models;

use App\Models\Scopes\OrganizationScope;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = [
        'category_id',
        'organization_id',
        'title',
        'description',
        'date',
        'location',
        'price',
        'stock',
        'poster_path',
    ];

    /**
     * Daftarkan Global Scope di sini. Efeknya: setiap kali ada
     * yang panggil Event::all(), Event::find(), dst, otomatis
     * ke-filter sesuai organization_id organizer yang login
     * (lihat App\Models\Scopes\OrganizationScope).
     *
     * PENTING untuk halaman publik (buyer browsing katalog event,
     * lihat detail event, dst): karena scope ini HANYA aktif kalau
     * yang login role-nya 'organizer', buyer/guest/admin tetap
     * melihat semua event seperti biasa - tidak perlu ubah apa-apa
     * di controller publik yang sudah ada.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new OrganizationScope);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}