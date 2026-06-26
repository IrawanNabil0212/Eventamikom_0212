@extends('layouts.admin')
@section('title', 'Laporan Transaksi - Admin')
@section('page_title', 'Laporan Transaksi')
@section('page_subtitle', 'Pantau arus kas dan penjualan tiket Anda.')

@section('content')
<div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
    
    <div class="px-8 py-5 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
        <h3 class="font-bold text-slate-700">Daftar Transaksi Terbaru</h3>
        <div class="text-sm text-slate-500 font-medium">
            Total Data: <span class="text-indigo-600 font-bold">{{ $transactions->total() }}</span>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead class="bg-white text-slate-400 uppercase text-[10px] font-black tracking-widest border-b border-slate-100">
                <tr>
                    <th class="px-8 py-5">Order ID</th>
                    <th class="px-8 py-5">Detail Pembeli</th>
                    <th class="px-8 py-5">Event</th>
                    <th class="px-8 py-5">Waktu Transaksi</th>
                    <th class="px-8 py-5">Status</th>
                    <th class="px-8 py-5 text-right">Total Tagihan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($transactions as $trx)
                <tr class="hover:bg-slate-50/80 transition duration-200 {{ $trx->status == 'pending' ? 'opacity-75' : '' }}">
                    
                    <td class="px-8 py-5 align-top">
                        <span class="font-mono font-bold px-3 py-1.5 rounded-lg text-xs tracking-wide {{ $trx->status == 'pending' ? 'bg-slate-100 text-slate-500' : 'text-indigo-700 bg-indigo-50 border border-indigo-100' }}">
                            {{ $trx->order_id }}
                        </span>
                    </td>

                    <td class="px-8 py-5 align-top">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-indigo-100 to-blue-50 text-indigo-600 flex items-center justify-center font-bold text-sm shrink-0 border border-indigo-100">
                                {{ strtoupper(substr($trx->customer_name ?? 'U', 0, 1)) }}
                            </div>
                            <div>
                                <p class="font-bold text-slate-800">{{ $trx->customer_name }}</p>
                                <div class="flex flex-col gap-1 mt-1 text-xs text-slate-500">
                                    <span class="flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                        {{ $trx->customer_email }}
                                    </span>
                                    <span class="flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                        {{ $trx->customer_phone }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </td>

                    <td class="px-8 py-5 align-top">
                        <div class="flex items-center gap-2">
                            <div class="p-1.5 bg-slate-100 rounded-md text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path></svg>
                            </div>
                            <p class="font-semibold text-slate-700">{{ $trx->event->title ?? '-' }}</p>
                        </div>
                    </td>

                    <td class="px-8 py-5 align-top">
                        <p class="text-sm font-semibold text-slate-700">{{ $trx->created_at->format('d M Y') }}</p>
                        <p class="text-xs text-slate-400 mt-0.5">{{ $trx->created_at->format('H:i') }} WIB</p>
                    </td>

                    <td class="px-8 py-5 align-top">
                        @if($trx->status === 'settlement' || $trx->status === 'success')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 text-emerald-600 rounded-lg text-[11px] font-black uppercase tracking-wider border border-emerald-200/60 shadow-sm">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Success
                            </span>
                        @elseif($trx->status === 'pending')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-50 text-amber-600 rounded-lg text-[11px] font-black uppercase tracking-wider border border-amber-200/60 shadow-sm">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span> Pending
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-rose-50 text-rose-600 rounded-lg text-[11px] font-black uppercase tracking-wider border border-rose-200/60 shadow-sm">
                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> {{ $trx->status }}
                            </span>
                        @endif
                    </td>

                    <td class="px-8 py-5 text-right align-top">
                        <p class="font-black text-base {{ $trx->status == 'pending' ? 'text-slate-500' : 'text-slate-900' }}">
                            Rp {{ number_format($trx->total_price, 0, ',', '.') }}
                        </p>
                    </td>
                </tr>
                
                @empty
                <tr>
                    <td colspan="6" class="px-8 py-20 text-center">
                        <div class="flex flex-col items-center justify-center space-y-4">
                            <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center border-4 border-white shadow-sm">
                                <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </div>
                            <div>
                                <p class="text-slate-600 font-bold text-lg">Belum Ada Transaksi</p>
                                <p class="text-slate-400 text-sm mt-1">Data penjualan tiket akan muncul di sini.</p>
                            </div>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="px-8 py-5 bg-slate-50/50 border-t border-slate-100 flex items-center justify-between">
        <div class="w-full">
            {{ $transactions->links() }}
        </div>
    </div>
</div>
@endsection