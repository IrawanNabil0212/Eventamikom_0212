@extends('layouts.admin', ['title' => 'Kelola Partner'])

@section('content')
<header class="mb-10 flex items-start justify-between">
    <div>
        <h1 class="text-3xl font-black text-slate-800">Kelola Partner</h1>
        <p class="text-slate-500 font-medium">Atur mitra yang mendukung platform AmikomEventHub.</p>
    </div>
    <a href="{{ route('admin.partners.create') }}"
       class="px-6 py-3 bg-indigo-600 text-white rounded-2xl font-bold shadow-lg shadow-indigo-100 hover:bg-indigo-700 transition">
        + Tambah Partner
    </a>
</header>

@if(session('success'))
    <div class="mb-6 px-5 py-4 bg-green-50 border border-green-200 text-green-700 rounded-2xl font-medium text-sm">
        ✓ {{ session('success') }}
    </div>
@endif

<form method="GET" action="{{ route('admin.partners.index') }}" class="mb-6">
    <div class="flex gap-3 max-w-md">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Cari nama partner..."
               class="flex-1 px-5 py-3 rounded-xl border border-slate-200 focus:ring-2 focus:ring-indigo-500 outline-none text-sm">
        <button type="submit"
                class="px-5 py-3 bg-indigo-600 text-white rounded-xl font-bold text-sm hover:bg-indigo-700 transition">
            Cari
        </button>
        @if(request('search'))
            <a href="{{ route('admin.partners.index') }}"
               class="px-5 py-3 border border-slate-200 text-slate-500 rounded-xl font-bold text-sm hover:bg-slate-50 transition">
                Reset
            </a>
        @endif
    </div>
</form>

<div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden">
    <table class="w-full">
        <thead>
            <tr class="border-b border-slate-100">
                <th class="text-left text-xs font-black uppercase tracking-widest text-slate-400 px-8 py-5 w-16">No</th>
                <th class="text-left text-xs font-black uppercase tracking-widest text-slate-400 py-5">Nama Partner</th>
                <th class="text-left text-xs font-black uppercase tracking-widest text-slate-400 py-5">Logo</th>
                <th class="text-left text-xs font-black uppercase tracking-widest text-slate-400 py-5">Dibuat</th>
                <th class="text-left text-xs font-black uppercase tracking-widest text-slate-400 py-5 pr-8">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-50">
            @forelse($partners as $index => $partner)
            <tr class="hover:bg-slate-50/60 transition">
                <td class="px-8 py-5 text-slate-400 font-medium">{{ $index + 1 }}</td>
                <td class="py-5 font-bold text-slate-800">{{ $partner->name }}</td>
                <td class="py-5">
                    @if($partner->logo_url)
                        <img src="{{ $partner->logo_url }}" alt="{{ $partner->name }}"
                             class="h-8 object-contain rounded border border-slate-100"
                             onerror="this.style.display='none'">
                    @else
                        <span class="text-slate-400 text-sm">-</span>
                    @endif
                </td>
                <td class="py-5 text-slate-400 text-sm">{{ $partner->created_at->format('d M Y') }}</td>
                <td class="py-5 pr-8">
                    <div class="flex items-center gap-2">
                        <a href="{{ route('admin.partners.edit', $partner) }}"
                           class="p-2 text-indigo-500 hover:bg-indigo-50 rounded-xl transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </a>
                        <form action="{{ route('admin.partners.destroy', $partner) }}" method="POST" class="inline"
                              onsubmit="return confirm('Hapus partner \'{{ $partner->name }}\'?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-2 text-red-400 hover:bg-red-50 rounded-xl transition">
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
                <td colspan="5" class="text-center py-16 text-slate-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10 mx-auto mb-3 opacity-40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <p class="font-semibold">
                        @if(request('search'))
                            Tidak ada partner dengan nama "{{ request('search') }}".
                        @else
                            Belum ada partner.
                        @endif
                    </p>
                    <a href="{{ route('admin.partners.create') }}" class="text-indigo-500 hover:underline text-sm">Tambah sekarang →</a>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection