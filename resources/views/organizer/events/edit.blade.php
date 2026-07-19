@extends('layouts.organizer')
@section('page_title', 'Edit Event')
@section('page_subtitle', 'Perbarui detail acara Anda.')
@section('content')
<div class="bg-white rounded-2xl border border-slate-200 p-8 max-w-2xl">
    @if($errors->any())
    <div class="mb-6 p-4 bg-red-100 text-red-700 rounded-xl">
        <ul class="list-disc list-inside">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    @if($event->poster_path)
    <img src="{{ asset('storage/' . $event->poster_path) }}" alt="Poster" class="w-full h-48 object-cover rounded-xl mb-6">
    @endif

    <form action="{{ route('organizer.events.update', $event->id) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-sm font-bold mb-2">Judul Event</label>
            <input type="text" name="title" value="{{ old('title', $event->title) }}" required
                class="w-full px-4 py-3 border-2 border-slate-100 rounded-xl focus:border-emerald-600 outline-none">
        </div>

        <div>
            <label class="block text-sm font-bold mb-2">Kategori</label>
            <select name="category_id" required class="w-full px-4 py-3 border-2 border-slate-100 rounded-xl focus:border-emerald-600 outline-none">
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ old('category_id', $event->category_id) == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-bold mb-2">Deskripsi</label>
            <textarea name="description" rows="4"
                class="w-full px-4 py-3 border-2 border-slate-100 rounded-xl focus:border-emerald-600 outline-none">{{ old('description', $event->description) }}</textarea>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-bold mb-2">Tanggal & Waktu</label>
                <input type="datetime-local" name="date"
                    value="{{ old('date', \Carbon\Carbon::parse($event->date)->format('Y-m-d\TH:i')) }}" required
                    class="w-full px-4 py-3 border-2 border-slate-100 rounded-xl focus:border-emerald-600 outline-none">
            </div>
            <div>
                <label class="block text-sm font-bold mb-2">Lokasi</label>
                <input type="text" name="location" value="{{ old('location', $event->location) }}" required
                    class="w-full px-4 py-3 border-2 border-slate-100 rounded-xl focus:border-emerald-600 outline-none">
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-bold mb-2">Harga Tiket (Rp)</label>
                <input type="number" name="price" min="0" value="{{ old('price', $event->price) }}" required
                    class="w-full px-4 py-3 border-2 border-slate-100 rounded-xl focus:border-emerald-600 outline-none">
            </div>
            <div>
                <label class="block text-sm font-bold mb-2">Stok Tiket</label>
                <input type="number" name="stock" min="0" value="{{ old('stock', $event->stock) }}" required
                    class="w-full px-4 py-3 border-2 border-slate-100 rounded-xl focus:border-emerald-600 outline-none">
            </div>
        </div>

        <div>
            <label class="block text-sm font-bold mb-2">Ganti Poster (opsional)</label>
            <input type="file" name="poster" accept="image/*"
                class="w-full px-4 py-3 border-2 border-slate-100 rounded-xl">
        </div>

        <button type="submit" class="w-full py-4 bg-emerald-600 text-white rounded-xl font-black hover:bg-emerald-700 transition">
            Perbarui Event
        </button>
    </form>
</div>
@endsection