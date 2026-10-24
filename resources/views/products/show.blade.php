<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $product->name }} | Detail Barang Retronics</title>
    <meta name="description" content="Detail dan spesifikasi sewa {{ $product->name }} di Retronics. Kondisi {{ $product->condition }}, harga Rp{{ number_format($product->price_per_day, 0, ',', '.') }}/hari.">

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
                    <div class="w-10 h-10 rounded-xl bg-brand-600 text-white flex items-center justify-center font-bold text-xl shadow-md">
                        📷
                    </div>
                    <div>
                        <span class="font-display text-2xl font-bold tracking-tight text-slate-900 block">Retronics</span>
                        <span class="text-[10px] uppercase tracking-widest font-semibold text-slate-500 -mt-1 block">Camera Rental</span>
                    </div>
                </a>

                <div class="flex items-center gap-3 text-xs font-semibold">
                    <a href="{{ route('products.index') }}" class="text-slate-600 hover:text-brand-600 transition flex items-center gap-1">
                        ← Katalog
                    </a>

                    <a href="{{ route('cart.index') }}" class="relative p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 transition flex items-center gap-1.5" title="Keranjang">
                        <span>🛒</span>
                        <span class="font-bold hidden sm:inline">Keranjang</span>
                        @php
                            $cartCount = array_sum(array_column(session('cart', []), 'quantity'));
                        @endphp
                        @if ($cartCount > 0)
                            <span class="w-4 h-4 rounded-full bg-brand-600 text-white text-[9px] flex items-center justify-center font-bold">
                                {{ $cartCount }}
                            </span>
                        @endif
                    </a>

                    @auth
                        <a href="{{ route('account.index') }}" class="px-3 py-1.5 rounded-xl bg-blue-50 text-brand-600 font-bold">
                            Akun Saya
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="px-3 py-1.5 rounded-xl bg-brand-600 text-white font-bold">
                            Masuk
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 w-full flex-grow">

        <!-- Breadcrumb -->
        <nav class="flex items-center gap-2 text-xs text-slate-500 mb-6">
            <a href="{{ route('home') }}" class="hover:text-brand-600">Beranda</a>
            <span>/</span>
            <a href="{{ route('products.index') }}" class="hover:text-brand-600">Katalog</a>
            <span>/</span>
            <span class="text-slate-900 font-semibold">{{ $product->name }}</span>
        </nav>

        @if (session('success'))
            <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm flex items-center justify-between">
                <span>✓ {{ session('success') }}</span>
                <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900">✕</button>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

            <!-- Left: Product Image & Badges (7 cols) -->
            <div class="lg:col-span-7 bg-white rounded-3xl p-8 border border-slate-200 shadow-sm">
                <div class="bg-gradient-to-b from-slate-50 to-blue-50/50 rounded-2xl p-8 h-80 sm:h-96 flex items-center justify-center relative overflow-hidden mb-6">
                    <span class="absolute top-4 left-4 text-xs font-bold px-3 py-1 rounded-full bg-white text-brand-700 shadow-sm border border-slate-100">
                        {{ $product->category->name }}
                    </span>

                    @if ($product->stock > 0)
                        <span class="absolute top-4 right-4 text-xs font-bold px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            Tersedia ({{ $product->stock }} unit)
                        </span>
                    @else
                        <span class="absolute top-4 right-4 text-xs font-bold px-3 py-1 rounded-full bg-rose-50 text-rose-700 border border-rose-200">
                            Habis Tersewa
                        </span>
                    @endif

                    @if ($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}"
                             alt="{{ $product->name }}"
                             class="max-h-72 max-w-full object-contain drop-shadow-xl"
                             onerror="this.onerror=null; this.src='{{ asset('images/products/' . $product->image) }}';">
                    @else
                        <div class="text-8xl">📷</div>
                    @endif
                </div>

                <!-- Product Details Tabs -->
                <div>
                    <h2 class="text-xl font-bold text-slate-900 mb-3">Deskripsi & Kelengkapan</h2>
                    <p class="text-slate-600 text-sm leading-relaxed mb-6">
                        {{ $product->description ?? 'Peralatan fotografi dan produksi original dengan standar perawatan tinggi. Sensor terkalibrasi bersih, fungsi autofokus akurat, dan baterai terisi penuh siap pakai.' }}
                    </p>

                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 border-t border-slate-100 pt-6">
                        <div class="p-3 bg-slate-50 rounded-xl">
                            <span class="text-[10px] text-slate-400 block font-bold uppercase">Kondisi Alat</span>
                            <span class="text-xs font-bold text-slate-800">{{ ucfirst($product->condition) }}</span>
                        </div>
                        <div class="p-3 bg-slate-50 rounded-xl">
                            <span class="text-[10px] text-slate-400 block font-bold uppercase">Kategori</span>
                            <span class="text-xs font-bold text-slate-800">{{ $product->category->name }}</span>
                        </div>
                        <div class="p-3 bg-slate-50 rounded-xl">
                            <span class="text-[10px] text-slate-400 block font-bold uppercase">Jaminan Unit</span>
                            <span class="text-xs font-bold text-emerald-600">Garansi Tukar 100%</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Booking Form & Pricing Action (5 cols) -->
            <div class="lg:col-span-5 space-y-6">
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
                        <span class="text-xs uppercase font-bold tracking-wider text-slate-400">Tarif Sewa Harian</span>
                        @auth
                            <form method="POST" action="{{ route('wishlist.toggle', $product->id) }}">
                                @csrf
                                <button type="submit" class="flex items-center gap-1.5 text-xs font-semibold text-slate-600 hover:text-rose-600 transition">
                                    @if ($isWishlisted)
                                        <svg class="w-4 h-4 text-rose-500 fill-current" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                                        <span class="text-rose-600">Favorit</span>
                                    @else
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                                        <span>Simpan</span>
                                    @endif
                                </button>
                            </form>
                        @endauth
                    </div>

                    <h1 class="text-2xl font-black text-slate-900 font-display mb-1">{{ $product->name }}</h1>
                    <div class="text-3xl font-black text-brand-600 font-display mb-6">
                        Rp{{ number_format($product->price_per_day, 0, ',', '.') }}
                        <span class="text-xs text-slate-500 font-normal">/ 24 Jam</span>
                    </div>

                    <!-- Flowchart Step: Masukkan Tanggal dan Durasi Sewa -->
                    <form method="GET" action="{{ route('rentals.create') }}" class="space-y-4">
                        <input type="hidden" name="product_id" value="{{ $product->id }}">

                        <div>
                            <label for="start_date" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Tanggal Mulai Sewa
                            </label>
                            <input type="date"
                                   id="start_date"
                                   name="start_date"
                                   value="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                   min="{{ date('Y-m-d') }}"
                                   required
                                   class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                        </div>

                        <div>
                            <label for="days" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Durasi Sewa
                            </label>
                            <select id="days" name="days" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                                <option value="1">1 Hari (Tarif Normal)</option>
                                <option value="2">2 Hari (Hemat 5%)</option>
                                <option value="3">3 Hari (Hemat 15% - Promo)</option>
                                <option value="7">7 Hari (Hemat 25% - Mingguan)</option>
                            </select>
                        </div>

                        @if ($product->stock > 0)
                            <div class="space-y-2 pt-2">
                                <button type="submit"
                                        formaction="{{ route('cart.add') }}"
                                        formmethod="POST"
                                        class="w-full py-3 px-4 rounded-xl font-bold text-xs sm:text-sm text-brand-700 bg-brand-50 hover:bg-brand-100 border border-brand-200 transition flex items-center justify-center gap-2">
                                    <span>🛒 Masukkan ke Keranjang Sewa</span>
                                </button>

                                <button type="submit"
                                        class="w-full py-3.5 px-4 rounded-xl font-bold text-sm text-white bg-brand-600 hover:bg-brand-700 shadow-lg shadow-brand-500/25 transition flex items-center justify-center gap-2">
                                    <span>Lanjut ke Proses Pemesanan (Checkout)</span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                </button>
                            </div>
                        @else
                            <button disabled class="w-full mt-4 py-3.5 px-4 rounded-xl font-bold text-sm text-slate-400 bg-slate-100 cursor-not-allowed">
                                Stok Saat Ini Sedang Habis
                            </button>
                        @endif
                    </form>

                    <!-- WhatsApp Hotline Option -->
                    <div class="mt-4 pt-4 border-t border-slate-100 text-center">
                        <a href="https://wa.me/6281234567890?text=Halo%20Retronics%2C%20saya%20ingin%20tanya%20sewa%20{{ urlencode($product->name) }}"
                           target="_blank"
                           class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 flex items-center justify-center gap-1.5">
                            <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.711 2.598 2.669-.699c.969.586 1.838.88 2.791.88h.005c3.18 0 5.767-2.586 5.768-5.766 0-3.18-2.587-5.766-5.773-5.766zm8.814 5.765c0 4.87-3.953 8.823-8.82 8.823-1.503 0-2.915-.382-4.15-1.047l-4.575 1.199 1.226-4.464c-.75-1.309-1.196-2.825-1.196-4.511 0-4.87 3.953-8.824 8.82-8.824 4.869 0 8.82 3.954 8.82 8.824z"/></svg>
                            <span>Konsultasi Produk via WhatsApp Admin</span>
                        </a>
                    </div>
                </div>

                <!-- Periksa Rincian Jaminan Card -->
                <div class="bg-blue-50/60 rounded-2xl p-5 border border-blue-100 text-xs text-slate-600 space-y-2">
                    <div class="flex items-center gap-2 font-bold text-brand-700">
                        <span>🛡️</span>
                        <span>Fasilitas Sewa Retronics:</span>
                    </div>
                    <ul class="list-disc list-inside space-y-1 text-slate-600 pl-1">
                        <li>Gratis Tas Kamera & Lens Hood</li>
                        <li>Baterai Full Charge + Charger Unit</li>
                        <li>Verifikasi Dokumen Cepat KTP / KTM</li>
                        <li>Pilihan Ambil di Store atau Kirim ke Lokasi</li>
                    </ul>
                </div>
            </div>

        </div>

        {{-- ============================================================
             SECTION ULASAN PELANGGAN
             ============================================================ --}}
        <div class="mt-10">
            <h2 class="text-xl font-black text-slate-900 font-display mb-6 flex items-center gap-2">
                ⭐ Ulasan Pelanggan
                @if ($reviews->count() > 0)
                    <span class="text-sm font-normal text-slate-500">
                        ({{ $reviews->count() }} ulasan · rata-rata
                        <span class="font-bold text-amber-500">{{ number_format($averageRating, 1) }}</span>/5)
                    </span>
                @endif
            </h2>

            {{-- Form Kirim Ulasan --}}
            @auth
                @if ($canReview)
                    <div class="bg-blue-50 border border-blue-100 rounded-2xl p-6 mb-8">
                        <h3 class="font-bold text-slate-800 mb-4 text-sm">✍️ Tulis Ulasan Anda</h3>
                        <form method="POST" action="{{ route('reviews.store', $product->id) }}">
                            @csrf

                            {{-- Rating Bintang --}}
                            <div class="mb-4">
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Rating</label>
                                <div class="flex items-center gap-1" id="star-rating">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <label class="cursor-pointer">
                                            <input type="radio" name="rating" value="{{ $i }}" class="sr-only" required>
                                            <span class="text-3xl text-slate-300 hover:text-amber-400 transition star-btn" data-value="{{ $i }}">★</span>
                                        </label>
                                    @endfor
                                </div>
                                @error('rating')
                                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Komentar --}}
                            <div class="mb-4">
                                <label for="comment" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Komentar</label>
                                <textarea id="comment"
                                          name="comment"
                                          rows="3"
                                          placeholder="Ceritakan pengalaman Anda menyewa produk ini..."
                                          class="w-full px-3.5 py-2.5 rounded-xl bg-white border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 resize-none"
                                          required>{{ old('comment') }}</textarea>
                                @error('comment')
                                    <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <button type="submit"
                                    class="px-5 py-2.5 rounded-xl bg-brand-600 text-white text-xs font-bold hover:bg-brand-700 transition">
                                Kirim Ulasan
                            </button>
                        </form>
                    </div>
                @elseif ($hasReviewed)
                    <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-4 mb-8 text-sm text-emerald-700 font-semibold">
                        ✅ Anda sudah memberikan ulasan untuk produk ini.
                    </div>
                @endif
            @else
                <div class="bg-slate-50 border border-slate-200 rounded-2xl p-4 mb-8 text-sm text-slate-500">
                    <a href="{{ route('login') }}" class="text-brand-600 font-bold hover:underline">Masuk</a> untuk memberikan ulasan setelah menyewa produk ini.
                </div>
            @endauth

            {{-- Daftar Ulasan --}}
            @if ($reviews->isEmpty())
                <div class="text-center py-10 text-slate-400">
                    <div class="text-4xl mb-3">💬</div>
                    <p class="text-sm">Belum ada ulasan untuk produk ini.</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach ($reviews as $review)
                        <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
                            <div class="flex items-start justify-between mb-2">
                                <div>
                                    <p class="font-bold text-slate-800 text-sm">{{ $review->user->name }}</p>
                                    <p class="text-[11px] text-slate-400">{{ $review->created_at->format('d M Y') }}</p>
                                </div>
                                <div class="flex items-center gap-0.5">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <span class="text-lg {{ $i <= $review->rating ? 'text-amber-400' : 'text-slate-200' }}">★</span>
                                    @endfor
                                </div>
                            </div>
                            <p class="text-sm text-slate-600 leading-relaxed">{{ $review->comment }}</p>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </main>

    <footer class="bg-white border-t border-slate-200 py-6 text-center text-xs text-slate-500">
        &copy; {{ date('Y') }} Retronics. Rental Perlengkapan Fotografi & Kamera Profesional.
    </footer>

    {{-- Script interaktif bintang rating --}}
    <script>
        const stars = document.querySelectorAll('.star-btn');
        stars.forEach((star, index) => {
            star.addEventListener('click', () => {
                stars.forEach((s, i) => {
                    s.classList.toggle('text-amber-400', i <= index);
                    s.classList.toggle('text-slate-300', i > index);
                });
            });
            star.addEventListener('mouseover', () => {
                stars.forEach((s, i) => {
                    s.classList.toggle('text-amber-400', i <= index);
                    s.classList.toggle('text-slate-300', i > index);
                });
            });
        });

        document.getElementById('star-rating')?.addEventListener('mouseleave', () => {
            const checked = document.querySelector('input[name="rating"]:checked');
            const selectedVal = checked ? parseInt(checked.value) : 0;
            stars.forEach((s, i) => {
                s.classList.toggle('text-amber-400', i < selectedVal);
                s.classList.toggle('text-slate-300', i >= selectedVal);
            });
        });
    </script>

</body>
</html>
