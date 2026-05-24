@extends('layouts.app')

@section('content')
<section class="max-w-7xl mx-auto px-6 py-12">

    {{-- Header --}}
    <div class="mb-10">
        <a href="{{ route('home') }}" class="text-sm text-indigo-600 font-bold hover:underline">← Kembali ke Home</a>
        <h1 class="text-4xl font-black mt-3 text-slate-800">{{ $category->name }}</h1>
        <p class="text-slate-500 mt-1">{{ $events->count() }} event ditemukan dalam kategori ini</p>
    </div>

    {{-- Filter kategori lain --}}
    <div class="flex flex-wrap gap-3 mb-10">
        <a href="{{ route('home') }}#events"
           class="px-5 py-2.5 bg-slate-100 border border-slate-200 rounded-2xl font-bold text-sm text-slate-600 hover:border-indigo-400 hover:text-indigo-600 transition">
            Semua
        </a>
        @foreach($categories as $cat)
            <a href="{{ route('category.show', $cat->slug) }}"
               class="flex items-center gap-2 px-5 py-2.5 rounded-2xl font-bold text-sm transition border
                      {{ $cat->slug === $category->slug
                         ? 'bg-indigo-600 text-white border-indigo-600'
                         : 'bg-white border-slate-200 text-slate-700 hover:border-indigo-400 hover:text-indigo-600 hover:bg-indigo-50' }}">
                {{ $cat->name }}
                @if($cat->events_count > 0)
                    <span class="px-2 py-0.5 rounded-full text-xs font-black
                                 {{ $cat->slug === $category->slug ? 'bg-white/20 text-white' : 'bg-indigo-100 text-indigo-600' }}">
                        {{ $cat->events_count }}
                    </span>
                @endif
            </a>
        @endforeach
    </div>

    {{-- Grid Event --}}
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
                <div class="absolute top-4 left-4 px-3 py-1 bg-white/90 backdrop-blur rounded-lg text-xs font-bold uppercase text-indigo-600">
                    {{ $category->name }}
                </div>
            </div>
            <div class="p-6">
                <h3 class="text-xl font-bold mb-2 group-hover:text-indigo-600 transition">{{ $event->title }}</h3>
                <div class="flex items-center gap-2 text-slate-500 text-sm mb-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span>{{ \Carbon\Carbon::parse($event->date)->translatedFormat('d F Y') }}</span>
                </div>
                <div class="flex items-center gap-2 text-slate-500 text-sm mb-4">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    </svg>
                    <span>{{ $event->location }}</span>
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
            <svg class="w-16 h-16 mx-auto mb-4 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-xl font-bold">Belum ada event di kategori ini.</p>
            <a href="{{ route('home') }}" class="text-indigo-500 hover:underline text-sm">Lihat semua event →</a>
        </div>
        @endforelse
    </div>
</section>
@endsection