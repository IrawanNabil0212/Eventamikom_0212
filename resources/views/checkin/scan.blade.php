@extends('layouts.organizer')
@section('page_title', 'Scan Check-in Peserta')
@section('page_subtitle', 'Arahkan scanner QR ke kotak input di bawah ini, atau ketik Order ID secara manual.')
@section('content')
<div class="max-w-xl">

    @if (session('checkin_status'))
        @php
            $color = match(session('checkin_status')) {
                'success' => 'bg-green-100 text-green-800 border-green-300',
                'warning' => 'bg-yellow-100 text-yellow-800 border-yellow-300',
                default   => 'bg-red-100 text-red-800 border-red-300',
            };
        @endphp
        <div class="border {{ $color }} rounded-lg p-4 mb-6">
            {{ session('checkin_message') }}
        </div>
    @endif

    <form action="{{ route('organizer.checkin.store') }}" method="POST">
        @csrf
        <input
            type="text"
            name="order_id"
            id="order_id"
            autofocus
            autocomplete="off"
            placeholder="Scan atau ketik Order ID di sini..."
            class="w-full border border-gray-300 rounded-lg px-4 py-3 text-lg focus:outline-none focus:ring-2 focus:ring-emerald-500"
        >
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const input = document.getElementById('order_id');
        input.focus();
        input.addEventListener('blur', function () {
            setTimeout(() => input.focus(), 100);
        });
    });
</script>
@endsection