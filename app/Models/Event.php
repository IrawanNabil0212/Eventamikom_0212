<?php

namespace App\Models;

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

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Event ini milik organizer/tenant yang mana.
     * (Global Scope untuk isolasi data otomatis akan ditambahkan
     * di Tahap 4, supaya tidak ganggu dulu proses migrasi data lama.)
     */
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