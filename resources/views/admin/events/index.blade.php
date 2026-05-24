@extends('layouts.admin', ['title' => 'Kelola Event'])

@section('content')
<header class="mb-10 flex items-start justify-between">
    <div>
        <h1 class="text-3xl font-black text-slate-800">Kelola Event</h1>
        <p class="text-slate-500 font-medium">Tambah, edit, dan hapus event yang tampil di platform.</p>
    </div>
    <a href="{{ route('admin.events.create') }}"
       class="px-6 py-3 bg-indigo-600 text-white rounded-2xl font-bold shadow-lg shadow-indigo-100 hover:bg-indigo-700 transition">
        + Tambah Event
    </a>
</header>

@if(session('success'))
    <div class="mb-6 px-5 py-4 bg-green-50 border border-green-200 text-green-700 rounded-2xl font-medium text-sm">
        ✓ {{ session('success') }}
    </div>
@endif

<div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden">
    <table class="w-full">
        <thead>
            <tr class="border-b border-slate-100">
                <th class="text-left text-xs font-black uppercase tracking-widest text-slate-400 px-8 py-5 w-16">No</th>
                <th class="text-left text-xs font-black uppercase tracking-widest text-slate-400 py-5">Poster</th>
                <th class="text-left text-xs font-black uppercase tracking-widest text-slate-400 py-5">Event</th>
                <th class="text-left text-xs font-black uppercase tracking-widest text-slate-400 py-5">Kategori</th>
                <th class="text-left text-xs font-black uppercase tracking-widest text-slate-400 py-5">Tanggal</th>
                <th class="text-left text-xs font-black uppercase tracking-widest text-slate-400 py-5">Harga</th>
                <th class="text-left text-xs font-black uppercase tracking-widest text-slate-400 py-5">Stok</th>
                <th class="text-left text-xs font-black uppercase tracking-widest text-slate-400 py-5 pr-8">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-50">
            @forelse($events as $index => $event)
            <tr class="hover:bg-slate-50/60 transition">
                <td class="px-8 py-4 text-slate-400 font-medium">{{ $index + 1 }}</td>
                <td class="py-4">
                    @if($event->poster_path)
                        <img src="{{ asset('storage/' . $event->poster_path) }}"
                             class="w-14 h-14 rounded-2xl object-cover border border-slate-100" alt="poster">
                    @else
                        <div class="w-14 h-14 rounded-2xl bg-indigo-50 flex items-center justify-center">
                            <svg class="w-6 h-6 text-indigo-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01"/>
                            </svg>
                        </div>
                    @endif
                </td>
                <td class="py-4 pr-4">
                    <p class="font-bold text-slate-800 leading-tight">{{ $event->title }}</p>
                    <p class="text-xs text-slate-400 mt-1 truncate max-w-[200px]">{{ $event->location }}</p>
                </td>
                <td class="py-4">
                    <span class="px-3 py-1 bg-indigo-50 text-indigo-600 rounded-full text-xs font-bold">
                        {{ $event->category->name ?? '-' }}
                    </span>
                </td>
                <td class="py-4 text-slate-500 text-sm">
                    {{ \Carbon\Carbon::parse($event->date)->format('d M Y') }}
                </td>
                <td class="py-4 font-bold text-indigo-600">
                    {{ $event->price == 0 ? 'Gratis' : 'Rp ' . number_format($event->price, 0, ',', '.') }}
                </td>
                <td class="py-4">
                    <span class="px-3 py-1 rounded-full text-xs font-bold
                        {{ $event->stock > 10 ? 'bg-green-50 text-green-600' : 'bg-rose-50 text-rose-600' }}">
                        {{ $event->stock }}
                    </span>
                </td>
                <td class="py-4 pr-8">
                    <div class="flex items-center gap-2">
                        <a href="{{ route('events.show', $event->id) }}" target="_blank"
                           class="p-2 text-slate-400 hover:bg-slate-100 rounded-xl transition" title="Lihat">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </a>
                        <a href="{{ route('admin.events.edit', $event) }}"
                           class="p-2 text-indigo-500 hover:bg-indigo-50 rounded-xl transition" title="Edit">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </a>
                        <form action="{{ route('admin.events.destroy', $event) }}" method="POST" class="inline"
                              onsubmit="return confirm('Hapus event \'{{ $event->title }}\'?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-2 text-red-400 hover:bg-red-50 rounded-xl transition" title="Hapus">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center py-16 text-slate-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 mx-auto mb-3 opacity-40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <p class="font-semibold">Belum ada event.</p>
                    <a href="{{ route('admin.events.create') }}" class="text-indigo-500 hover:underline text-sm">Tambah sekarang →</a>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection