<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class BuyerAuthController extends Controller
{
    /**
     * Langkah 1: User klik tombol "Continue with Google".
     * Method ini melempar (redirect) user ke halaman login Google.
     */
    public function redirectToGoogle(Request $request)
    {
        // Simpan URL asal (misal halaman checkout event tertentu),
        // supaya setelah login Google, user diarahkan BALIK ke sana,
        // bukan ke halaman umum. Disimpan di session sementara.
        if ($request->has('redirect_to')) {
            session(['sso_redirect_to' => $request->query('redirect_to')]);
        }

        return Socialite::driver('google')->redirect();
    }

    /**
     * Langkah 2: Google mengarahkan balik ke sini setelah user approve.
     * Di sinilah akun otomatis dibuat (kalau baru) atau di-login-kan
     * (kalau emailnya sudah pernah dipakai daftar).
     */
    public function handleGoogleCallback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect()->route('home')
                ->with('error', 'Login dengan Google gagal, silakan coba lagi.');
        }

        // Cari user berdasarkan google_id ATAU email yang sama.
        // Kenapa dicek dua-duanya? Supaya kalau user PERNAH daftar manual
        // pakai email yang sama (kalau ada form lain), akunnya tetap
        // nyambung ke akun yang sama, bukan bikin duplikat.
        $user = User::where('google_id', $googleUser->getId())
                    ->orWhere('email', $googleUser->getEmail())
                    ->first();

        if ($user) {
            // Sudah pernah ada -> pastikan google_id & avatar ter-update
            $user->update([
                'google_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
            ]);
        } else {
            // Belum ada -> buat akun buyer baru otomatis
            $user = User::create([
                'name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
                'role' => 'buyer',
                'password' => null, // buyer via SSO tidak butuh password
            ]);
        }

        Auth::login($user, remember: true);
        $request->session()->regenerate();

        // Arahkan balik ke halaman asal (misal checkout event tertentu)
        // kalau ada, atau ke homepage kalau tidak ada.
        $redirectTo = session()->pull('sso_redirect_to');

        return $redirectTo
            ? redirect($redirectTo)
            : redirect()->route('home');
    }

    /**
     * Logout untuk buyer.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}