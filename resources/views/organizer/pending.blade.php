@extends('layouts.app')
@section('title', 'Status Pendaftaran')
@section('content')
<main class="max-w-lg mx-auto px-6 py-24 text-center">

    @if(session('success'))
    <div class="mb-6 p-4 bg-green-100 text-green-700 rounded-xl">
        {{ session('success') }}
    </div>
    @endif

    @if($organization && $organization->status === 'rejected')
        <div class="w-20 h-20 bg-red-100 text-red-500 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l18 18"></path>
            </svg>
        </div>
        <h1 class="text-2xl font-extrabold mb-2">Pendaftaran Ditolak</h1>
        <p class="text-slate-500 mb-4">
            Mohon maaf, pendaftaran organisasi <strong>{{ $organization->name }}</strong> belum bisa kami setujui.
        </p>
        @if($organization->rejection_reason)
        <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 text-left text-sm text-slate-600">
            <span class="font-bold block mb-1">Alasan:</span>
            {{ $organization->rejection_reason }}
        </div>
        @endif
    @else
        <div class="w-20 h-20 bg-yellow-100 text-yellow-500 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
        <h1 class="text-2xl font-extrabold mb-2">Menunggu Persetujuan</h1>
        <p class="text-slate-500">
            Pendaftaran organisasi <strong>{{ $organization->name ?? '' }}</strong> sedang ditinjau oleh tim kami.
            Anda akan bisa mengelola acara setelah akun disetujui.
        </p>
    @endif

    <form method="POST" action="{{ route('organizer.logout') }}" class="mt-8">
        @csrf
        <button type="submit" class="text-sm text-slate-500 hover:underline">Logout</button>
    </form>
</main>
@endsection