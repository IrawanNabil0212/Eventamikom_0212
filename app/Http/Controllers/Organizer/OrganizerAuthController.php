<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class OrganizerAuthController extends Controller
{
    /**
     * Tampilkan form "Daftar Jadi Penyelenggara".
     */
    public function showRegister()
    {
        return view('organizer.auth.register');
    }

    /**
     * Proses registrasi organizer baru.
     *
     * Satu submit form ini melakukan 2 hal sekaligus:
     * 1. Bikin akun User baru dengan role 'organizer'
     * 2. Bikin data Organization baru dengan status 'pending'
     *
     * Organizer TIDAK bisa langsung masuk ke dashboard setelah daftar -
     * harus menunggu Superadmin approve dulu (dicek oleh OrganizerMiddleware).
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'organization_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        // Bikin slug unik dari nama organisasi, untuk URL profil publik nanti
        $baseSlug = Str::slug($request->organization_name);
        $slug = $baseSlug;
        $counter = 1;
        while (Organization::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter++;
        }

        $organization = Organization::create([
            'name' => $request->organization_name,
            'slug' => $slug,
            'description' => $request->description,
            'phone' => $request->phone,
            'status' => 'pending',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'organizer',
            'organization_id' => $organization->id,
        ]);

        // Jadikan user ini sebagai owner/pendaftar dari organisasi tadi
        $organization->update(['owner_id' => $user->id]);

        Auth::login($user);

        return redirect()->route('organizer.pending')
            ->with('success', 'Pendaftaran berhasil! Akun Anda akan aktif setelah disetujui Superadmin.');
    }

    /**
     * Halaman login untuk organizer (manual, email + password).
     */
    public function showLogin()
    {
        return view('organizer.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Pastikan yang login memang role organizer, bukan buyer/admin
            // yang kebetulan submit form ini.
            if ($user->role !== 'organizer') {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Akun ini bukan akun penyelenggara.',
                ]);
            }

            $request->session()->regenerate();

            if (!$user->organization || !$user->organization->isApproved()) {
                return redirect()->route('organizer.pending');
            }

            return redirect()->route('organizer.dashboard');
        }

        return back()->withErrors([
            'email' => 'Email atau Password yang Anda berikan tidak terdaftar di database kami.',
        ]);
    }

    /**
     * Halaman status "Menunggu Persetujuan" / "Ditolak".
     * Diarahkan ke sini kalau organisasi belum di-approve Superadmin.
     */
    public function pending()
    {
        $organization = Auth::user()->organization;

        return view('organizer.pending', compact('organization'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('organizer.login');
    }
}