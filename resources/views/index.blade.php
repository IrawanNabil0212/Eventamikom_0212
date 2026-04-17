@extends('app')

@section('content')

<!-- Hero Section -->
<section class="max-w-7xl mx-auto px-6 py-20 flex flex-col md:flex-row items-center gap-12">

    <div class="flex-1 space-y-8">
        <span
            class="inline-block px-4 py-1.5 bg-indigo-100 text-indigo-700 rounded-full text-sm font-bold uppercase tracking-wider">
            #1 Event Platform
        </span>

        <h1 class="text-5xl md:text-7xl font-extrabold leading-tight">
            Temukan & Pesan <span class="text-indigo-600">Tiket Event</span> Impianmu.
        </h1>

        <p class="text-lg text-slate-500 max-w-lg leading-relaxed">
            Dari konser musik hingga workshop teknologi, semua ada di genggamanmu.
            Pesan aman & cepat dengan Midtrans.
        </p>

        <div class="flex gap-4">
            <a href="#events"
                class="px-8 py-4 bg-indigo-600 text-white rounded-2xl font-bold text-lg shadow-xl hover:scale-105 transition">
                Mulai Jelajah
            </a>

            <a href="#"
                class="px-8 py-4 border-2 border-slate-200 rounded-2xl font-bold text-lg hover:border-indigo-600 hover:text-indigo-600 transition">
                Cara Pesan
            </a>
        </div>
    </div>

    <div class="flex-1">
        <img src="{{ asset('assets/concert.png') }}" alt="Concert"
            class="rounded-[2rem] shadow-2xl w-full object-cover aspect-[4/5]">
    </div>

</section>

<!-- Event Grid -->
<section id="events" class="max-w-7xl mx-auto px-6 py-20">

    <div class="flex justify-between items-end mb-12">
        <div>
            <h2 class="text-3xl font-extrabold mb-2">Event Terdekat</h2>
            <p class="text-slate-500 font-medium">Jangan sampai ketinggalan acara seru minggu ini!</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">

        <!-- Card 1 -->
        <div class="bg-white rounded-3xl shadow-sm overflow-hidden">
            <img src="{{ asset('assets/concert.png') }}" class="w-full h-72 object-cover">

            <div class="p-6">
                <h3 class="text-xl font-bold mb-2">Jazz Night 2024</h3>
                <p class="text-slate-500 mb-4">16 November 2024</p>

                <div class="flex justify-between items-center">
                    <span class="text-2xl font-black text-indigo-600">Rp 150rb</span>
                    <a href="#" class="px-4 py-2 bg-indigo-100 text-indigo-600 rounded-xl">Detail</a>
                </div>
            </div>
        </div>

        <!-- Card 2 -->
        <div class="bg-white rounded-3xl shadow-sm overflow-hidden">
            <img src="{{ asset('assets/workshop.png') }}" class="w-full h-72 object-cover">

            <div class="p-6">
                <h3 class="text-xl font-bold mb-2">AI & Future</h3>
                <p class="text-slate-500 mb-4">26 October 2024</p>

                <div class="flex justify-between items-center">
                    <span class="text-2xl font-black text-indigo-600">Rp 50rb</span>
                    <a href="#" class="px-4 py-2 bg-indigo-100 text-indigo-600 rounded-xl">Detail</a>
                </div>
            </div>
        </div>

        <!-- Card 3 -->
        <div class="bg-white rounded-3xl shadow-sm overflow-hidden">
            <img src="{{ asset('assets/hackathon.png') }}" class="w-full h-72 object-cover">

            <div class="p-6">
                <h3 class="text-xl font-bold mb-2">Hackathon 2024</h3>
                <p class="text-slate-500 mb-4">18-20 October 2024</p>

                <div class="flex justify-between items-center">
                    <span class="text-2xl font-black text-indigo-600">Gratis</span>
                    <a href="#" class="px-4 py-2 bg-indigo-100 text-indigo-600 rounded-xl">Detail</a>
                </div>
            </div>
        </div>

    </div>
</section>

@endsection