<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Retronics - Rental Kamera & Peralatan Fotografi Profesional</title>
    <meta name="description" content="Sewa kamera DSLR, mirrorless, lensa premium, lighting studio, dan stabilizer di Retronics. Unit siap pakai, sensor bersih, dan proses cepat.">

    <!-- Fonts: Plus Jakarta Sans & Space Grotesk -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'system-ui', 'sans-serif'],
                        display: ['"Space Grotesk"', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            200: '#bfdbfe',
                            500: '#2563eb',
                            600: '#1d4ed8',
                            700: '#1e40af',
                            800: '#1e3a8a',
                            900: '#0f172a',
                        }
                    }
                }
            }
        }
    </script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc;
            color: #0f172a;
        }

        .font-display {
            font-family: 'Space Grotesk', sans-serif;
        }

        /* Scroll Reveal Animations */
        .reveal-on-scroll {
            opacity: 0;
            transform: translateY(28px);
            transition: opacity 0.7s cubic-bezier(0.16, 1, 0.3, 1), transform 0.7s cubic-bezier(0.16, 1, 0.3, 1);
            will-change: opacity, transform;
        }

        .reveal-on-scroll.is-revealed {
            opacity: 1;
            transform: translateY(0);
        }

        .delay-100 { transition-delay: 100ms; }
        .delay-150 { transition-delay: 150ms; }
        .delay-200 { transition-delay: 200ms; }
        .delay-250 { transition-delay: 250ms; }
        .delay-300 { transition-delay: 300ms; }
        .delay-400 { transition-delay: 400ms; }

        @keyframes progressLine {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }
        .animate-progress {
            animation: progressLine 0.9s cubic-bezier(0.4, 0, 0.2, 1) infinite;
        }

        /* Viewfinder reticle subtle animation */
        @keyframes subtlePulse {
            0%, 100% { opacity: 0.8; }
            50% { opacity: 0.4; }
        }
        .animate-subtle-pulse {
            animation: subtlePulse 3s ease-in-out infinite;
        }
    </style>
