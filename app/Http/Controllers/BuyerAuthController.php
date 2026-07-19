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
        if ($request->has('redirect_to')) {
            session(['sso_redirect_to' => $request->query('redirect_to')]);
        }

        // PENTING: 'prompt' => 'select_account' memaksa Google SELALU
        // menampilkan halaman pilih akun, meski browser masih ada sesi
        // Google yang aktif. Tanpa ini, Google akan otomatis pakai akun
        // terakhir yang dipakai di browser tanpa nanya lagi - bikin user
        // sulit ganti akun tanpa logout total dari Google dulu.
        return Socialite::driver('google')
            ->with(['prompt' => 'select_account'])
            ->redirect();
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

        $user = User::where('google_id', $googleUser->getId())
                    ->orWhere('email', $googleUser->getEmail())
                    ->first();

        if ($user) {
            $user->update([
                'google_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
            ]);
        } else {
            $user = User::create([
                'name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'google_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
                'role' => 'buyer',
                'password' => null,
            ]);
        }

        Auth::login($user, remember: true);
        $request->session()->regenerate();

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