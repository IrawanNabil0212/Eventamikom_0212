<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog - Amikom Event Hub</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 font-sans text-slate-800">

    <nav class="bg-indigo-600 shadow-lg">
        <div class="max-w-6xl mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <div class="text-white font-bold text-xl tracking-wider">
                    🎉 Amikom<span class="text-indigo-200">EventHub</span>
                </div>
                <div class="flex space-x-2">
                    <a href="/" class="text-indigo-100 hover:bg-indigo-500 hover:text-white px-4 py-2 rounded-md font-medium transition">Home</a>
                    <a href="/profil" class="text-indigo-100 hover:bg-indigo-500 hover:text-white px-4 py-2 rounded-md font-medium transition">Profil</a>
                    <a href="/katalog" class="bg-indigo-800 text-white px-4 py-2 rounded-md font-medium transition">Katalog</a>
                    <a href="/bantuan" class="text-indigo-100 hover:bg-indigo-500 hover:text-white px-4 py-2 rounded-md font-medium transition">Bantuan</a>
                    <a href="/kontak" class="text-indigo-100 hover:bg-indigo-500 hover:text-white px-4 py-2 rounded-md font-medium transition">Kontak</a>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-6xl mx-auto mt-12 p-6">
        <div class="mb-10 text-center">
            <h1 class="text-4xl font-extrabold text-slate-800 mb-3">Katalog Event</h1>
            <p class="text-slate-500 text-lg">Temukan dan ikuti event seru di kampus Amikom!</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-xl shadow-md hover:shadow-xl transition-shadow duration-300 border border-slate-100 overflow-hidden group">
                <div class="h-32 bg-indigo-500 flex items-center justify-center text-4xl group-hover:scale-105 transition-transform">🚀</div>
                <div class="p-6">
                    <span class="bg-indigo-100 text-indigo-800 text-xs font-bold uppercase px-3 py-1 rounded-full mb-3 inline-block">Seminar</span>
                    <h3 class="font-bold text-xl text-slate-800 mb-2">Tech Talk: Web 3.0</h3>
                    <p class="text-slate-500 text-sm mb-4">Mengenal lebih dalam teknologi desentralisasi dan masa depan internet.</p>
                    <div class="flex items-center text-sm text-slate-400">
                        <span>📅 20 April 2026</span>
                        <span class="mx-2">•</span>
                        <span>📍 Ruang Citra</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-md hover:shadow-xl transition-shadow duration-300 border border-slate-100 overflow-hidden group">
                <div class="h-32 bg-emerald-500 flex items-center justify-center text-4xl group-hover:scale-105 transition-transform">🎨</div>
                <div class="p-6">
                    <span class="bg-emerald-100 text-emerald-800 text-xs font-bold uppercase px-3 py-1 rounded-full mb-3 inline-block">Workshop</span>
                    <h3 class="font-bold text-xl text-slate-800 mb-2">Desain UI/UX</h3>
                    <p class="text-slate-500 text-sm mb-4">Praktik langsung membuat wireframe dan prototipe menggunakan Figma.</p>
                    <div class="flex items-center text-sm text-slate-400">
                        <span>📅 25 April 2026</span>
                        <span class="mx-2">•</span>
                        <span>📍 Lab Komputer 2</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-md hover:shadow-xl transition-shadow duration-300 border border-slate-100 overflow-hidden group">
                <div class="h-32 bg-rose-500 flex items-center justify-center text-4xl group-hover:scale-105 transition-transform">🏆</div>
                <div class="p-6">
                    <span class="bg-rose-100 text-rose-800 text-xs font-bold uppercase px-3 py-1 rounded-full mb-3 inline-block">Kompetisi</span>
                    <h3 class="font-bold text-xl text-slate-800 mb-2">Hackathon Mahasiswa</h3>
                    <p class="text-slate-500 text-sm mb-4">Buat solusi inovatif dalam 24 jam dan menangkan total hadiah jutaan rupiah!</p>
                    <div class="flex items-center text-sm text-slate-400">
                        <span>📅 10 Mei 2026</span>
                        <span class="mx-2">•</span>
                        <span>📍 Gedung Innovation</span>
                    </div>
                </div>
            </div>
        </div>
    </main>

</body>
</html>