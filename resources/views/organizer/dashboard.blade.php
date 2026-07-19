@extends('layouts.organizer')
@section('page_title', 'Dashboard')
@section('page_subtitle', 'Ringkasan performa acara Anda.')
@section('content')
<div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-2xl border border-slate-200 p-6">
            <p class="text-sm text-slate-500 font-bold uppercase tracking-wide mb-2">Total Pendapatan</p>
            <h2 class="text-3xl font-black text-emerald-600">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h2>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 p-6">
            <p class="text-sm text-slate-500 font-bold uppercase tracking-wide mb-2">Tiket Terjual</p>
            <h2 class="text-3xl font-black text-slate-800">{{ number_format($totalTicketsSold) }}</h2>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 p-6">
            <p class="text-sm text-slate-500 font-bold uppercase tracking-wide mb-2">Total Event</p>
            <h2 class="text-3xl font-black text-slate-800">{{ $totalEvents }}</h2>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
        <div class="p-6 border-b flex items-center justify-between">
            <h3 class="font-bold text-lg">Event Terbaru</h3>
            <a href="{{ route('organizer.events.create') }}" class="text-sm font-bold text-emerald-600 hover:underline">
                + Buat Event Baru
            </a>
        </div>
        <table class="w-full text-left">
            <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                <tr>
                    <th class="px-6 py-4">Judul Event</th>
                    <th class="px-6 py-4">Tanggal</th>
                    <th class="px-6 py-4">Stok</th>
                    <th class="px-6 py-4">Tiket Terjual</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($recentEvents as $event)
                <tr>
                    <td class="px-6 py-4 font-bold">{{ $event->title }}</td>
                    <td class="px-6 py-4 text-sm text-slate-500">{{ \Carbon\Carbon::parse($event->date)->format('d M Y') }}</td>
                    <td class="px-6 py-4 text-sm">{{ $event->stock }}</td>
                    <td class="px-6 py-4 text-sm">{{ $event->transactions_count }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-10 text-center text-slate-400">
                        Belum ada event. <a href="{{ route('organizer.events.create') }}" class="text-emerald-600 font-bold">Buat sekarang</a>.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection