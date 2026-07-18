<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'google_id',
        'avatar',
        'organization_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Organisasi tempat user ini menjadi anggota/pengelola.
     * (Diisi kalau role-nya 'organizer'.)
     */
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Organisasi yang dia daftarkan sebagai pemilik/pendaftar pertama.
     */
    public function ownedOrganization()
    {
        return $this->hasOne(Organization::class, 'owner_id');
    }

    /**
     * Transaksi/tiket yang pernah dibeli user ini (sebagai buyer).
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Review yang pernah ditulis user ini.
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Role Helper Methods
    |--------------------------------------------------------------------------
    | Supaya di controller/blade tidak perlu tulis `$user->role === 'admin'`
    | berulang-ulang, cukup panggil $user->isAdmin(), dst.
    */

    /**
     * PENTING: role 'admin' yang SUDAH ADA di project Anda kita jadikan
     * setara dengan "Superadmin" di arsitektur baru (yang mengawasi semua
     * organizer). Jadi tidak perlu bikin role string baru 'superadmin' -
     * cukup pakai 'admin' yang sudah ada supaya AdminMiddleware lama
     * tetap jalan tanpa diubah sama sekali.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    // Alias, supaya kode di controller lain lebih mudah dibaca
    // maksudnya ("superadmin platform"), meski nilainya sama dengan isAdmin().
    public function isSuperadmin(): bool
    {
        return $this->isAdmin();
    }

    public function isOrganizer(): bool
    {
        return $this->role === 'organizer';
    }

    public function isBuyer(): bool
    {
        return $this->role === 'buyer';
    }
}