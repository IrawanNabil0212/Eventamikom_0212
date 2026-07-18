<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class OrganizerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * Selain cek role === 'organizer', kita juga cek status organisasinya
     * harus 'approved'. Kalau masih 'pending' atau 'rejected', organizer
     * tidak boleh masuk ke dashboard (nanti diarahkan ke halaman status
     * pendaftaran di Tahap 3).
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (Auth::check() && $user->role === 'organizer') {

            if (!$user->organization || !$user->organization->isApproved()) {
                return redirect()->route('organizer.pending')
                    ->with('error', 'Akun organisasi Anda belum disetujui oleh Superadmin.');
            }

            return $next($request);
        }

        return redirect('/')->with('error', 'Anda tidak memiliki akses ke halaman ini.');
    }
}