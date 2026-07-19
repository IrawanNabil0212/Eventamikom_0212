@extends('layouts.app')
@section('content')
<main class="max-w-4xl mx-auto px-6 py-16">
    <div class="mb-10">
        <h1 class="text-4xl font-extrabold">Tiket Saya</h1>
        <p class="text-slate-500 mt-2">Daftar tiket yang pernah Anda beli.</p>
    </div>

    @if(session('success'))
    <div class="mb-6 p-4 bg-green-100 text-green-700 rounded-xl font-bold">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div class="mb-6 p-4 bg-red-100 text-red-700 rounded-xl font-bold">{{ session('error') }}</div>
    @endif

    <div class="space-y-6">
        @forelse($transactions as $transaction)
        <div class="bg-white rounded-3xl border border-slate-200 p-6">
            <div class="flex flex-col md:flex-row justify-between md:items-center gap-4">
                <div>
                    <h3 class="font-extrabold text-lg">{{ $transaction->event->title ?? 'Event tidak ditemukan' }}</h3>
                    @if($transaction->event)
                    <p class="text-slate-500 text-sm">
                        {{ \Carbon\Carbon::parse($transaction->event->date)->translatedFormat('d F Y') }}
                        • {{ $transaction->event->location }}
                    </p>
                    @endif
                    <p class="text-xs text-slate-400 mt-1">Order ID: {{ $transaction->order_id }}</p>
                </div>
                <span class="inline-block px-4 py-1.5 rounded-full text-xs font-bold w-fit
                    {{ $transaction->status === 'success' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                    {{ $transaction->status === 'success' ? 'Lunas' : ucfirst($transaction->status) }}
                </span>
            </div>

            {{-- Kalau sudah pernah review, tampilkan review-nya --}}
            @if($transaction->review)
            <div class="mt-5 pt-5 border-t border-slate-100">
                <p class="text-sm font-bold text-slate-700 mb-1">Ulasan Anda:</p>
                <div class="flex gap-1 mb-2">
                    @for($i = 1; $i <= 5; $i++)
                        <span class="{{ $i <= $transaction->review->rating ? 'text-yellow-400' : 'text-slate-200' }}">★</span>
                    @endfor
                </div>
                @if($transaction->review->comment)
                <p class="text-slate-600 text-sm italic">"{{ $transaction->review->comment }}"</p>
                @endif
            </div>

            {{-- Kalau eligible tapi belum review, tampilkan form --}}
            @elseif($transaction->is_reviewable)
            <div class="mt-5 pt-5 border-t border-slate-100">
                <p class="text-sm font-bold text-slate-700 mb-3">Bagaimana pengalaman Anda di acara ini?</p>
                <form action="{{ route('reviews.store', $transaction->id) }}" method="POST">
                    @csrf
                    <div class="flex gap-1 mb-3" id="star-rating-{{ $transaction->id }}">
                        @for($i = 1; $i <= 5; $i++)
                        <label class="cursor-pointer text-2xl text-slate-300 star-label" data-value="{{ $i }}">
                            <input type="radio" name="rating" value="{{ $i }}" class="hidden star-input" required>
                            ★
                        </label>
                        @endfor
                    </div>
                    <textarea name="comment" rows="2" placeholder="Ceritakan pengalaman Anda (opsional)"
                        class="w-full px-4 py-3 border-2 border-slate-100 rounded-xl focus:border-indigo-600 outline-none mb-3"></textarea>
                    <button type="submit" class="px-6 py-2.5 bg-indigo-600 text-white rounded-xl font-bold hover:bg-indigo-700 transition">
                        Kirim Ulasan
                    </button>
                </form>
            </div>

            {{-- Kalau belum eligible (event belum lewat H+1) --}}
            @elseif($transaction->status === 'success' && $transaction->event)
            <div class="mt-5 pt-5 border-t border-slate-100">
                <p class="text-xs text-slate-400">
                    Anda bisa memberi ulasan mulai
                    {{ \Carbon\Carbon::parse($transaction->event->date)->addMinutes(\App\Models\Review::REVIEW_DELAY_MINUTES)->translatedFormat('d F Y, H:i') }}.
                </p>
            </div>
            @endif
        </div>
        @empty
        <div class="text-center py-20 text-slate-400">
            Anda belum memiliki tiket. <a href="{{ route('home') }}" class="text-indigo-600 font-bold">Jelajahi event</a>.
        </div>
        @endforelse
    </div>

    <div class="mt-8">
        {{ $transactions->links() }}
    </div>
</main>

{{-- Script kecil untuk highlight bintang saat diklik/hover, murni UX,
     tidak pakai library tambahan supaya tidak bentrok dengan setup project --}}
<script>
document.querySelectorAll('[id^="star-rating-"]').forEach(function (group) {
    const labels = group.querySelectorAll('.star-label');
    labels.forEach(function (label) {
        label.addEventListener('click', function () {
            const value = parseInt(label.dataset.value);
            labels.forEach(function (l) {
                l.classList.toggle('text-yellow-400', parseInt(l.dataset.value) <= value);
                l.classList.toggle('text-slate-300', parseInt(l.dataset.value) > value);
            });
        });
    });
});
</script>
@endsection