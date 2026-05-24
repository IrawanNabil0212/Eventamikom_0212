@extends('layouts.app')

@section('content')
    {{-- ======================== HERO ======================== --}}
    <section class="max-w-7xl mx-auto px-6 py-20 flex flex-col md:flex-row items-center gap-12">
        <div class="flex-1 space-y-8">
            <span class="inline-block px-4 py-1.5 bg-indigo-100 text-indigo-700 rounded-full text-sm font-bold uppercase tracking-wider">#1 Event Platform</span>
            <h1 class="text-5xl md:text-7xl font-extrabold leading-tight">
                Temukan & Pesan <span class="text-indigo-600">Tiket Event</span> Impianmu.
            </h1>
            <p class="text-lg text-slate-500 max-w-lg leading-relaxed">
                Dari konser musik hingga workshop teknologi, semua ada di genggamanmu. Pesan aman & cepat dengan Midtrans.
            </p>
            <div class="flex gap-4">
                <a href="#events" class="px-8 py-4 bg-indigo-600 text-white rounded-2xl font-bold text-lg shadow-xl shadow-indigo-200 hover:scale-105 transition-transform">
                    Mulai Jelajah
                </a>
                <a href="#" class="px-8 py-4 border-2 border-slate-200 rounded-2xl font-bold text-lg hover:border-indigo-600 hover:text-indigo-600 transition">
                    Cara Pesan
                </a>
            </div>
        </div>

        <div class="flex-1 relative">
            <div class="absolute -top-10 -left-10 w-64 h-64 bg-indigo-400 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob"></div>
            <div class="absolute -bottom-10 -right-10 w-64 h-64 bg-purple-400 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-2000"></div>
            <img src="{{ asset('template-amikomhub-share-mhs/assets/concert.png') }}" alt="Concert" class="rounded-[2rem] shadow-2xl relative z-10 w-full object-cover aspect-[4/5] object-center">
            <div class="absolute -bottom-6 -left-6 glass p-6 rounded-2xl shadow-xl z-20 border border-white">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center text-green-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 font-bold uppercase">Terverifikasi</p>
                        <p class="font-bold">Pembayaran Aman via Midtrans</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ======================== KATEGORI ======================== --}}
@if($categories->count() > 0)
<section class="max-w-7xl mx-auto px-6 pb-8">
    <div class="flex items-center gap-3 mb-6">
        <h2 class="text-xl font-extrabold text-slate-800">Jelajahi Kategori</h2>
        <span class="px-3 py-1 bg-indigo-50 text-indigo-600 text-xs font-bold rounded-full">{{ $categories->count() }} kategori</span>
    </div>
    <div class="flex flex-wrap gap-3">
        @foreach($categories as $category)
            <a href="{{ route('category.show', $category->slug) }}"
               class="flex items-center gap-2 px-5 py-2.5 bg-white border border-slate-200 rounded-2xl font-bold text-sm text-slate-700 hover:border-indigo-400 hover:text-indigo-600 hover:bg-indigo-50 transition shadow-sm">
                {{ $category->name }}
                @if($category->events_count > 0)
                    <span class="px-2 py-0.5 bg-indigo-100 text-indigo-600 rounded-full text-xs font-black">{{ $category->events_count }}</span>
                @endif
            </a>
        @endforeach
    </div>
</section>
@endif

{{-- ======================== EVENTS (DINAMIS) ======================== --}}
<section id="kategori" class="max-w-7xl mx-auto px-6 pb-8">
        <div class="flex justify-between items-end mb-12">
        <div>
            <h2 class="text-3xl font-extrabold mb-2">Event Terdekat</h2>
            <p class="text-slate-500 font-medium">Jangan sampai ketinggalan acara seru minggu ini!</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @forelse($events as $event)
        <div class="group bg-white rounded-3xl border border-slate-100 shadow-sm hover:shadow-2xl transition-all duration-300 overflow-hidden">
            <div class="relative overflow-hidden aspect-[3/4]">
                @if($event->poster_path)
                    <img src="{{ asset('storage/' . $event->poster_path) }}"
                         alt="{{ $event->title }}"
                         class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                @else
                    <div class="w-full h-full bg-indigo-50 flex items-center justify-center">
                        <svg class="w-16 h-16 text-indigo-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                @endif
                @if($event->category)
                    <div class="absolute top-4 left-4 px-3 py-1 bg-white/90 backdrop-blur rounded-lg text-xs font-bold uppercase text-indigo-600">
                        {{ $event->category->name }}
                    </div>
                @endif
            </div>
            <div class="p-6">
                <h3 class="text-xl font-bold mb-2 group-hover:text-indigo-600 transition">{{ $event->title }}</h3>
                <div class="flex items-center gap-2 text-slate-500 text-sm mb-4">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span>{{ \Carbon\Carbon::parse($event->date)->translatedFormat('d F Y') }}</span>
                </div>
                <div class="flex justify-between items-center pt-4 border-t">
                    <span class="text-2xl font-black text-indigo-600">
                        {{ $event->price == 0 ? 'Gratis' : 'Rp ' . number_format($event->price, 0, ',', '.') }}
                    </span>
                    <a href="{{ route('events.show', $event->id) }}"
                       class="px-5 py-2 bg-indigo-50 text-indigo-600 rounded-xl font-bold hover:bg-indigo-600 hover:text-white transition">
                        Detail
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-3 text-center py-20 text-slate-400">
            <p class="text-xl font-bold">Belum ada event tersedia.</p>
        </div>
        @endforelse
    </div>
</section>

    {{-- ======================== PARTNER ======================== --}}
    @if($partners->count() > 0)
    <section class="max-w-7xl mx-auto px-6 py-16 border-t border-slate-100">
        <div class="text-center mb-10">
            <p class="text-xs font-black uppercase tracking-widest text-slate-400 mb-2">Didukung Oleh</p>
            <h2 class="text-2xl font-extrabold text-slate-800">Partner Kami</h2>
        </div>
        <div class="flex flex-wrap justify-center items-center gap-8">
            @foreach($partners as $partner)
                <div class="flex flex-col items-center gap-2 group">
                    @if($partner->logo_path)
                        <img src="{{ asset('storage/' . $partner->logo_path) }}"
                             alt="{{ $partner->name }}"
                            class="h-16 object-contain transition-all duration-300"
                             onerror="this.style.display='none'">
                    @else
                        <div class="w-12 h-12 bg-slate-100 rounded-xl flex items-center justify-center text-slate-400 font-black text-lg">
                            {{ strtoupper(substr($partner->name, 0, 1)) }}
                        </div>
                    @endif
                    <span class="text-xs font-bold text-slate-400 group-hover:text-slate-600 transition">{{ $partner->name }}</span>
                </div>
            @endforeach
        </div>
    </section>
    @endif

@endsection