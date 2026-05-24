@extends('layouts.admin', ['title' => 'Dashboard'])

@section('content')

{{-- Header --}}
<header class="flex justify-between items-center mb-10">
    <div>
        <h1 class="text-3xl font-black text-slate-800">Dashboard</h1>
        <p class="text-slate-500 font-medium">Selamat datang kembali, Admin!</p>
    </div>
    <div class="flex items-center gap-4">
        <div class="text-right hidden md:block">
            <p class="font-bold">Admin</p>
            <p class="text-xs text-slate-400">AmikomEventHub</p>
        </div>
        <div class="w-12 h-12 bg-white rounded-2xl shadow-sm border flex items-center justify-center p-1">
            <img src="https://ui-avatars.com/api/?name=Admin&background=6366f1&color=fff" class="rounded-xl">
        </div>
    </div>
</header>

{{-- Statistik Cards --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">

    {{-- Total Event --}}
    <a href="{{ route('admin.events.index') }}"
       class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm hover:shadow-md hover:border-indigo-100 transition group">
        <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center mb-4 group-hover:bg-indigo-100 transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
        </div>
        <p class="text-slate-400 text-sm font-bold uppercase mb-1">Total Event</p>
        <h3 class="text-2xl font-black text-slate-800">{{ $totalEvents }} <span class="text-base font-semibold text-slate-400">event</span></h3>
    </a>

    {{-- Total Kategori --}}
    <a href="{{ route('admin.categories.index') }}"
       class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm hover:shadow-md hover:border-green-100 transition group">
        <div class="w-12 h-12 bg-green-50 text-green-600 rounded-2xl flex items-center justify-center mb-4 group-hover:bg-green-100 transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
            </svg>
        </div>
        <p class="text-slate-400 text-sm font-bold uppercase mb-1">Total Kategori</p>
        <h3 class="text-2xl font-black text-slate-800">{{ $totalCategories }} <span class="text-base font-semibold text-slate-400">kategori</span></h3>
    </a>

    {{-- Total Partner --}}
    <a href="{{ route('admin.partners.index') }}"
       class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm hover:shadow-md hover:border-orange-100 transition group">
        <div class="w-12 h-12 bg-orange-50 text-orange-600 rounded-2xl flex items-center justify-center mb-4 group-hover:bg-orange-100 transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
        </div>
        <p class="text-slate-400 text-sm font-bold uppercase mb-1">Total Partner</p>
        <h3 class="text-2xl font-black text-slate-800">{{ $totalPartners }} <span class="text-base font-semibold text-slate-400">partner</span></h3>
    </a>

    {{-- Total Stok Tiket --}}
    <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm">
        <div class="w-12 h-12 bg-rose-50 text-rose-600 rounded-2xl flex items-center justify-center mb-4">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
            </svg>
        </div>
        <p class="text-slate-400 text-sm font-bold uppercase mb-1">Total Stok Tiket</p>
        <h3 class="text-2xl font-black text-slate-800">{{ number_format($totalStock, 0, ',', '.') }} <span class="text-base font-semibold text-slate-400">tiket</span></h3>
    </div>

</div>

{{-- Konten Bawah: 2 Kolom --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Event Terbaru --}}
    <div class="lg:col-span-2 bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-50 flex justify-between items-center">
            <h3 class="font-black text-lg text-slate-800">Event Terbaru</h3>
            <a href="{{ route('admin.events.index') }}" class="text-indigo-600 font-bold text-sm hover:underline">Lihat Semua →</a>
        </div>
        <div class="divide-y divide-slate-50">
            @forelse($latestEvents as $event)
            <div class="flex items-center gap-4 px-6 py-4 hover:bg-slate-50/60 transition">
                {{-- Poster --}}
                @if($event->poster_path)
                    <img src="{{ asset('storage/' . $event->poster_path) }}"
                         alt="{{ $event->title }}"
                         class="w-12 h-16 object-cover rounded-xl border border-slate-100 shrink-0">
                @else
                    <div class="w-12 h-16 bg-indigo-50 rounded-xl flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-indigo-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586"/>
                        </svg>
                    </div>
                @endif

                {{-- Info --}}
                <div class="flex-1 min-w-0">
                    <p class="font-bold text-slate-800 truncate">{{ $event->title }}</p>
                    <p class="text-xs text-slate-400 mt-0.5">
                        {{ $event->category->name ?? '-' }} •
                        {{ \Carbon\Carbon::parse($event->date)->format('d M Y') }}
                    </p>
                </div>

                {{-- Harga & Stok --}}
                <div class="text-right shrink-0">
                    <p class="font-black text-indigo-600 text-sm">
                        {{ $event->price == 0 ? 'Gratis' : 'Rp ' . number_format($event->price, 0, ',', '.') }}
                    </p>
                    <p class="text-xs text-slate-400">Stok: {{ $event->stock }}</p>
                </div>

                {{-- Tombol Edit --}}
                <a href="{{ route('admin.events.edit', $event) }}"
                   class="p-2 text-indigo-400 hover:bg-indigo-50 rounded-xl transition shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </a>
            </div>
            @empty
            <div class="text-center py-12 text-slate-400">
                <p class="font-semibold">Belum ada event.</p>
                <a href="{{ route('admin.events.create') }}" class="text-indigo-500 hover:underline text-sm">Tambah sekarang →</a>
            </div>
            @endforelse
        </div>
    </div>

    {{-- Kategori Terpopuler --}}
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-50 flex justify-between items-center">
            <h3 class="font-black text-lg text-slate-800">Kategori</h3>
            <a href="{{ route('admin.categories.index') }}" class="text-indigo-600 font-bold text-sm hover:underline">Kelola →</a>
        </div>
        <div class="divide-y divide-slate-50 p-2">
            @forelse($topCategories as $category)
            <div class="flex items-center justify-between px-4 py-3 hover:bg-slate-50/60 rounded-2xl transition">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 bg-indigo-50 rounded-xl flex items-center justify-center text-indigo-600 font-black text-sm">
                        {{ strtoupper(substr($category->name, 0, 1)) }}
                    </div>
                    <span class="font-bold text-slate-700 text-sm">{{ $category->name }}</span>
                </div>
                <span class="px-2.5 py-1 bg-indigo-50 text-indigo-600 rounded-full text-xs font-black">
                    {{ $category->events_count }} event
                </span>
            </div>
            @empty
            <div class="text-center py-12 text-slate-400">
                <p class="font-semibold text-sm">Belum ada kategori.</p>
            </div>
            @endforelse
        </div>
    </div>

</div>

@endsection