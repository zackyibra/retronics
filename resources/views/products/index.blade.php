<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog Peralatan Fotografi | Retronics</title>
    <meta name="description" content="Katalog sewa kamera DSLR, mirrorless, lensa premium, lighting, dan aksesori fotografi di Retronics.">

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
<body class="bg-slate-50 text-slate-800 antialiased min-h-screen flex flex-col justify-between">

    <!-- Navbar -->
    <header class="bg-white border-b border-slate-200/90 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <a href="{{ route('home') }}" class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-brand-600 text-white flex items-center justify-center font-bold text-xl shadow-md shadow-brand-500/20">
                        📷
                    </div>
                    <div>
                        <span class="font-display text-2xl font-bold tracking-tight text-slate-900 block">Retronics</span>
                        <span class="text-[10px] uppercase tracking-widest font-semibold text-slate-500 -mt-1 block">Camera Rental</span>
                    </div>
                </a>

                <nav class="hidden md:flex items-center gap-6 text-sm font-semibold text-slate-600">
                    <a href="{{ route('home') }}" class="hover:text-brand-600 transition">Beranda</a>
                    <a href="{{ route('products.index') }}" class="text-brand-600">Katalog Barang</a>
                    <a href="{{ route('home') }}#keunggulan" class="hover:text-brand-600 transition">Keunggulan</a>
                    <a href="{{ route('home') }}#cara-sewa" class="hover:text-brand-600 transition">Cara Sewa</a>
                    <a href="{{ route('home') }}#faq" class="hover:text-brand-600 transition">FAQ</a>
                </nav>

                <div class="flex items-center gap-3">
                    <a href="{{ route('cart.index') }}" class="relative p-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 transition flex items-center gap-1.5" title="Keranjang Sewa">
                        <span class="text-base">🛒</span>
                        <span class="text-xs font-bold hidden sm:inline">Keranjang</span>
                        @php
                            $cartCount = array_sum(array_column(session('cart', []), 'quantity'));
                        @endphp
                        @if ($cartCount > 0)
                            <span class="w-5 h-5 rounded-full bg-brand-600 text-white text-[10px] flex items-center justify-center font-black">
                                {{ $cartCount }}
                            </span>
                        @endif
                    </a>

                    @auth
                        @if (auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="px-3.5 py-2 text-xs font-bold text-amber-700 bg-amber-100 hover:bg-amber-200 rounded-xl transition">
                                👑 Dashboard Admin
                            </a>
                        @endif
                        <a href="{{ route('account.wishlist') }}" title="Wishlist Favorit" class="p-2 text-slate-600 hover:text-rose-600 rounded-xl hover:bg-slate-100 transition relative">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                        </a>
                        <a href="{{ route('account.index') }}" class="px-4 py-2 text-xs font-bold text-brand-600 bg-blue-50 hover:bg-blue-100 rounded-xl transition">
                            👤 Akun Saya ({{ auth()->user()->name }})
                        </a>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="px-3 py-2 text-xs font-semibold text-slate-500 hover:text-slate-800 transition">
                                Keluar
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="px-4 py-2 text-xs font-semibold text-slate-700 hover:text-brand-600 transition">
                            Masuk
                        </a>
                        <a href="{{ route('register') }}" class="px-4 py-2 text-xs font-bold text-white bg-brand-600 hover:bg-brand-700 rounded-xl shadow-sm transition">
                            Daftar Akun
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 w-full flex-grow">

        <!-- Page Header -->
        <div class="mb-8">
            <span class="text-xs font-bold uppercase tracking-widest text-brand-600 bg-blue-50 px-3 py-1 rounded-full">
                Inventaris Lengkap
            </span>
            <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 font-display mt-2">
                Katalog Peralatan Fotografi & Kamera
            </h1>
            <p class="text-slate-500 text-sm mt-1">
                Pilih gear yang Anda butuhkan, periksa spesifikasi, dan lakukan pemesanan secara langsung.
            </p>
        </div>

        @if (session('success'))
            <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm flex items-center justify-between">
                <span>✓ {{ session('success') }}</span>
                <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900">✕</button>
            </div>
        @endif

        <!-- Filter & Search Toolbar -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm mb-8">
            <form method="GET" action="{{ route('products.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-4 items-center">
                <!-- Search Input -->
                <div class="sm:col-span-5 relative">
                    <input type="text"
                           name="q"
                           value="{{ request('q') }}"
                           placeholder="Cari kamera, lensa, lampu, mic..."
                           class="w-full pl-10 pr-4 py-2.5 text-xs sm:text-sm rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>

                <!-- Category Select -->
                <div class="sm:col-span-4">
                    <select name="category" class="w-full px-3 py-2.5 text-xs sm:text-sm rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500">
                        <option value="">Semua Kategori</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->name }}" {{ request('category') == $cat->name ? 'selected' : '' }}>
                                {{ $cat->name }} ({{ $cat->products_count }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Sort Select -->
                <div class="sm:col-span-2">
                    <select name="sort" class="w-full px-3 py-2.5 text-xs sm:text-sm rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500">
                        <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Urut Nama</option>
                        <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Tarif Terendah</option>
                        <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Tarif Tertinggi</option>
                        <option value="stock" {{ request('sort') == 'stock' ? 'selected' : '' }}>Stok Terbanyak</option>
                    </select>
                </div>

                <!-- Submit Button -->
                <div class="sm:col-span-1">
                    <button type="submit" class="w-full py-2.5 px-3 bg-brand-600 hover:bg-brand-700 text-white rounded-xl text-xs font-bold transition flex items-center justify-center">
                        Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- Product Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @forelse ($products as $product)
                <div class="group bg-white rounded-2xl border border-slate-200/90 shadow-sm hover:shadow-xl hover:border-brand-300 transition duration-300 flex flex-col justify-between overflow-hidden">
                    <div>
                        <!-- Image Container -->
                        <div class="relative bg-gradient-to-b from-slate-50 to-blue-50/40 p-6 h-52 flex items-center justify-center overflow-hidden">
                            <span class="absolute top-3 left-3 text-[11px] font-bold px-2.5 py-0.5 rounded-full bg-white/90 backdrop-blur shadow-sm text-brand-700 border border-slate-100">
                                {{ $product->category->name }}
                            </span>

                            @if ($product->stock > 0)
                                <span class="absolute top-3 right-3 text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    Stok: {{ $product->stock }}
                                </span>
                            @else
                                <span class="absolute top-3 right-3 text-[10px] font-bold px-2 py-0.5 rounded-full bg-rose-50 text-rose-700 border border-rose-200">
                                    Habis
                                </span>
                            @endif

                            @if ($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}"
                                     alt="{{ $product->name }}"
                                     class="max-h-40 max-w-full object-contain group-hover:scale-105 transition duration-300 drop-shadow"
                                     onerror="this.onerror=null; this.src='{{ asset('images/products/' . $product->image) }}';">
                            @else
                                <div class="text-center p-4">
                                    <div class="w-16 h-16 rounded-2xl bg-white shadow-sm border border-slate-100 flex items-center justify-center text-3xl mx-auto mb-2">
                                        📷
                                    </div>
                                    <span class="text-[10px] text-slate-400 font-medium">Ready Stock</span>
                                </div>
                            @endif
                        </div>

                        <!-- Info -->
                        <div class="p-5">
                            <div class="flex items-center justify-between text-[11px] text-slate-500 mb-1">
                                <span>Kondisi: <strong class="text-emerald-700">{{ ucfirst($product->condition) }}</strong></span>
                                <!-- Wishlist Button -->
                                @auth
                                    <form method="POST" action="{{ route('wishlist.toggle', $product->id) }}" class="inline">
                                        @csrf
                                        <button type="submit" title="Simpan ke Wishlist" class="text-slate-400 hover:text-rose-500 transition">
                                            @if (in_array($product->id, $wishlistIds))
                                                <svg class="w-4 h-4 text-rose-500 fill-current" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                                            @else
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                                            @endif
                                        </button>
                                    </form>
                                @endauth
                            </div>

                            <a href="{{ route('products.show', $product->slug) }}">
                                <h3 class="font-bold text-slate-900 text-base group-hover:text-brand-600 transition line-clamp-1">
                                    {{ $product->name }}
                                </h3>
                            </a>

                            <p class="text-xs text-slate-500 mt-1 line-clamp-2 leading-relaxed">
                                {{ $product->description ?? 'Peralatan fotografi original dan siap diajak produksi.' }}
                            </p>
                        </div>
                    </div>

                    <!-- Footer & Actions -->
                    <div class="p-5 pt-0">
                        <div class="pt-3 border-t border-slate-100 flex items-baseline justify-between mb-4">
                            <div>
                                <span class="text-[10px] text-slate-400 block font-medium">Tarif Sewa</span>
                                <span class="text-lg font-black text-brand-600 font-display">
                                    Rp{{ number_format($product->price_per_day, 0, ',', '.') }}
                                </span>
                                <span class="text-[10px] text-slate-500">/ hari</span>
                            </div>

                            <a href="{{ route('products.show', $product->slug) }}" class="text-xs font-semibold text-slate-600 hover:text-brand-600 underline">
                                Lihat Detail
                            </a>
                        </div>

                        <div class="grid grid-cols-2 gap-2">
                            <a href="{{ route('products.show', $product->slug) }}"
                               class="w-full py-2 px-2 text-xs font-bold text-center text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-xl transition">
                                Rincian
                            </a>

                            @if ($product->stock > 0)
                                <form method="POST" action="{{ route('cart.add') }}">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                    <input type="hidden" name="quantity" value="1">
                                    <input type="hidden" name="duration_days" value="1">
                                    <button type="submit"
                                            class="w-full py-2 px-2 text-xs font-bold text-center text-white bg-brand-600 hover:bg-brand-700 rounded-xl transition shadow-sm">
                                        + Keranjang
                                    </button>
                                </form>
                            @else
                                <button disabled class="w-full py-2 px-2 text-xs font-semibold text-center text-slate-400 bg-slate-100 rounded-xl cursor-not-allowed">
                                    Habis
                                </button>
                            @endif
                        </div>
                    </div>

                </div>
            @empty
                <div class="col-span-full py-16 text-center bg-white rounded-2xl border border-dashed border-slate-300">
                    <span class="text-5xl block mb-3">🔍</span>
                    <h3 class="text-base font-bold text-slate-800">Tidak ada barang yang cocok</h3>
                    <p class="text-slate-500 text-xs mt-1">Coba gunakan kata kunci lain atau reset filter kategori.</p>
                    <a href="{{ route('products.index') }}" class="inline-block mt-4 px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition">
                        Reset Filter
                    </a>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="mt-10">
            {{ $products->links() }}
        </div>

    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 py-6 text-center text-xs text-slate-500">
        &copy; {{ date('Y') }} Retronics. Rental Perlengkapan Fotografi & Kamera Profesional.
    </footer>

</body>
</html>