</head>
<body class="antialiased selection:bg-brand-500 selection:text-white min-h-screen flex flex-col justify-between">

    <!-- ========================================================================= -->
    <!-- PRELOADER ANIMATION (Animasi Loading Masuk Sebelum Halaman Utama)           -->
    <!-- ========================================================================= -->
    <div id="retronics-loader" class="fixed inset-0 z-50 flex flex-col items-center justify-center bg-slate-950 text-white transition-opacity duration-600 ease-out">
        <div class="relative flex items-center justify-center mb-5">
            <div class="w-20 h-20 rounded-2xl bg-white p-2 shadow-2xl shadow-brand-500/30 flex items-center justify-center overflow-hidden border border-white/20">
                <img src="{{ asset('images/logo.jpg') }}" alt="Retronics Logo" class="w-full h-full object-contain">
            </div>
            <div class="absolute -inset-3 rounded-3xl border border-brand-500/30 animate-ping opacity-60"></div>
        </div>
        <span class="font-display font-black text-2xl tracking-tight text-white">Retronics</span>
        <span class="text-xs text-slate-400 mt-1 font-medium tracking-wide">Menyiapkan Katalog & Peralatan Produksi...</span>
        <div class="w-36 h-1.5 bg-slate-800 rounded-full mt-4 overflow-hidden relative">
            <div class="w-full h-full bg-brand-500 rounded-full animate-progress"></div>
        </div>
    </div>

    <script>
        window.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => {
                const loader = document.getElementById('retronics-loader');
                if (loader) {
                    loader.classList.add('opacity-0', 'pointer-events-none');
                    setTimeout(() => loader.remove(), 600);
                }
            }, 350);
        });
    </script>

    <!-- ========================================================================= -->
    <!-- HEADER & NAVBAR                                                           -->
    <!-- ========================================================================= -->
    <header class="bg-white/90 backdrop-blur-md border-b border-slate-200 sticky top-0 z-40 transition-all">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                
                <!-- Logo & Brand Identity -->
                <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                    <img src="{{ asset('images/logo.jpg') }}" alt="Retronics Logo"
                         class="h-11 w-auto rounded-xl border border-slate-200 bg-white p-1 object-contain shadow-sm group-hover:scale-105 transition duration-200">
                    <div>
                        <span class="font-display text-2xl font-black tracking-tight text-slate-900 block leading-none">
                            Retronics
                        </span>
                        <span class="text-[9px] uppercase tracking-widest font-bold text-brand-600 block mt-1">
                            Camera Rental Service
                        </span>
                    </div>
                </a>

                <!-- Nav Links Minimalist -->
                <nav class="hidden md:flex items-center gap-8 text-sm font-semibold text-slate-600">
                    <a href="{{ route('home') }}" class="text-brand-600 font-bold transition">Beranda</a>
                    <a href="{{ route('products.index') }}" class="hover:text-brand-600 transition">Katalog Gear</a>
                    <a href="#cara-sewa" class="hover:text-brand-600 transition">Alur Sewa</a>
                    <a href="#kalkulator" class="hover:text-brand-600 transition">Kalkulator</a>
                    <a href="#keunggulan" class="hover:text-brand-600 transition">Keunggulan</a>
                </nav>

                <!-- Actions: Cart & Auth -->
                <div class="flex items-center gap-3">
                    <!-- Cart Button with Count Badge -->
                    <a href="{{ route('cart.index') }}" class="relative p-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 transition flex items-center gap-2 border border-slate-200/60" title="Keranjang Sewa">
                        <span class="text-base">🛒</span>
                        <span class="text-xs font-bold hidden sm:inline">Keranjang</span>
                        @php
                            $cartCount = array_sum(array_column(session('cart', []), 'quantity'));
                        @endphp
                        @if ($cartCount > 0)
                            <span class="w-5 h-5 rounded-full bg-brand-600 text-white text-[10px] flex items-center justify-center font-black animate-pulse">
                                {{ $cartCount }}
                            </span>
                        @endif
                    </a>

                    @auth
                        @if (auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="px-3.5 py-2 text-xs font-bold rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 transition shadow-sm">
                                👑 Panel Admin
                            </a>
                        @else
                            <a href="{{ route('account.index') }}" class="px-3.5 py-2 text-xs font-bold rounded-xl bg-blue-50 text-brand-700 hover:bg-blue-100 transition border border-blue-200/60">
                                👤 Akun Saya
                            </a>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="px-3.5 py-2 text-xs font-semibold text-slate-700 hover:text-brand-600 transition">
                            Masuk
                        </a>
                        <a href="{{ route('register') }}" class="px-4 py-2 text-xs font-bold text-white bg-brand-600 hover:bg-brand-700 rounded-xl shadow-sm transition">
                            Daftar
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- ========================================================================= -->
    <!-- MAIN CONTENT                                                              -->
    <!-- ========================================================================= -->
    <main class="w-full flex-grow">

        <!-- 1. HERO SECTION DENGAN BACKGROUND GAMBAR & FRAMING VIEWFINDER -->
        <section class="relative overflow-hidden bg-slate-100 border-b border-slate-200/90">
            <!-- Background Image -->
            <div class="absolute inset-0 bg-cover bg-center transition-all duration-700"
                 style="background-image: url('{{ asset('images/hero-bg.jpg') }}');">
            </div>

            <!-- Soft Overlay with Subtle Radial Focus (Kamera sudut tetap terlihat, teks di tengah tetap 100% kontras & tajam) -->
            <div class="absolute inset-0 bg-gradient-to-r from-white/95 via-white/85 to-white/70 backdrop-blur-[1.5px]"></div>
            <div class="absolute inset-0 bg-gradient-to-b from-white/40 via-transparent to-slate-100/90"></div>

            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24">
                <div class="max-w-3xl">
                    
                    <!-- Viewfinder Focus Badge -->
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-blue-50 text-brand-700 border border-blue-200/80 mb-5 shadow-xs">
                        <span class="w-2 h-2 rounded-full bg-brand-600 animate-ping"></span>
                        <span>[ • READY TO SHOOT ] Rental Kamera & Gear Fotografi Surabaya</span>
                    </div>

                    <!-- Main Headline -->
                    <h1 class="text-3xl sm:text-5xl lg:text-6xl font-black text-slate-900 tracking-tight font-display leading-[1.12]">
                        Sewa Kamera & Peralatan Fotografi Profesional
                    </h1>

                    <!-- Clear Subtitle -->
                    <p class="mt-5 text-base sm:text-lg text-slate-700 leading-relaxed font-normal">
                        Pilihan terlengkap bodi kamera mirrorless & DSLR, lensa premium, tata cahaya studio, drone, dan gimbal stabilizer. Unit siap pakai, sensor bersih, dan verifikasi cepat tanpa birokrasi berbelit.
                    </p>

                    <!-- CTAs -->
                    <div class="mt-8 flex flex-wrap items-center gap-3">
                        <a href="{{ route('products.index') }}" class="px-6 py-3.5 rounded-xl font-bold text-sm text-white bg-brand-600 hover:bg-brand-700 shadow-lg shadow-brand-500/25 transition flex items-center gap-2">
                            <span>Jelajahi Katalog Peralatan</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </a>

                        <a href="{{ route('cart.index') }}" class="px-6 py-3.5 rounded-xl font-bold text-sm text-slate-800 bg-white/90 hover:bg-white border border-slate-300 shadow-sm transition backdrop-blur-sm">
                            Cek Keranjang Sewa
                        </a>
                    </div>

                    <!-- Key Stats Highlights -->
                    <div class="mt-10 pt-6 border-t border-slate-300/60 grid grid-cols-3 gap-4 sm:gap-8 max-w-lg">
                        <div>
                            <span class="block font-display font-extrabold text-2xl sm:text-3xl text-slate-900">{{ $stats['total_models'] }}+</span>
                            <span class="text-xs text-slate-600 font-medium">Model Gear Siap Sewa</span>
                        </div>
                        <div>
                            <span class="block font-display font-extrabold text-2xl sm:text-3xl text-brand-600">100%</span>
                            <span class="text-xs text-slate-600 font-medium">Sensor & Optik Bersih</span>
                        </div>
                        <div>
                            <span class="block font-display font-extrabold text-2xl sm:text-3xl text-emerald-600">4.9 ★</span>
                            <span class="text-xs text-slate-600 font-medium">Rating Pelanggan</span>
                        </div>
                    </div>

                    <!-- Category Quick Filter Pills -->
                    <div class="mt-8 flex flex-wrap items-center gap-2">
                        <span class="text-xs font-bold text-slate-500 mr-1">Kategori:</span>
                        @foreach ($categories as $cat)
                            <a href="{{ route('products.index', ['category' => $cat->name]) }}" class="px-3 py-1.5 rounded-lg bg-white/90 border border-slate-200/90 hover:border-brand-500 hover:text-brand-600 text-xs font-semibold text-slate-700 shadow-xs transition backdrop-blur-sm">
                                {{ $cat->name }} ({{ $cat->products_count }})
                            </a>
                        @endforeach
                    </div>

                </div>
            </div>
        </section>

        <!-- 2. ALUR 4 LANGKAH SEWA (FLOWCHART RESMI RETRONICS) -->
        <section id="cara-sewa" class="bg-white border-b border-slate-200/80 py-16 sm:py-20">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <div class="text-center max-w-2xl mx-auto mb-14 reveal-on-scroll">
                    <span class="text-xs font-bold uppercase tracking-widest text-brand-600 block mb-1.5">Alur Flowchart Sistem</span>
                    <h2 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-slate-900 font-display">
                        Cara Mudah Menyewa Peralatan di Retronics
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-500 mt-2">
                        Empat tahap sederhana dan transparan sesuai alur operasional resmi penyewaan kami.
                    </p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Step 1 -->
                    <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200/90 relative group hover:border-brand-400 hover:bg-white hover:shadow-lg transition duration-300 reveal-on-scroll delay-100">
                        <div class="flex items-center justify-between mb-4">
                            <span class="w-9 h-9 rounded-xl bg-brand-600 text-white font-black text-sm flex items-center justify-center shadow-md shadow-brand-500/20">
                                1
                            </span>
                            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Tahap 1</span>
                        </div>
                        <h3 class="text-base font-bold text-slate-900 group-hover:text-brand-600 transition">Pilih Gear & Unit</h3>
                        <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                            Telusuri katalog bodi kamera mirrorless/DSLR, lensa, lighting, atau stabilizer dan tentukan jumlah unit yang dibutuhkan.
                        </p>
                    </div>

                    <!-- Step 2 -->
                    <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200/90 relative group hover:border-brand-400 hover:bg-white hover:shadow-lg transition duration-300 reveal-on-scroll delay-200">
                        <div class="flex items-center justify-between mb-4">
                            <span class="w-9 h-9 rounded-xl bg-brand-600 text-white font-black text-sm flex items-center justify-center shadow-md shadow-brand-500/20">
                                2
                            </span>
                            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Tahap 2</span>
                        </div>
                        <h3 class="text-base font-bold text-slate-900 group-hover:text-brand-600 transition">Tentukan Tanggal & Keranjang</h3>
                        <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                            Masukkan ke keranjang sewa, pilih tanggal mulai serta durasi hari. Sistem menghitung rincian biaya sewa dan diskon durasi.
                        </p>
                    </div>

                    <!-- Step 3 -->
                    <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200/90 relative group hover:border-brand-400 hover:bg-white hover:shadow-lg transition duration-300 reveal-on-scroll delay-300">
                        <div class="flex items-center justify-between mb-4">
                            <span class="w-9 h-9 rounded-xl bg-brand-600 text-white font-black text-sm flex items-center justify-center shadow-md shadow-brand-500/20">
                                3
                            </span>
                            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Tahap 3</span>
                        </div>
                        <h3 class="text-base font-bold text-slate-900 group-hover:text-brand-600 transition">Bayar & Verifikasi</h3>
                        <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                            Kirim permintaan pemesanan, lakukan pembayaran via transfer/QRIS, lalu unggah bukti. Admin segera memverifikasi pesanan Anda.
                        </p>
                    </div>

                    <!-- Step 4 -->
                    <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200/90 relative group hover:border-brand-400 hover:bg-white hover:shadow-lg transition duration-300 reveal-on-scroll delay-400">
                        <div class="flex items-center justify-between mb-4">
                            <span class="w-9 h-9 rounded-xl bg-brand-600 text-white font-black text-sm flex items-center justify-center shadow-md shadow-brand-500/20">
                                4
                            </span>
                            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Tahap 4</span>
                        </div>
                        <h3 class="text-base font-bold text-slate-900 group-hover:text-brand-600 transition">Ambil & Produksi</h3>
                        <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                            Ambil gear di store kami pada hari-H. Gunakan unit untuk kebutuhan produksi Anda dan kembalikan unit tepat waktu.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- 3. SOROTAN KATALOG POPULER (FEATURED PRODUCTS) -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-20">
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-10 reveal-on-scroll">
                <div>
                    <span class="text-xs font-bold uppercase tracking-widest text-brand-600 block mb-1">Inventaris Pilihan</span>
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-display">
                        Peralatan Fotografi Populer
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1">
                        Peralatan terbaik yang paling sering disewa oleh fotografer, videografer, dan tim produksi.
                    </p>
                </div>

                <a href="{{ route('products.index') }}" class="text-xs sm:text-sm font-bold text-brand-600 hover:text-brand-700 flex items-center gap-1.5 transition group">
                    <span>Lihat Semua Katalog ({{ $stats['total_models'] }} Gear)</span>
                    <span class="group-hover:translate-x-1 transition-transform">→</span>
                </a>
            </div>

            <!-- Grid 4 Columns -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach ($featuredProducts as $index => $product)
                    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm hover:shadow-xl hover:border-brand-400 transition-all duration-300 flex flex-col justify-between overflow-hidden group reveal-on-scroll delay-{{ ($index % 4 + 1) * 100 }}">
                        <div>
                            <!-- Image Container -->
                            <div class="relative bg-slate-50 p-4 h-52 flex items-center justify-center overflow-hidden border-b border-slate-100">
                                <span class="absolute top-3 left-3 text-[10px] font-bold px-2 py-0.5 rounded-full bg-white/95 text-slate-700 shadow-xs border border-slate-200">
                                    {{ $product->category->name ?? 'Gear' }}
                                </span>

                                <span class="absolute top-3 right-3 text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    Stok: {{ $product->stock }}
                                </span>

                                @if ($product->image)
                                    <img src="{{ asset('storage/' . $product->image) }}"
                                         alt="{{ $product->name }}"
                                         class="max-h-40 max-w-full object-contain drop-shadow transition duration-300 group-hover:scale-105"
                                         onerror="this.onerror=null; this.src='{{ asset('images/products/' . $product->image) }}';">
                                @else
                                    <div class="w-16 h-16 rounded-2xl bg-white shadow-xs flex items-center justify-center text-3xl">
                                        📷
                                    </div>
                                @endif
                            </div>

                            <!-- Details -->
                            <div class="p-5">
                                <span class="text-[10px] font-semibold text-slate-400 block mb-1">
                                    Kondisi: <strong class="text-emerald-700 capitalize">{{ $product->condition }}</strong>
                                </span>

                                <a href="{{ route('products.show', $product->slug) }}">
                                    <h3 class="font-bold text-slate-900 text-sm group-hover:text-brand-600 transition line-clamp-1">
                                        {{ $product->name }}
                                    </h3>
                                </a>

                                <p class="text-xs text-slate-500 mt-1 line-clamp-2 leading-relaxed">
                                    {{ $product->description }}
                                </p>
                            </div>
                        </div>

                        <!-- Price & Action -->
                        <div class="p-5 pt-0">
                            <div class="pt-3 border-t border-slate-100 flex items-baseline justify-between mb-3">
                                <div>
                                    <span class="text-[10px] text-slate-400 block">Tarif Sewa</span>
                                    <span class="text-base font-bold text-brand-600 font-display">
                                        Rp{{ number_format($product->price_per_day, 0, ',', '.') }}
                                    </span>
                                    <span class="text-[10px] text-slate-400">/ hari</span>
                                </div>

                                <a href="{{ route('products.show', $product->slug) }}" class="text-xs font-semibold text-slate-500 hover:text-brand-600 underline">
                                    Detail
                                </a>
                            </div>

                            <form method="POST" action="{{ route('cart.add') }}">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <input type="hidden" name="quantity" value="1">
                                <input type="hidden" name="duration_days" value="1">
                                <button type="submit"
                                        class="w-full py-2.5 px-3 rounded-xl font-bold text-xs text-white bg-slate-900 hover:bg-brand-600 shadow-sm transition flex items-center justify-center gap-1.5">
                                    <span>+ Tambah ke Keranjang</span>
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        <!-- 4. KALKULATOR SEWA INTERAKTIF -->
        <section id="kalkulator" class="bg-slate-100/80 border-t border-slate-200 py-16 sm:py-20">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-10 reveal-on-scroll">
                    <span class="text-xs font-bold uppercase tracking-widest text-brand-600 block mb-1">Simulasi Biaya Cepat</span>
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-display">
                        Kalkulator Sewa Interaktif
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1">
                        Pilih perlengkapan dan tentukan durasi sewa untuk melihat rincian biaya seketika.
                    </p>
                </div>

                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-md reveal-on-scroll delay-100">
                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-6 items-center">
                        
                        <!-- Inputs -->
                        <div class="sm:col-span-6 space-y-5">
                            <div>
                                <label for="calc-product" class="block text-xs font-bold text-slate-700 uppercase mb-1.5">
                                    Pilih Peralatan
                                </label>
                                <select id="calc-product" class="w-full px-3.5 py-2.5 text-xs sm:text-sm rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500">
                                    @foreach ($products as $p)
                                        <option value="{{ $p->id }}" data-price="{{ $p->price_per_day }}" data-name="{{ $p->name }}">
                                            {{ $p->name }} (Rp{{ number_format($p->price_per_day, 0, ',', '.') }}/hari)
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="calc-days" class="block text-xs font-bold text-slate-700 uppercase mb-1.5">
                                    Durasi Sewa (Hari)
                                </label>
                                <div class="flex items-center gap-3">
                                    <input type="range" id="calc-days" min="1" max="14" value="2" class="w-full accent-brand-600 cursor-pointer">
                                    <span id="calc-days-label" class="w-16 text-center font-bold text-sm px-2.5 py-1.5 bg-slate-100 rounded-lg text-slate-800">
                                        2 Hari
                                    </span>
                                </div>
                                <span class="text-[10px] text-slate-400 block mt-1.5">Diskon durasi: &ge; 3 hari diskon 15%, &ge; 7 hari diskon 25%</span>
                            </div>
                        </div>

                        <!-- Calculation Output Box -->
                        <div class="sm:col-span-6 bg-slate-50 p-6 rounded-2xl border border-slate-200/90">
                            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1">Estimasi Total Biaya</span>
                            <div class="flex items-baseline gap-2">
                                <span id="calc-total" class="text-3xl font-black text-brand-600 font-display">
                                    Rp0
                                </span>
                            </div>
                            <span id="calc-discount" class="text-xs text-emerald-600 font-semibold block mt-1">
                                Diskon Durasi: Rp0
                            </span>

                            <div class="mt-5 pt-4 border-t border-slate-200">
                                <button type="button"
                                        id="calc-add-btn"
                                        class="w-full py-2.5 px-4 bg-brand-600 hover:bg-brand-700 text-white rounded-xl text-xs font-bold transition shadow-sm">
                                    Sewa Gear Ini Sekarang →
                                </button>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </section>

        <!-- Script Calculator -->
        <script>
            (function () {
                const productSelect = document.getElementById('calc-product');
                const daysRange = document.getElementById('calc-days');
                const daysLabel = document.getElementById('calc-days-label');
                const totalLabel = document.getElementById('calc-total');
                const discountLabel = document.getElementById('calc-discount');
                const addBtn = document.getElementById('calc-add-btn');

                function calculate() {
                    if (!productSelect || !productSelect.options.length) return;
                    const selected = productSelect.options[productSelect.selectedIndex];
                    const price = parseFloat(selected.getAttribute('data-price')) || 0;
                    const days = parseInt(daysRange.value) || 1;

                    daysLabel.textContent = days + ' Hari';

                    const gross = price * days;
                    let discountRate = 0;
                    if (days >= 7) discountRate = 0.25;
                    else if (days >= 3) discountRate = 0.15;
                    else if (days >= 2) discountRate = 0.05;

                    const discount = gross * discountRate;
                    const total = gross - discount;

                    totalLabel.textContent = 'Rp' + Math.round(total).toLocaleString('id-ID');
                    if (discountRate > 0) {
                        discountLabel.textContent = 'Hemat ' + (discountRate * 100) + '% (-Rp' + Math.round(discount).toLocaleString('id-ID') + ')';
                        discountLabel.classList.remove('hidden');
                    } else {
                        discountLabel.textContent = 'Tanpa potongan durasi';
                    }
                }

                if (productSelect && daysRange) {
                    productSelect.addEventListener('change', calculate);
                    daysRange.addEventListener('input', calculate);
                    calculate();
                }

                if (addBtn && productSelect) {
                    addBtn.addEventListener('click', () => {
                        const productId = productSelect.value;
                        const days = daysRange.value;
                        window.location.href = "{{ route('rentals.create') }}?product_id=" + productId + "&days=" + days;
                    });
                }
            })();
        </script>

        <!-- 5. KEUNGGULAN / STANDAR KUALITAS RETRONICS -->
        <section id="keunggulan" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-20">
            <div class="text-center max-w-xl mx-auto mb-12 reveal-on-scroll">
                <span class="text-xs font-bold uppercase tracking-widest text-brand-600 block mb-1">Standar Kualitas Retronics</span>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-display">
                    Gear Siap Pakai Untuk Produksi
                </h2>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">
                    Setiap unit kamera dan perlengkapan melalui uji fungsi menyeluruh sebelum diserahkan kepada penyewa.
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 text-center">
                <!-- Card 1 -->
                <div class="p-6 rounded-2xl bg-white border border-slate-200/90 shadow-sm hover:shadow-md transition reveal-on-scroll delay-100">
                    <span class="text-3xl block mb-3">✨</span>
                    <h3 class="font-bold text-slate-900 text-sm">Sensor & Lensa Bersih</h3>
                    <p class="text-xs text-slate-500 mt-1.5 leading-relaxed">
                        Sensor kamera dibersihkan rutin dari debu/jamur, dan optik lensa jernih untuk hasil foto dan video maksimal.
                    </p>
                </div>

                <!-- Card 2 -->
                <div class="p-6 rounded-2xl bg-white border border-slate-200/90 shadow-sm hover:shadow-md transition reveal-on-scroll delay-200">
                    <span class="text-3xl block mb-3">📋</span>
                    <h3 class="font-bold text-slate-900 text-sm">Syarat Sewa Transparan</h3>
                    <p class="text-xs text-slate-500 mt-1.5 leading-relaxed">
                        Verifikasi identitas KTP/KTM mahasiswa cepat, tanpa deposit berlebihan, dan alur konfirmasi transparan.
                    </p>
                </div>

                <!-- Card 3 -->
                <div class="p-6 rounded-2xl bg-white border border-slate-200/90 shadow-sm hover:shadow-md transition reveal-on-scroll delay-300">
                    <span class="text-3xl block mb-3">⚡</span>
                    <h3 class="font-bold text-slate-900 text-sm">Pengambilan Tepat Waktu</h3>
                    <p class="text-xs text-slate-500 mt-1.5 leading-relaxed">
                        Unit dipersiapkan dan diuji sebelum jadwal sewa dimulai sehingga jadwal pemotretan Anda berjalan tanpa hambatan.
                    </p>
                </div>
            </div>
        </section>

    </main>

    <!-- ========================================================================= -->
    <!-- FOOTER WITH RETRONICS BRANDING                                            -->
    <!-- ========================================================================= -->
    <footer class="bg-white border-t border-slate-200 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row items-center justify-between gap-6 pb-8 border-b border-slate-100">
                
                <!-- Logo & Short Bio -->
                <div class="flex items-center gap-3">
                    <img src="{{ asset('images/logo.jpg') }}" alt="Retronics Logo" class="h-10 w-auto rounded-xl border border-slate-200 bg-white p-1 object-contain">
                    <div>
                        <span class="font-display font-black text-slate-900 text-base block leading-none">Retronics</span>
                        <span class="text-[11px] text-slate-500 block mt-1">Rental Kamera & Studio Fotografi Profesional di Surabaya</span>
                    </div>
                </div>

                <!-- Footer Navigation -->
                <div class="flex flex-wrap items-center gap-6 text-xs font-semibold text-slate-600">
                    <a href="{{ route('products.index') }}" class="hover:text-brand-600 transition">Katalog Gear</a>
                    <a href="{{ route('cart.index') }}" class="hover:text-brand-600 transition">Keranjang</a>
                    <a href="{{ route('login') }}" class="hover:text-brand-600 transition">Login Pelanggan</a>
                    <a href="{{ route('admin.login') }}" class="hover:text-brand-600 transition">Portal Khusus Admin</a>
                </div>

            </div>

            <div class="pt-6 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-400">
                <p>&copy; 2026 Retronics Camera Rental Service. Seluruh hak cipta dilindungi.</p>
                <p>Jam Operasional: Senin - Minggu (08.00 - 21.00 WIB)</p>
            </div>
        </div>
    </footer>

    <!-- ========================================================================= -->
    <!-- SCRIPT ANIMASI PUDAR MASUK KETIKA SCROLL (INTERSECTION OBSERVER)           -->
    <!-- ========================================================================= -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const reveals = document.querySelectorAll('.reveal-on-scroll');
            if ('IntersectionObserver' in window) {
                const observer = new IntersectionObserver((entries, obs) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('is-revealed');
                            obs.unobserve(entry.target);
                        }
                    });
                }, {
                    threshold: 0.1,
                    rootMargin: '0px 0px -30px 0px'
                });

                reveals.forEach(el => observer.observe(el));
            } else {
                // Fallback jika browser lawas
                reveals.forEach(el => el.classList.add('is-revealed'));
            }
        });
    </script>

</body>
</html>
