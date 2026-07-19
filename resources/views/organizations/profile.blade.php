@extends('layouts.app')
@section('content')
<main class="max-w-6xl mx-auto px-6 py-16">

    {{-- Header Profil --}}
    <div class="bg-white rounded-3xl border border-slate-200 p-8 mb-10 flex flex-col md:flex-row items-center gap-8">
        <div class="w-24 h-24 bg-indigo-100 text-indigo-700 rounded-2xl flex items-center justify-center font-black text-3xl shrink-0">
            {{ strtoupper(substr($organization->name, 0, 2)) }}
        </div>
        <div class="flex-1 text-center md:text-left">
            <h1 class="text-3xl font-black">{{ $organization->name }}</h1>
            @if($organization->description)
            <p class="text-slate-500 mt-2 max-w-2xl">{{ $organization->description }}</p>
            @endif

            <div class="flex items-center justify-center md:justify-start gap-6 mt-4">
                @if($overallReviewCount > 0)
                <div class="flex items-center gap-2">
                    <span class="text-yellow-400 text-xl">★</span>
                    <span class="font-black text-lg">{{ number_format($overallAverageRating, 1) }}</span>
                    <span class="text-slate-400 text-sm">({{ $overallReviewCount }} ulasan)</span>
                </div>
                @else
                <span class="text-slate-400 text-sm">Belum ada ulasan</span>
                @endif
                <span class="text-slate-300">|</span>
                <span class="text-slate-500 text-sm">{{ $events->total() }} event diselenggarakan</span>
            </div>
        </div>
    </div>

    {{-- Daftar Event --}}
    <div class="mb-12">
        <h2 class="text-2xl font-bold mb-6">Event dari {{ $organization->name }}</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @forelse($events as $event)
            <a href="{{ route('events.show', $event->id) }}" class="bg-white rounded-2xl border border-slate-200 overflow-hidden hover:shadow-lg transition">
                <img src="{{ $event->poster_path ? asset('storage/' . $event->poster_path) : 'https://placehold.co/400x300' }}"
                     class="w-full h-40 object-cover" alt="{{ $event->title }}">
                <div class="p-4">
                    <h3 class="font-bold">{{ $event->title }}</h3>
                    <p class="text-xs text-slate-400 mt-1">{{ \Carbon\Carbon::parse($event->date)->format('d M Y') }}</p>
                    @if($event->reviews_count > 0)
                    <div class="flex items-center gap-1 mt-2 text-sm">
                        <span class="text-yellow-400">★</span>
                        <span class="font-bold">{{ number_format($event->reviews_avg_rating, 1) }}</span>
                        <span class="text-slate-400">({{ $event->reviews_count }})</span>
                    </div>
                    @endif
                </div>
            </a>
            @empty
            <p class="text-slate-400 col-span-3">Belum ada event yang diselenggarakan.</p>
            @endforelse
        </div>
        <div class="mt-6">
            {{ $events->links() }}
        </div>
    </div>

    {{-- Ulasan Terbaru Gabungan --}}
    <div>
        <h2 class="text-2xl font-bold mb-6">Ulasan Terbaru</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @forelse($recentReviews as $review)
            <div class="bg-white rounded-2xl border border-slate-100 p-5">
                <div class="flex items-center justify-between mb-2">
                    <span class="font-bold">{{ $review->user->name ?? 'Pengguna' }}</span>
                    <div class="flex gap-0.5">
                        @for($i = 1; $i <= 5; $i++)
                            <span class="{{ $i <= $review->rating ? 'text-yellow-400' : 'text-slate-200' }}">★</span>
                        @endfor
                    </div>
                </div>
                <p class="text-xs text-slate-400 mb-2">untuk event "{{ $review->event->title ?? '-' }}"</p>
                @if($review->comment)
                <p class="text-slate-600 text-sm">{{ $review->comment }}</p>
                @endif
            </div>
            @empty
            <p class="text-slate-400">Belum ada ulasan.</p>
            @endforelse
        </div>
    </div>
</main>
@endsection