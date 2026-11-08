<header class="bg-white/95 backdrop-blur-md border-b border-slate-200 sticky top-0 z-40 transition-all">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-20">
            
            <!-- Logo & Brand Identity (Matching Design) -->
            <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                <img src="{{ asset('images/logo.jpg') }}" alt="Logo Retronics" class="w-10 h-10 object-contain rounded-xl border border-slate-200 bg-white group-hover:scale-105 transition shadow-xs">
                <div>
                    <span class="font-display text-2xl font-black tracking-tight text-slate-900 block leading-none">
                        retronics
                    </span>
                    <span class="text-[9px] uppercase tracking-widest font-bold text-slate-500 block mt-1">
                        Camera Rental Service
                    </span>
                </div>
            </a>

            <!-- Desktop Nav Links (Simplified: Beranda, Katalog Gear, Promo) -->
            <nav class="hidden md:flex items-center gap-8 text-sm font-semibold">
                <a href="{{ route('home') }}"
                   class="{{ request()->routeIs('home') ? 'text-brand-600 font-bold' : 'text-slate-600 hover:text-brand-600 font-semibold' }} transition">
                    Beranda
                </a>
                <a href="{{ route('products.index') }}"
                   class="{{ request()->routeIs('products.*') ? 'text-brand-600 font-bold' : 'text-slate-600 hover:text-brand-600 font-semibold' }} transition">
                    Katalog Gear
                </a>
                <a href="{{ route('promo') }}"
                   class="{{ request()->routeIs('promo') || request()->is('promo') ? 'text-brand-600 font-bold' : 'text-slate-600 hover:text-brand-600 font-semibold' }} transition flex items-center gap-1.5">
                    <span>Promo</span>
                    <span class="px-1.5 py-0.2 rounded-full bg-rose-100 text-rose-700 text-[10px] font-black uppercase tracking-wider">Hot</span>
                </a>
            </nav>

            <!-- Actions: Cart, Wishlist, Auth & Mobile Trigger -->
            <div class="flex items-center gap-3">
                
                @auth
                    @if (auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}"
                           class="hidden sm:inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs transition shadow-xs"
                           title="Kembali ke Dashboard Administrator">
                            <span>👑 Dashboard Admin</span>
                        </a>
                    @endif
                @endauth

                <!-- Cart Button with Count Badge -->
                @php
                    $cartCount = array_sum(array_column(session('cart', []), 'quantity'));
                @endphp
                <a href="{{ route('cart.index') }}"
                   class="relative px-3.5 py-2 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-700 transition flex items-center gap-2 border border-slate-200 text-xs font-semibold"
                   title="Keranjang Sewa">
                    <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <span class="hidden sm:inline">Keranjang · {{ $cartCount > 0 ? $cartCount . ' unit' : '0 unit' }}</span>
                </a>

                <!-- Wishlist (Auth Only) -->
                @auth
                    <a href="{{ route('account.wishlist') }}"
                       class="p-2 rounded-xl bg-slate-50 hover:bg-rose-50 hover:text-rose-600 text-slate-600 transition border border-slate-200 hidden sm:flex items-center"
                       title="Wishlist Favorit">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                    </a>
                @endauth

                <!-- Auth Controls -->
                @auth
                    <!-- User Dropdown Menu (Alpine.js) matching 'Akun Saya ▾' in design -->
                    <div class="relative" x-data="{ open: false }">
                        <button type="button"
                                @click="open = !open"
                                @click.away="open = false"
                                class="px-4 py-2 rounded-xl bg-white text-slate-800 hover:bg-slate-50 transition border border-slate-200 text-xs font-bold flex items-center gap-2 shadow-xs">
                            <span>Akun Saya</span>
                            <svg class="w-3.5 h-3.5 text-slate-500 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>

                        <div x-show="open"
                             x-cloak
                             x-transition
                             class="absolute right-0 mt-2 w-56 bg-white rounded-2xl shadow-xl border border-slate-100 py-2 z-50">
                            
                            <div class="px-4 py-2 border-b border-slate-100">
                                <span class="text-[10px] font-bold text-slate-400 block uppercase tracking-wider">Penyewa</span>
                                <span class="text-xs font-bold text-slate-800 block truncate">{{ auth()->user()->name }}</span>
                                <span class="text-[10px] text-slate-500 block truncate">{{ auth()->user()->email }}</span>
                            </div>

                            <a href="{{ route('account.index') }}"
                               class="flex items-center gap-2.5 px-4 py-2.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 hover:text-brand-600 transition">
                                <span>⚙️ Profil & Password</span>
                            </a>

                            <a href="{{ route('account.rentals') }}"
                               class="flex items-center gap-2.5 px-4 py-2.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 hover:text-brand-600 transition">
                                <span>📦 Riwayat Sewa</span>
                            </a>

                            <a href="{{ route('account.wishlist') }}"
                               class="flex items-center gap-2.5 px-4 py-2.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 hover:text-brand-600 transition">
                                <span>❤️ Wishlist Gear</span>
                            </a>

                            @if (auth()->user()->isAdmin())
                                <div class="my-1 border-t border-slate-100"></div>
                                <a href="{{ route('admin.dashboard') }}"
                                   class="flex items-center gap-2.5 px-4 py-2 text-xs font-bold text-amber-700 bg-amber-50 hover:bg-amber-100 transition">
                                    <span>👑 Portal Dashboard Admin</span>
                                </a>
                            @endif

                            <div class="my-1 border-t border-slate-100"></div>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                        class="w-full flex items-center gap-2.5 px-4 py-2.5 text-xs font-bold text-rose-600 hover:bg-rose-50 transition text-left">
                                    <span>Keluar (Logout)</span>
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <!-- Guest Links matching design: Masuk, Daftar (pill solid blue) -->
                    <a href="{{ route('login') }}" class="px-3.5 py-2 text-xs font-semibold text-slate-700 hover:text-brand-600 transition hidden sm:inline-block">
                        Masuk
                    </a>
                    <a href="{{ route('register') }}" class="px-5 py-2 text-xs font-bold text-white bg-brand-600 hover:bg-brand-700 rounded-full shadow-xs transition hidden sm:inline-block">
                        Daftar
                    </a>
                @endauth

                <!-- Mobile Hamburger Button -->
                <button type="button"
                        @click="mobileMenuOpen = !mobileMenuOpen"
                        class="md:hidden p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 transition"
                        aria-label="Toggle navigation menu">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>

            </div>
        </div>
    </div>

    <!-- Mobile Slide-Over Drawer Navigation -->
    <div x-show="mobileMenuOpen"
         x-cloak
         class="fixed inset-0 z-50 md:hidden"
         aria-modal="true">
        
        <!-- Backdrop Blur -->
        <div x-show="mobileMenuOpen"
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="mobileMenuOpen = false"
             class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs"></div>

        <!-- Off-canvas panel -->
        <div x-show="mobileMenuOpen"
             x-transition:enter="transition ease-in-out duration-300 transform"
             x-transition:enter-start="translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in-out duration-300 transform"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="translate-x-full"
             class="fixed inset-y-0 right-0 max-w-xs w-full bg-white shadow-2xl p-6 flex flex-col justify-between overflow-y-auto">
            
            <div>
                <!-- Drawer Header -->
                <div class="flex items-center justify-between pb-5 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <span class="font-display font-black text-xl text-slate-900">retronics</span>
                    </div>
                    <button type="button"
                            @click="mobileMenuOpen = false"
                            class="p-2 rounded-xl text-slate-400 hover:text-slate-700 bg-slate-100">
                        ✕
                    </button>
                </div>

                <!-- Navigation Links (Simplified: Beranda, Katalog Gear, Promo) -->
                <nav class="mt-6 space-y-1.5 text-sm font-bold text-slate-700">
                    <a href="{{ route('home') }}"
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl hover:bg-slate-100 {{ request()->routeIs('home') ? 'bg-blue-50 text-brand-600 font-bold' : '' }}">
                        <span>Beranda</span>
                    </a>
                    <a href="{{ route('products.index') }}"
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl hover:bg-slate-100 {{ request()->routeIs('products.*') ? 'bg-blue-50 text-brand-600 font-bold' : '' }}">
                        <span>Katalog Gear</span>
                    </a>
                    <a href="{{ route('promo') }}"
                       class="flex items-center justify-between px-3.5 py-2.5 rounded-xl hover:bg-slate-100 {{ request()->routeIs('promo') || request()->is('promo') ? 'bg-blue-50 text-brand-600 font-bold' : '' }}">
                        <span>Promo Spesial</span>
                        <span class="px-2 py-0.5 rounded-full bg-rose-100 text-rose-700 text-[10px] font-black uppercase">Hot</span>
                    </a>
                    <a href="{{ route('cart.index') }}"
                       class="flex items-center justify-between px-3.5 py-2.5 rounded-xl hover:bg-slate-100 {{ request()->routeIs('cart.*') ? 'bg-blue-50 text-brand-600 font-bold' : '' }}">
                        <span>Keranjang Sewa</span>
                        <span class="px-2 py-0.5 rounded-full bg-brand-600 text-white text-[10px] font-black">
                            {{ $cartCount }}
                        </span>
                    </a>

                    @auth
                        <a href="{{ route('account.rentals') }}"
                           class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl hover:bg-slate-100 {{ request()->routeIs('account.rentals') ? 'bg-blue-50 text-brand-600 font-bold' : '' }}">
                            <span>Riwayat Sewa</span>
                        </a>
                        <a href="{{ route('account.wishlist') }}"
                           class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl hover:bg-slate-100 {{ request()->routeIs('account.wishlist') ? 'bg-blue-50 text-brand-600 font-bold' : '' }}">
                            <span>Wishlist Favorit</span>
                        </a>
                        <a href="{{ route('account.index') }}"
                           class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl hover:bg-slate-100 {{ request()->routeIs('account.index') ? 'bg-blue-50 text-brand-600 font-bold' : '' }}">
                            <span>Profil & Password</span>
                        </a>
                        @if (auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}"
                               class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl bg-amber-50 text-amber-800 font-bold border border-amber-200 mt-2">
                                <span>👑 Dashboard Admin</span>
                            </a>
                        @endif
                    @endauth
                </nav>
            </div>

            <!-- Drawer Footer / Auth Action -->
            <div class="pt-6 border-t border-slate-100">
                @auth
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                class="w-full py-2.5 px-4 rounded-xl bg-rose-50 text-rose-700 hover:bg-rose-100 font-bold text-xs transition flex items-center justify-center gap-2">
                            <span>Keluar dari Akun</span>
                        </button>
                    </form>
                @else
                    <div class="grid grid-cols-2 gap-3">
                        <a href="{{ route('login') }}"
                           class="py-2.5 text-center text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-xl transition">
                            Masuk
                        </a>
                        <a href="{{ route('register') }}"
                           class="py-2.5 text-center text-xs font-bold text-white bg-brand-600 hover:bg-brand-700 rounded-full shadow-xs transition">
                            Daftar
                        </a>
                    </div>
                @endauth
            </div>

        </div>
    </div>
</header>
