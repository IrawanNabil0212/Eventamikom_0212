@extends('layouts.admin', ['title' => 'Edit Partner'])

@section('content')
<header class="mb-10">
    <a href="{{ route('admin.partners.index') }}" class="text-sm text-slate-400 hover:text-slate-600 font-medium transition">← Kembali</a>
    <h1 class="text-3xl font-black text-slate-800 mt-1">Edit Partner</h1>
    <p class="text-slate-500 font-medium">Perbarui informasi partner <span class="text-indigo-600">"{{ $partner->name }}"</span>.</p>
</header>

<div class="bg-white rounded-[2.5rem] border border-slate-100 p-10 shadow-sm max-w-lg">
    <form action="{{ route('admin.partners.update', $partner) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-sm font-bold text-slate-700 mb-2">
                Nama Partner <span class="text-red-400">*</span>
            </label>
            <input type="text" name="name" value="{{ old('name', $partner->name) }}"
                   autofocus
                   class="w-full px-5 py-3 rounded-xl border @error('name') border-red-300 bg-red-50 @else border-slate-200 @enderror focus:ring-2 focus:ring-indigo-500 outline-none transition">
            @error('name')
                <p class="mt-2 text-sm text-red-500 font-medium">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-bold text-slate-700 mb-2">
                Logo Partner <span class="text-slate-400 font-normal">(opsional)</span>
            </label>

            @if($partner->logo_path)
                <div class="mb-3 p-4 border border-dashed border-slate-200 rounded-2xl bg-slate-50/50 flex items-center gap-4">
                    <img src="{{ asset('storage/' . $partner->logo_path) }}"
                         alt="{{ $partner->name }}"
                         class="w-16 h-16 object-contain rounded-lg border border-slate-100">
                    <p class="text-xs text-slate-400">Logo saat ini. Upload baru untuk mengganti.</p>
                </div>
            @endif

            <input type="file" name="logo" accept="image/*"
                   class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
            @error('logo')
                <p class="mt-2 text-sm text-red-500 font-medium">{{ $message }}</p>
            @enderror
            <p class="mt-2 text-xs text-slate-400">Format: JPG, PNG, SVG. Maks 2MB. Kosongkan jika tidak ingin mengganti.</p>
        </div>

        <div class="flex justify-end gap-4 pt-4 border-t border-slate-100">
            <a href="{{ route('admin.partners.index') }}"
               class="px-6 py-3 font-bold text-slate-400 hover:text-slate-600 transition">Batal</a>
            <button type="submit"
                    class="px-8 py-3 bg-indigo-600 text-white rounded-2xl font-bold shadow-lg shadow-indigo-100 hover:bg-indigo-700 transform active:scale-95 transition">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>
@endsection