@extends('layouts.app')

@section('content')
<section class="max-w-7xl mx-auto px-6 py-12">

    {{-- Header --}}
    <div class="mb-10">
        <a href="{{ route('home') }}" class="text-sm text-indigo-600 font-bold hover:underline">← Kembali ke Home</a>
        <h1 class="text-4xl font-black mt-3 text-slate-800">Semua Kategori</h1>
        <p class="text-slate-500 mt-1">Temukan event berdasarkan kategori favoritmu.</p>
    </div>

    {{-- Grid Kategori --}}
    @if($categories->count() > 0)
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @foreach($categories as $category)
        <a href="{{ route('category.show', $category->slug) }}"
           class="group bg-white rounded-3xl border border-slate-100 shadow-sm hover:shadow-xl hover:border-indigo-200 transition-all duration-300 p-8 flex flex-col items-center text-center gap-4">

            {{-- Icon / Inisial --}}
            <div class="w-16 h-16 bg-indigo-50 group-hover:bg-indigo-100 rounded-2xl flex items-center justify-center transition">
                <span class="text-2xl font-black text-indigo-600">
                    {{ strtoupper(substr($category->name, 0, 1)) }}
                </span>
            </div>

            {{-- Nama --}}
            <div>
                <h3 class="font-black text-slate-800 group-hover:text-indigo-600 transition text-lg">
                    {{ $category->name }}
                </h3>
                <p class="text-sm text-slate-400 mt-1">
                    {{ $category->events_count }} event tersedia
                </p>
            </div>

            {{-- Badge --}}
            @if($category->events_count > 0)
                <span class="px-3 py-1 bg-indigo-50 text-indigo-600 rounded-full text-xs font-black">
                    Lihat Event →
                </span>
            @else
                <span class="px-3 py-1 bg-slate-50 text-slate-400 rounded-full text-xs font-bold">
                    Belum ada event
                </span>
            @endif
        </a>
        @endforeach
    </div>
    @else
    <div class="text-center py-20 text-slate-400">
        <svg class="w-16 h-16 mx-auto mb-4 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
        </svg>
        <p class="text-xl font-bold">Belum ada kategori tersedia.</p>
    </div>
    @endif

</section>
@endsection