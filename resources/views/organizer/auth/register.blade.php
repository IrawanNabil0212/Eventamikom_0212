@extends('layouts.app')
@section('title', 'Daftar Jadi Penyelenggara')
@section('content')
<main class="max-w-2xl mx-auto px-6 py-20">
    <div class="mb-10 text-center">
        <h1 class="text-4xl font-extrabold">Daftar Jadi Penyelenggara</h1>
        <p class="text-slate-500 mt-2">
            Kelola acara Anda sendiri, pantau penjualan tiket, dan bangun reputasi lewat rating & review.
        </p>
    </div>

    @if($errors->any())
    <div class="mb-6 p-4 bg-red-100 text-red-700 rounded-xl">
        <ul class="list-disc list-inside space-y-1">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="bg-white rounded-3xl border border-slate-200 p-8 shadow-sm">
        <form action="{{ route('organizer.register.store') }}" method="POST" class="space-y-6">
            @csrf

            <div>
                <h3 class="font-bold text-lg mb-4 border-b pb-2">Data Organisasi</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Nama Organisasi / Kepanitiaan</label>
                        <input type="text" name="organization_name" placeholder="Contoh: HIMA Teknik Informatika"
                            class="w-full px-4 py-3 border-2 border-slate-100 rounded-xl focus:border-indigo-600 outline-none"
                            required value="{{ old('organization_name') }}">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">No. Telepon/WhatsApp Organisasi</label>
                        <input type="text" name="phone" placeholder="08xxxxxxxxxx"
                            class="w-full px-4 py-3 border-2 border-slate-100 rounded-xl focus:border-indigo-600 outline-none"
                            required value="{{ old('phone') }}">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Deskripsi Singkat (opsional)</label>
                        <textarea name="description" rows="3" placeholder="Ceritakan sedikit tentang organisasi Anda"
                            class="w-full px-4 py-3 border-2 border-slate-100 rounded-xl focus:border-indigo-600 outline-none">{{ old('description') }}</textarea>
                    </div>
                </div>
            </div>

            <div>
                <h3 class="font-bold text-lg mb-4 border-b pb-2">Akun Penanggung Jawab</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Nama Lengkap</label>
                        <input type="text" name="name" placeholder="Nama Anda"
                            class="w-full px-4 py-3 border-2 border-slate-100 rounded-xl focus:border-indigo-600 outline-none"
                            required value="{{ old('name') }}">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Email</label>
                        <input type="email" name="email" placeholder="email@contoh.com"
                            class="w-full px-4 py-3 border-2 border-slate-100 rounded-xl focus:border-indigo-600 outline-none"
                            required value="{{ old('email') }}">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Password</label>
                            <input type="password" name="password"
                                class="w-full px-4 py-3 border-2 border-slate-100 rounded-xl focus:border-indigo-600 outline-none"
                                required>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation"
                                class="w-full px-4 py-3 border-2 border-slate-100 rounded-xl focus:border-indigo-600 outline-none"
                                required>
                        </div>
                    </div>
                </div>
            </div>

            <button type="submit"
                class="w-full py-4 bg-indigo-600 text-white rounded-2xl font-black text-lg hover:bg-indigo-700 transition">
                Daftar Sekarang
            </button>
            <p class="text-center text-xs text-slate-400">
                Pendaftaran akan direview oleh tim kami sebelum akun aktif.
            </p>
        </form>

        <p class="text-center text-sm text-slate-500 mt-6">
            Sudah punya akun penyelenggara?
            <a href="{{ route('organizer.login') }}" class="text-indigo-600 font-bold hover:underline">Masuk di sini</a>
        </p>
    </div>
</main>
@endsection