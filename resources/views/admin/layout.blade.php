<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Ringkasan operasional') | Retronics Admin</title>

    <!-- Favicon / Logo Ikon Retronics -->
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Space+Grotesk:wght@700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
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
                        },
                        sidebar: {
                            900: '#0b1320',
                            800: '#111c2e',
                            700: '#1e293b'
                        }
                    }
                }
            }
        }
    </script>
    <style>
        .custom-sidebar-scroll::-webkit-scrollbar {
            width: 5px;
        }
        .custom-sidebar-scroll::-webkit-scrollbar-track {
            background: #0b1320;
        }
        .custom-sidebar-scroll::-webkit-scrollbar-thumb {
            background: #1e293b;
            border-radius: 4px;
        }
        .custom-sidebar-scroll::-webkit-scrollbar-thumb:hover {
            background: #334155;
        }
        .custom-sidebar-scroll {
            scrollbar-width: thin;
            scrollbar-color: #1e293b #0b1320;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased min-h-screen flex" x-data="{ mobileMenuOpen: false }">

    @php
        $pendingVerificationsCount = \App\Models\Rental::where('status', 'waiting_verification')->count();
        $pendingDocVerificationsCount = \App\Models\RentalVerification::where('verification_status', \App\Models\RentalVerification::STATUS_MENUNGGU)->count();
        $totalRentalAlerts = $pendingVerificationsCount + $pendingDocVerificationsCount;
        $pendingReturnsCount = \App\Models\Rental::where('status', 'in_use')->count();
        $pendingPaymentsCount = \App\Models\Payment::where('status', \App\Models\Payment::STATUS_WAITING_VERIFICATION)->count();
    @endphp

    <!-- Fixed Dark Navy Left Sidebar (Matching Figma Screenshot) -->
    <aside class="w-64 bg-[#0b1320] text-slate-300 flex-shrink-0 flex flex-col justify-between h-screen fixed left-0 top-0 bottom-0 z-40 hidden lg:flex border-r border-slate-900">
        <div class="flex-1 overflow-y-auto overscroll-contain custom-sidebar-scroll">
            <!-- Brand Logo -->
            <div class="p-6 sticky top-0 bg-[#0b1320] z-10">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
                    <img src="{{ asset('images/logo.jpg') }}" alt="Logo Retronics" class="w-9 h-9 object-contain rounded-xl border border-slate-700 bg-white shadow-md">
                    <div>
                        <span class="font-display text-xl font-bold tracking-tight text-white block">retronics</span>
                    </div>
                </a>
            </div>

            <!-- Section Label -->
            <div class="px-6 pb-2 text-[10px] font-bold uppercase tracking-wider text-slate-500">
                RUANG KERJA ADMIN
            </div>

            <!-- Navigation Links -->
            <nav class="px-3 pb-6 space-y-1 text-xs font-semibold">
                <a href="{{ route('admin.dashboard') }}"
                   class="flex items-center px-4 py-3 rounded-xl transition {{ request()->routeIs('admin.dashboard') ? 'bg-brand-600 text-white font-bold shadow-md shadow-brand-600/30' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                    <span>Dashboard</span>
                </a>

                <a href="{{ route('admin.categories.index') }}"
                   class="flex items-center px-4 py-3 rounded-xl transition {{ request()->routeIs('admin.categories.*') ? 'bg-brand-600 text-white font-bold shadow-md shadow-brand-600/30' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                    <span>Kategori</span>
                </a>

                <a href="{{ route('admin.products.index') }}"
                   class="flex items-center px-4 py-3 rounded-xl transition {{ request()->routeIs('admin.products.*') ? 'bg-brand-600 text-white font-bold shadow-md shadow-brand-600/30' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                    <span>Barang</span>
                </a>

                <a href="{{ route('admin.rentals.index') }}"
                   class="flex items-center justify-between px-4 py-3 rounded-xl transition {{ request()->routeIs('admin.rentals.*') ? 'bg-brand-600 text-white font-bold shadow-md shadow-brand-600/30' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                    <span>Penyewaan</span>
                    @if($totalRentalAlerts > 0)
                        <span class="px-1.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/20 text-amber-300">
                            {{ $totalRentalAlerts }}
                        </span>
                    @endif
                </a>

                <a href="{{ route('admin.payments.index') }}"
                   class="flex items-center justify-between px-4 py-3 rounded-xl transition {{ request()->routeIs('admin.payments.*') ? 'bg-brand-600 text-white font-bold shadow-md shadow-brand-600/30' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                    <span>Pembayaran</span>
                    @if($pendingPaymentsCount > 0)
                        <span class="px-1.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-500/20 text-blue-300">
                            {{ $pendingPaymentsCount }}
                        </span>
                    @endif
                </a>

                <a href="{{ route('admin.returns.index') }}"
                   class="flex items-center justify-between px-4 py-3 rounded-xl transition {{ request()->routeIs('admin.returns.*') ? 'bg-brand-600 text-white font-bold shadow-md shadow-brand-600/30' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                    <span>Pengembalian & Denda</span>
                    @if($pendingReturnsCount > 0)
                        <span class="px-1.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-500/20 text-rose-300">
                            {{ $pendingReturnsCount }}
                        </span>
                    @endif
                </a>

                <a href="{{ route('admin.promotions.index') }}"
                   class="flex items-center px-4 py-3 rounded-xl transition {{ request()->routeIs('admin.promotions.*') ? 'bg-brand-600 text-white font-bold shadow-md shadow-brand-600/30' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                    <span>Kelola Promo</span>
                </a>

                <a href="{{ route('admin.customers.index') }}"
                   class="flex items-center px-4 py-3 rounded-xl transition {{ request()->routeIs('admin.customers.*') ? 'bg-brand-600 text-white font-bold shadow-md shadow-brand-600/30' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                    <span>Pelanggan</span>
                </a>

                <a href="{{ route('admin.reviews.index') }}"
                   class="flex items-center px-4 py-3 rounded-xl transition {{ request()->routeIs('admin.reviews.*') ? 'bg-brand-600 text-white font-bold shadow-md shadow-brand-600/30' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                    <span>Ulasan Pelanggan</span>
                </a>

                <a href="{{ route('admin.reports.index') }}"
                   class="flex items-center px-4 py-3 rounded-xl transition {{ request()->routeIs('admin.reports.*') ? 'bg-brand-600 text-white font-bold shadow-md shadow-brand-600/30' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                    <span>Laporan Bisnis</span>
                </a>

                <a href="{{ route('admin.qris.index') }}"
                   class="flex items-center px-4 py-3 rounded-xl transition {{ request()->routeIs('admin.qris.*') ? 'bg-brand-600 text-white font-bold shadow-md shadow-brand-600/30' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                    <span>Pengaturan QRIS</span>
                </a>

                <a href="{{ route('admin.files.audit_logs') }}"
                   class="flex items-center px-4 py-3 rounded-xl transition {{ request()->routeIs('admin.files.audit_logs') ? 'bg-brand-600 text-white font-bold shadow-md shadow-brand-600/30' : 'text-slate-400 hover:text-white hover:bg-white/5' }}">
                    <span>Audit Log Berkas</span>
                </a>
            </nav>
        </div>

        <!-- Bottom Actions (Matching Screenshot) -->
        <div class="p-4 border-t border-slate-900 space-y-1 text-xs shrink-0 bg-[#0b1320] z-20">
            <a href="{{ route('home') }}"
               target="_blank"
               class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-slate-400 hover:text-white hover:bg-white/5 transition">
                <span>↗ Lihat website publik</span>
            </a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="w-full text-left px-4 py-2.5 rounded-xl text-slate-400 hover:text-rose-400 hover:bg-rose-500/10 transition">
                    Keluar dari admin
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0 lg:pl-64">

        <!-- Top Header Bar -->
        <header class="h-16 bg-white border-b border-slate-200 sticky top-0 z-30 flex items-center justify-between px-4 sm:px-8">
            <div class="flex items-center gap-3">
                <!-- Mobile toggle button -->
                <button type="button"
                        @click="mobileMenuOpen = !mobileMenuOpen"
                        class="lg:hidden p-2 rounded-xl text-slate-600 hover:bg-slate-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>

                <div class="text-xs text-slate-500 font-medium">
                    <span class="text-slate-900 font-bold">Retronics</span> / Administrasi · Store Gunung Putri, Bogor
                </div>
            </div>

            <div class="flex items-center gap-4 text-xs">
                <span class="text-slate-500 hidden sm:inline">
                    {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
                </span>
                <span class="text-slate-300 hidden sm:inline">·</span>
                <div class="flex items-center gap-2 font-bold text-slate-800">
                    <span>Admin Store</span>
                    <span class="text-slate-400">⌄</span>
                </div>
            </div>
        </header>

        <!-- Mobile Drawer -->
        <div x-show="mobileMenuOpen"
             class="lg:hidden fixed inset-0 z-50 bg-black/50"
             @click="mobileMenuOpen = false"
             style="display: none;">
            <div class="w-64 bg-[#0b1320] h-full p-6 text-slate-300 flex flex-col justify-between" @click.stop>
                <div class="flex-1 overflow-y-auto overscroll-contain custom-sidebar-scroll space-y-4 pr-1">
                    <div class="flex items-center justify-between pb-2">
                        <span class="font-display font-bold text-white text-lg">retronics</span>
                        <button type="button" @click="mobileMenuOpen = false" class="text-slate-400 hover:text-white text-lg">✕</button>
                    </div>
                    <nav class="space-y-1 text-xs font-semibold">
                        <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2 rounded-lg text-slate-300 hover:text-white hover:bg-white/5">Dashboard</a>
                        <a href="{{ route('admin.categories.index') }}" class="block px-3 py-2 rounded-lg text-slate-300 hover:text-white hover:bg-white/5">Kategori</a>
                        <a href="{{ route('admin.products.index') }}" class="block px-3 py-2 rounded-lg text-slate-300 hover:text-white hover:bg-white/5">Barang</a>
                        <a href="{{ route('admin.rentals.index') }}" class="flex items-center justify-between px-3 py-2 rounded-lg text-slate-300 hover:text-white hover:bg-white/5">
                            <span>Penyewaan</span>
                            @if($totalRentalAlerts > 0)
                                <span class="px-1.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/20 text-amber-300">
                                    {{ $totalRentalAlerts }}
                                </span>
                            @endif
                        </a>
                        <a href="{{ route('admin.payments.index') }}" class="block px-3 py-2 rounded-lg text-slate-300 hover:text-white hover:bg-white/5">Pembayaran</a>
                        <a href="{{ route('admin.returns.index') }}" class="block px-3 py-2 rounded-lg text-slate-300 hover:text-white hover:bg-white/5">Pengembalian & Denda</a>
                        <a href="{{ route('admin.promotions.index') }}" class="block px-3 py-2 rounded-lg text-slate-300 hover:text-white hover:bg-white/5">Kelola Promo</a>
                        <a href="{{ route('admin.customers.index') }}" class="block px-3 py-2 rounded-lg text-slate-300 hover:text-white hover:bg-white/5">Pelanggan</a>
                        <a href="{{ route('admin.reviews.index') }}" class="block px-3 py-2 rounded-lg text-slate-300 hover:text-white hover:bg-white/5">Ulasan Pelanggan</a>
                        <a href="{{ route('admin.reports.index') }}" class="block px-3 py-2 rounded-lg text-slate-300 hover:text-white hover:bg-white/5">Laporan Bisnis</a>
                        <a href="{{ route('admin.qris.index') }}" class="block px-3 py-2 rounded-lg text-slate-300 hover:text-white hover:bg-white/5">Pengaturan QRIS</a>
                        <a href="{{ route('admin.files.audit_logs') }}" class="block px-3 py-2 rounded-lg text-slate-300 hover:text-white hover:bg-white/5">Audit Log Berkas</a>
                    </nav>
                </div>

                <div class="pt-4 border-t border-slate-900 space-y-1 text-xs shrink-0">
                    <a href="{{ route('home') }}"
                       target="_blank"
                       class="flex items-center gap-2 px-3 py-2 rounded-lg text-slate-400 hover:text-white hover:bg-white/5 transition">
                        <span>↗ Lihat website publik</span>
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                class="w-full text-left px-3 py-2 rounded-lg text-slate-400 hover:text-rose-400 hover:bg-rose-500/10 transition">
                            Keluar dari admin
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Main Body -->
        <main class="flex-1 p-4 sm:p-8 max-w-7xl w-full">
            @if (session('success'))
                <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm flex items-center justify-between">
                    <span class="font-medium">✓ {{ session('success') }}</span>
                    <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900 font-bold">✕</button>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-xs sm:text-sm flex items-center justify-between">
                    <span class="font-medium">⚠️ {{ session('error') }}</span>
                    <button type="button" onclick="this.parentElement.remove()" class="text-rose-600 hover:text-rose-900 font-bold">✕</button>
                </div>
            @endif

            @yield('content')
        </main>

        <footer class="p-6 text-center text-xs text-slate-400 border-t border-slate-200 bg-white">
            Retronics – Rental Fotografi · Backoffice Gunung Putri, Bogor · Data operasional store
        </footer>

    </div>

</body>
</html>
