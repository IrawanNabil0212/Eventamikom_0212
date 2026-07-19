@extends('layouts.app')
@section('title', 'Login Penyelenggara')
@section('content')
<main class="max-w-md mx-auto px-6 py-20">
    <div class="mb-10 text-center">
        <h1 class="text-3xl font-extrabold">Login Penyelenggara</h1>
        <p class="text-slate-500 mt-2">Masuk untuk mengelola acara Anda.</p>
    </div>

    @if($errors->any())
    <div class="mb-6 p-4 bg-red-100 text-red-700 rounded-xl">
        {{ $errors->first() }}
    </div>
    @endif

    @if(session('success'))
    <div class="mb-6 p-4 bg-green-100 text-green-700 rounded-xl">
        {{ session('success') }}
    </div>
    @endif

    <div class="bg-white rounded-3xl border border-slate-200 p-8 shadow-sm">
        <form action="{{ route('organizer.login.post') }}" method="POST" class="space-y-5">
            @csrf
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Email</label>
                <input type="email" name="email"
                    class="w-full px-4 py-3 border-2 border-slate-100 rounded-xl focus:border-indigo-600 outline-none"
                    required value="{{ old('email') }}">
            </div>
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Password</label>
                <input type="password" name="password"
                    class="w-full px-4 py-3 border-2 border-slate-100 rounded-xl focus:border-indigo-600 outline-none"
                    required>
            </div>
            <button type="submit"
                class="w-full py-4 bg-indigo-600 text-white rounded-2xl font-black text-lg hover:bg-indigo-700 transition">
                Masuk
            </button>
        </form>

        <p class="text-center text-sm text-slate-500 mt-6">
            Belum punya akun penyelenggara?
            <a href="{{ route('organizer.register') }}" class="text-indigo-600 font-bold hover:underline">Daftar di sini</a>
        </p>
    </div>
</main>
@endsection