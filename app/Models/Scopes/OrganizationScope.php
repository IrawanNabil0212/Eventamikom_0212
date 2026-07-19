<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class OrganizationScope implements Scope
{
    /**
     * Terapkan filter otomatis: HANYA ketika sedang berada di dalam
     * halaman/route organizer (prefix "organizer.") DAN yang login
     * memang role organizer.
     *
     * KENAPA HARUS CEK ROUTE JUGA (bukan cuma role)?
     * Karena kalau cuma cek role, organizer yang masih login lalu
     * membuka halaman PUBLIK (katalog event untuk buyer) akan ikut
     * ke-filter juga - buyer/pengunjung jadi cuma lihat event dari
     * 1 organizer itu saja, bukan semua event dari semua organizer.
     * Dengan cek nama route, filter ini HANYA aktif di dashboard/CRUD
     * milik organizer sendiri, tidak bocor ke halaman publik.
     */
    public function apply(Builder $builder, Model $model): void
    {
        $isOrganizerArea = Request::route() && Request::route()->getName()
            && str_starts_with(Request::route()->getName(), 'organizer.');

        if ($isOrganizerArea && Auth::check() && Auth::user()->role === 'organizer') {
            $builder->where(
                $model->getTable() . '.organization_id',
                Auth::user()->organization_id
            );
        }
    }
}