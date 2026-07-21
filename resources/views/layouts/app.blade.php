<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AmikomEventHub - Temukan Event Seru!</title>

    {{-- ================================================================
         PWA (Progressive Web App)
         ================================================================ --}}
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#4f46e5">
    <link rel="apple-touch-icon" href="/icons/icon-192.png">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="EventHub">

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .glass { background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(10px); }
    </style>
</head>
<body class="bg-slate-50 text-slate-900">

    <nav class="glass sticky top-0 z-40 px-8 py-4 border-b border-white/20 shadow-sm flex items-center justify-between">
        <div class="flex items-center gap-2">
            <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center text-white font-bold text-xl">AH</div>
            <span class="text-xl font-bold tracking-tight">AmikomEventHub</span>
        </div>

        <div class="hidden md:flex items-center gap-8 font-medium">
            <a href="{{ route('home') }}" class="text-indigo-600">Jelajahi</a>
            <a href="{{ route('categories.index') }}" class="hover:text-indigo-600 transition">Kategori</a>
            <a href="#" class="hover:text-indigo-600 transition">Tentang Kami</a>
            <a href="{{ route('organizer.register') }}" class="text-sm text-slate-500 hover:text-indigo-600 transition">
                Jadi Penyelenggara
            </a>

            @auth
            {{-- Dropdown akun: klik avatar untuk buka menu Tiket Saya / Ganti Akun / Logout --}}
            <div class="relative">
                <button type="button" onclick="document.getElementById('user-menu').classList.toggle('hidden')"
                        class="flex items-center gap-2 pl-3 border-l border-slate-200">
                    <img src="{{ auth()->user()->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name) }}"
                         alt="avatar" class="w-8 h-8 rounded-full border" title="{{ auth()->user()->email }}">
                    <span class="text-sm font-medium">{{ auth()->user()->name }}</span>
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <div id="user-menu" class="hidden absolute right-0 mt-3 w-52 bg-white rounded-2xl border border-slate-100 shadow-xl py-2 z-50">
                    <a href="{{ route('tickets.my') }}" class="block px-4 py-2.5 text-sm hover:bg-slate-50 transition">
                        🎟️ Tiket Saya
                    </a>
                    <a href="{{ route('buyer.google.redirect', ['redirect_to' => url()->current()]) }}"
                       class="block px-4 py-2.5 text-sm hover:bg-slate-50 transition">
                        🔄 Ganti Akun
                    </a>
                    <div class="border-t border-slate-100 my-1"></div>
                    <form method="POST" action="{{ route('buyer.logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-2.5 text-sm text-rose-600 hover:bg-rose-50 transition">
                            🚪 Logout
                        </button>
                    </form>
                </div>
            </div>
            @endauth
        </div>
    </nav>

    <main>
        @yield('content')
    </main>

    <footer class="bg-indigo-900 text-indigo-100 py-20 px-6 mt-20">
        <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-4 gap-12">
            <div class="space-y-4 col-span-2">
                <div class="flex items-center gap-2">
                    <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center text-indigo-900 font-bold text-xl">AH</div>
                    <span class="text-2xl font-bold text-white">AmikomEventHub</span>
                </div>
                <p class="max-w-xs text-indigo-300">Platform reservasi tiket event online terbaik untuk mahasiswa dan penyelenggara profesional.</p>
            </div>
            <div>
                <h4 class="text-white font-bold mb-6">Navigasi</h4>
                <ul class="space-y-4">
                    <li><a href="/" class="hover:text-white transition">Home</a></li>
                    <li><a href="#" class="hover:text-white transition">Semua Event</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-white font-bold mb-6">Hubungi Kami</h4>
                <ul class="space-y-4">
                    <li>support@eventtiket.com</li>
                    <li>+62 812 3456 7890</li>
                </ul>
            </div>
        </div>
        <div class="max-w-7xl mx-auto pt-12 mt-12 border-t border-indigo-800 text-center text-indigo-400 text-sm">
            &copy; 2024 AmikomEventHub. Built with Laravel & Tailwind CSS.
        </div>
    </footer>

    {{-- Tutup dropdown akun kalau klik di luar area menu --}}
    <script>
        document.addEventListener('click', function (event) {
            const menu = document.getElementById('user-menu');
            if (!menu) return;
            const button = event.target.closest('button');
            const isToggleButton = button && button.getAttribute('onclick')?.includes('user-menu');
            if (!isToggleButton && !menu.contains(event.target)) {
                menu.classList.add('hidden');
            }
        });
    </script>

    {{-- Registrasi Service Worker untuk fitur PWA (install ke homescreen,
         loading lebih cepat via cache, fallback halaman offline) --}}
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function () {
                navigator.serviceWorker.register('/service-worker.js')
                    .catch(function (err) {
                        console.log('Service Worker gagal didaftarkan:', err);
                    });
            });
        }
    </script>

</body>
</html>