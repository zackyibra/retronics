<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Dashboard') | Retronics Admin</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Space+Grotesk:wght@700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        display: ['"Space Grotesk"', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            500: '#2563eb',
                            600: '#1d4ed8',
                            700: '#1e40af',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-slate-100 text-slate-800 antialiased min-h-screen flex flex-col">

    <!-- Top Admin Header -->
    <header class="bg-slate-900 text-white sticky top-0 z-40 border-b border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Brand -->
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-brand-600 flex items-center justify-center font-bold text-lg">
                        📷
                    </div>
                    <div>
                        <span class="font-display font-bold text-lg text-white">Retronics Admin</span>
                        <span class="text-[10px] text-amber-400 font-bold uppercase tracking-wider block -mt-1">Penyewaan Fotografi</span>
                    </div>
                </div>

                <!-- Admin Navigation -->
                <nav class="hidden md:flex items-center gap-1 text-xs font-bold">
                    <a href="{{ route('admin.dashboard') }}" class="px-3 py-2 rounded-lg {{ request()->routeIs('admin.dashboard') ? 'bg-slate-800 text-white' : 'text-slate-400 hover:text-white' }}">
                        Dashboard
                    </a>
                    <a href="{{ route('admin.categories.index') }}" class="px-3 py-2 rounded-lg {{ request()->routeIs('admin.categories.*') ? 'bg-slate-800 text-white' : 'text-slate-400 hover:text-white' }}">
                        1. Kategori
                    </a>
                    <a href="{{ route('admin.products.index') }}" class="px-3 py-2 rounded-lg {{ request()->routeIs('admin.products.*') ? 'bg-slate-800 text-white' : 'text-slate-400 hover:text-white' }}">
                        2. Barang
                    </a>
                    <a href="{{ route('admin.rentals.index') }}" class="px-3 py-2 rounded-lg {{ request()->routeIs('admin.rentals.*') ? 'bg-slate-800 text-white' : 'text-slate-400 hover:text-white' }}">
                        3. Penyewaan
                    </a>
                    <a href="{{ route('admin.returns.index') }}" class="px-3 py-2 rounded-lg {{ request()->routeIs('admin.returns.*') ? 'bg-slate-800 text-white' : 'text-slate-400 hover:text-white' }}">
                        4. Pengembalian
                    </a>
                    <a href="{{ route('admin.customers.index') }}" class="px-3 py-2 rounded-lg {{ request()->routeIs('admin.customers.*') ? 'bg-slate-800 text-white' : 'text-slate-400 hover:text-white' }}">
                        Pelanggan
                    </a>
                    <a href="{{ route('admin.reports.index') }}" class="px-3 py-2 rounded-lg {{ request()->routeIs('admin.reports.*') ? 'bg-slate-800 text-white' : 'text-slate-400 hover:text-white' }}">
                        Laporan
                    </a>
                    <a href="{{ route('admin.reviews.index') }}" class="px-3 py-2 rounded-lg {{ request()->routeIs('admin.reviews.*') ? 'bg-slate-800 text-white' : 'text-slate-400 hover:text-white' }}">
                        Ulasan
                    </a>
                    <a href="{{ route('admin.settings.index') }}" class="px-3 py-2 rounded-lg {{ request()->routeIs('admin.settings.*') ? 'bg-slate-800 text-white' : 'text-slate-400 hover:text-white' }}">
                        Pengaturan
                    </a>
                </nav>

                <!-- User & Exit -->
                <div class="flex items-center gap-3 text-xs">
                    <a href="{{ route('home') }}" class="px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold transition">
                        🌐 Buka Web
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="px-3 py-1.5 rounded-lg bg-rose-600/20 text-rose-300 hover:bg-rose-600 hover:text-white font-semibold transition">
                            Keluar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <!-- Subnav on Mobile -->
    <div class="md:hidden bg-slate-800 px-4 py-2 flex items-center gap-2 overflow-x-auto text-xs font-bold text-slate-300 border-b border-slate-700">
        <a href="{{ route('admin.dashboard') }}" class="whitespace-nowrap px-2 py-1 rounded {{ request()->routeIs('admin.dashboard') ? 'bg-brand-600 text-white' : '' }}">Dashboard</a>
        <a href="{{ route('admin.categories.index') }}" class="whitespace-nowrap px-2 py-1 rounded {{ request()->routeIs('admin.categories.*') ? 'bg-brand-600 text-white' : '' }}">1. Kategori</a>
        <a href="{{ route('admin.products.index') }}" class="whitespace-nowrap px-2 py-1 rounded {{ request()->routeIs('admin.products.*') ? 'bg-brand-600 text-white' : '' }}">2. Barang</a>
        <a href="{{ route('admin.rentals.index') }}" class="whitespace-nowrap px-2 py-1 rounded {{ request()->routeIs('admin.rentals.*') ? 'bg-brand-600 text-white' : '' }}">3. Penyewaan</a>
        <a href="{{ route('admin.returns.index') }}" class="whitespace-nowrap px-2 py-1 rounded {{ request()->routeIs('admin.returns.*') ? 'bg-brand-600 text-white' : '' }}">4. Pengembalian</a>
        <a href="{{ route('admin.customers.index') }}" class="whitespace-nowrap px-2 py-1 rounded {{ request()->routeIs('admin.customers.*') ? 'bg-brand-600 text-white' : '' }}">Pelanggan</a>
        <a href="{{ route('admin.reports.index') }}" class="whitespace-nowrap px-2 py-1 rounded {{ request()->routeIs('admin.reports.*') ? 'bg-brand-600 text-white' : '' }}">Laporan</a>
        <a href="{{ route('admin.reviews.index') }}" class="whitespace-nowrap px-2 py-1 rounded {{ request()->routeIs('admin.reviews.*') ? 'bg-brand-600 text-white' : '' }}">Ulasan</a>
        <a href="{{ route('admin.settings.index') }}" class="whitespace-nowrap px-2 py-1 rounded {{ request()->routeIs('admin.settings.*') ? 'bg-brand-600 text-white' : '' }}">Pengaturan</a>
    </div>

    <!-- Main Content Area -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full flex-grow">
        @if (session('success'))
            <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm flex items-center justify-between shadow-sm">
                <span>✓ {{ session('success') }}</span>
                <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900 font-bold">✕</button>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs sm:text-sm flex items-center justify-between shadow-sm">
                <span>⚠️ {{ session('error') }}</span>
                <button type="button" onclick="this.parentElement.remove()" class="text-rose-600 hover:text-rose-900 font-bold">✕</button>
            </div>
        @endif

        @if (session('info'))
            <div class="mb-6 p-4 rounded-2xl bg-blue-50 border border-blue-200 text-blue-800 text-xs sm:text-sm shadow-sm">
                ℹ️ {{ session('info') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs sm:text-sm shadow-sm">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="bg-white border-t border-slate-200 py-4 text-center text-xs text-slate-500">
        Panel Admin Retronics • Berdasarkan Flowchart Alur Sistem Penyewaan
    </footer>

</body>
</html>
