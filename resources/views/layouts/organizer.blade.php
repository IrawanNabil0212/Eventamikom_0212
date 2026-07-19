<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Dashboard Penyelenggara' }} - AmikomEventHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Plus Jakarta Sans', sans-serif; }</style>
</head>
<body class="bg-slate-50 text-slate-900 flex min-h-screen">

    <aside class="w-64 bg-emerald-900 text-emerald-100 flex flex-col p-6 space-y-8 sticky top-0 h-screen">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center text-emerald-900 font-bold text-xl">
                {{ strtoupper(substr(auth()->user()->organization->name ?? 'OR', 0, 2)) }}
            </div>
            <span class="text-lg font-bold text-white tracking-tight leading-tight">
                {{ auth()->user()->organization->name ?? 'Penyelenggara' }}
            </span>
        </div>

        <nav class="flex-1 space-y-2">
            <p class="text-[10px] font-bold uppercase tracking-widest text-emerald-400 mb-4 px-2">Main Menu</p>

            <a href="{{ route('organizer.dashboard') }}"
               class="flex items-center gap-3 px-4 py-3 rounded-xl font-bold transition
                      {{ request()->routeIs('organizer.dashboard') ? 'bg-emerald-800 text-white shadow-sm' : 'hover:bg-emerald-800/50 hover:text-white text-emerald-200' }}">
                Dashboard
            </a>

            <a href="{{ route('organizer.events.index') }}"
               class="flex items-center gap-3 px-4 py-3 rounded-xl font-bold transition
                      {{ request()->routeIs('organizer.events.*') ? 'bg-emerald-800 text-white shadow-sm' : 'hover:bg-emerald-800/50 hover:text-white text-emerald-200' }}">
                Kelola Event
            </a>
        </nav>

        <div class="pt-6 border-t border-emerald-800/50">
            <form action="{{ route('organizer.logout') }}" method="POST">
                @csrf
                <button type="submit"
                    class="w-full flex items-center gap-3 px-4 py-3 text-emerald-300 hover:text-white hover:bg-rose-500/10 hover:text-rose-400 rounded-xl transition font-medium text-left">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                        </path>
                    </svg>
                    Keluar
                </button>
            </form>
        </div>
    </aside>

    <main class="flex-1 p-10 overflow-y-auto">
        <div class="mb-8">
            <h1 class="text-3xl font-extrabold text-slate-800">@yield('page_title', 'Dashboard')</h1>
            <p class="text-slate-500 mt-1">@yield('page_subtitle', 'Kelola acara Anda di sini.')</p>
        </div>

        @if(session('success'))
        <div class="mb-6 p-4 bg-green-100 text-green-700 rounded-xl">
            {{ session('success') }}
        </div>
        @endif

        @yield('content')
    </main>

</body>
</html>