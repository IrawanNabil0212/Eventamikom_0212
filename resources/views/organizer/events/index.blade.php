@extends('layouts.organizer')
@section('page_title', 'Kelola Event')
@section('page_subtitle', 'Daftar acara yang Anda kelola.')
@section('content')
<div>
    <div class="flex justify-end mb-6">
        <a href="{{ route('organizer.events.create') }}"
           class="px-5 py-3 bg-emerald-600 text-white rounded-xl font-bold hover:bg-emerald-700 transition">
            + Buat Event Baru
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                <tr>
                    <th class="px-6 py-4">Judul</th>
                    <th class="px-6 py-4">Kategori</th>
                    <th class="px-6 py-4">Tanggal</th>
                    <th class="px-6 py-4">Harga</th>
                    <th class="px-6 py-4">Stok</th>
                    <th class="px-6 py-4">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($events as $event)
                <tr>
                    <td class="px-6 py-4 font-bold">{{ $event->title }}</td>
                    <td class="px-6 py-4 text-sm">{{ $event->category->name ?? '-' }}</td>
                    <td class="px-6 py-4 text-sm text-slate-500">{{ \Carbon\Carbon::parse($event->date)->format('d M Y') }}</td>
                    <td class="px-6 py-4 text-sm">Rp {{ number_format($event->price, 0, ',', '.') }}</td>
                    <td class="px-6 py-4 text-sm">{{ $event->stock }}</td>
                    <td class="px-6 py-4">
                        <div class="flex gap-3">
                            <a href="{{ route('organizer.events.edit', $event->id) }}" class="text-indigo-600 font-bold text-sm hover:underline">Edit</a>
                            <form action="{{ route('organizer.events.destroy', $event->id) }}" method="POST"
                                  onsubmit="return confirm('Yakin hapus event ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 font-bold text-sm hover:underline">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-10 text-center text-slate-400">Belum ada event.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $events->links() }}
    </div>
</div>
@endsection