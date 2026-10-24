<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Penyewaan | Retronics</title>
    <meta name="description" content="Periksa daftar peralatan kamera dan aksesori dalam keranjang sewa Retronics Anda sebelum melanjutkan pemesanan.">

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
    <header class="bg-white border-b border-slate-200 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <a href="{{ route('home') }}" class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-brand-600 text-white flex items-center justify-center font-bold text-xl shadow-md">
                        📷
                    </div>
                    <div>
                        <span class="font-display text-2xl font-bold tracking-tight text-slate-900 block">Retronics</span>
                        <span class="text-[10px] uppercase tracking-widest font-semibold text-slate-500 -mt-1 block">Rental Kamera & Studio</span>
                    </div>
                </a>

                <nav class="hidden md:flex items-center gap-6 text-sm font-semibold text-slate-600">
                    <a href="{{ route('home') }}" class="hover:text-brand-600 transition">Beranda</a>
                    <a href="{{ route('products.index') }}" class="hover:text-brand-600 transition">Katalog Gear</a>
                    <a href="{{ route('cart.index') }}" class="text-brand-600 font-bold flex items-center gap-1.5">
                        <span>Keranjang</span>
                        <span class="px-2 py-0.5 rounded-full bg-brand-100 text-brand-700 text-xs font-bold">{{ $totalItemsCount }}</span>
                    </a>
                </nav>

                <div class="flex items-center gap-3">
                    @auth
                        <a href="{{ route('account.index') }}" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition">
                            👤 Akun Saya
                        </a>
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

        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center gap-2 text-xs text-slate-400 mb-2">
                <a href="{{ route('home') }}" class="hover:underline">Beranda</a>
                <span>/</span>
                <span class="text-slate-700 font-medium">Keranjang Penyewaan</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-display">
                Keranjang Penyewaan Gear
            </h1>
            <p class="text-slate-500 text-xs sm:text-sm mt-1">
                Tentukan jumlah unit dan jadwal sewa untuk setiap gear yang Anda butuhkan sebelum melanjutkan ke pembayaran.
            </p>
        </div>

        @if (session('success'))
            <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm flex items-center justify-between">
                <span>✓ {{ session('success') }}</span>
                <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900">✕</button>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs sm:text-sm flex items-center justify-between">
                <span>⚠️ {{ session('error') }}</span>
                <button type="button" onclick="this.parentElement.remove()" class="text-rose-600 hover:text-rose-900">✕</button>
            </div>
        @endif

        @if (empty($items))
            <!-- Empty State -->
            <div class="py-16 text-center bg-white rounded-3xl border border-slate-200 shadow-sm p-8">
                <div class="w-20 h-20 rounded-3xl bg-blue-50 text-brand-600 flex items-center justify-center text-4xl mx-auto mb-4">
                    🛒
                </div>
                <h2 class="text-lg font-bold text-slate-800">Keranjang Sewa Anda Masih Kosong</h2>
                <p class="text-xs sm:text-sm text-slate-500 mt-1 max-w-md mx-auto leading-relaxed">
                    Belum ada perlengkapan fotografi yang ditambahkan. Silakan jelajahi katalog kamera, lensa, lighting, atau aksesori kami.
                </p>
                <div class="mt-6 flex flex-wrap justify-center gap-3">
                    <a href="{{ route('products.index') }}" class="px-6 py-3 rounded-xl bg-brand-600 hover:bg-brand-700 text-white text-xs sm:text-sm font-bold shadow-md shadow-brand-500/20 transition">
                        Jelajahi Katalog Kamera & Gear →
                    </a>
                </div>
            </div>
        @else
            <!-- Cart Items & Order Summary Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

                <!-- Left: Items Form (8 Cols) -->
                <div class="lg:col-span-8 space-y-4">
                    <form method="POST" action="{{ route('cart.update') }}" id="cart-form">
                        @csrf

                        <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
                            <div class="p-4 sm:p-6 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                                <span class="text-xs font-bold text-slate-700 uppercase tracking-wider">
                                    Daftar Barang ({{ count($items) }} Model, {{ $totalItemsCount }} Unit)
                                </span>
                                <div class="flex items-center gap-2">
                                    <button type="submit" class="text-xs font-bold text-brand-600 hover:text-brand-700">
                                        Perbarui Jadwal
                                    </button>
                                </div>
                            </div>

                            <div class="divide-y divide-slate-100">
                                @foreach ($items as $index => $item)
                                    <div class="p-4 sm:p-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                                        <!-- Image & Info -->
                                        <div class="flex items-center gap-4 min-w-[200px]">
                                            <div class="w-16 h-16 rounded-xl bg-slate-100 border border-slate-200/80 p-1 flex items-center justify-center shrink-0 overflow-hidden">
                                                @if ($item['product']->image)
                                                    <img src="{{ asset('storage/' . $item['product']->image) }}"
                                                         alt="{{ $item['product']->name }}"
                                                         class="max-h-full max-w-full object-contain"
                                                         onerror="this.onerror=null; this.src='{{ asset('images/products/' . $item['product']->image) }}';">
                                                @else
                                                    <span class="text-2xl">📷</span>
                                                @endif
                                            </div>

                                            <div>
                                                <span class="text-[10px] font-bold text-brand-600 uppercase tracking-wider block">
                                                    {{ $item['product']->category->name ?? 'Gear' }}
                                                </span>
                                                <a href="{{ route('products.show', $item['product']->slug) }}" class="text-sm font-bold text-slate-900 hover:text-brand-600 transition">
                                                    {{ $item['product']->name }}
                                                </a>
                                                <span class="text-xs text-slate-500 block mt-0.5">
                                                    Rp{{ number_format($item['price_per_day'], 0, ',', '.') }} <span class="text-[10px]">/ hari</span>
                                                </span>
                                                <span class="text-[10px] text-emerald-600 font-semibold">Tersedia: {{ $item['product']->stock }} Unit</span>
                                            </div>
                                        </div>

                                        <!-- Hidden ID -->
                                        <input type="hidden" name="items[{{ $index }}][product_id]" value="{{ $item['product']->id }}">

                                        <!-- Quantity, Date, & Duration Controls -->
                                        <div class="grid grid-cols-3 gap-2 w-full sm:w-auto text-xs">
                                            <!-- Qty -->
                                            <div>
                                                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Unit</label>
                                                <input type="number"
                                                       name="items[{{ $index }}][quantity]"
                                                       value="{{ $item['quantity'] }}"
                                                       min="1"
                                                       max="{{ $item['product']->stock }}"
                                                       class="w-16 px-2.5 py-1.5 rounded-lg border border-slate-200 bg-slate-50 text-center font-bold text-xs focus:bg-white focus:outline-none focus:ring-1 focus:ring-brand-500">
                                            </div>

                                            <!-- Start Date -->
                                            <div>
                                                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Mulai</label>
                                                <input type="date"
                                                       name="items[{{ $index }}][start_date]"
                                                       value="{{ $item['start_date'] }}"
                                                       min="{{ date('Y-m-d') }}"
                                                       class="px-2 py-1.5 rounded-lg border border-slate-200 bg-slate-50 text-[11px] focus:bg-white focus:outline-none focus:ring-1 focus:ring-brand-500">
                                            </div>

                                            <!-- Duration -->
                                            <div>
                                                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Hari</label>
                                                <input type="number"
                                                       name="items[{{ $index }}][duration_days]"
                                                       value="{{ $item['duration_days'] }}"
                                                       min="1"
                                                       max="30"
                                                       class="w-14 px-2.5 py-1.5 rounded-lg border border-slate-200 bg-slate-50 text-center font-bold text-xs focus:bg-white focus:outline-none focus:ring-1 focus:ring-brand-500">
                                            </div>
                                        </div>

                                        <!-- Line Total & Delete -->
                                        <div class="flex items-center justify-between sm:justify-end gap-4 w-full sm:w-auto pt-2 sm:pt-0 border-t sm:border-0 border-slate-100">
                                            <div class="text-right">
                                                <span class="text-[10px] text-slate-400 block font-medium">Subtotal</span>
                                                <span class="text-sm font-bold text-brand-600 font-display">
                                                    Rp{{ number_format($item['line_total'], 0, ',', '.') }}
                                                </span>
                                            </div>

                                            <button type="submit"
                                                    formaction="{{ route('cart.remove', $item['product']->id) }}"
                                                    formmethod="POST"
                                                    onclick="return confirm('Hapus {{ $item['product']->name }} dari keranjang sewa?');"
                                                    class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition"
                                                    title="Hapus dari keranjang">
                                                🗑️
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="p-4 sm:p-5 bg-slate-50/70 border-t border-slate-100 flex flex-wrap items-center justify-between gap-3">
                                <a href="{{ route('products.index') }}" class="text-xs font-bold text-slate-600 hover:text-brand-600 flex items-center gap-1.5">
                                    <span>← Tambah Gear Lainnya</span>
                                </a>

                                <div class="flex items-center gap-2">
                                    <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold text-brand-700 bg-brand-50 hover:bg-brand-100 border border-brand-200 transition">
                                        Simpan Perubahan
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Right: Summary Box (4 Cols) -->
                <div class="lg:col-span-4 space-y-4">
                    <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm sticky top-28">
                        <h2 class="text-base font-bold text-slate-900 pb-3 border-b border-slate-100 mb-4">
                            Ringkasan Biaya Sewa
                        </h2>

                        <div class="space-y-3 text-xs mb-6">
                            <div class="flex items-center justify-between text-slate-600">
                                <span>Total Unit Gear</span>
                                <span class="font-bold text-slate-900">{{ $totalItemsCount }} Unit</span>
                            </div>
                            <div class="flex items-center justify-between text-slate-600">
                                <span>Durasi Maksimal</span>
                                <span class="font-bold text-slate-900">{{ $maxDuration }} Hari</span>
                            </div>
                            <div class="flex items-center justify-between text-slate-600">
                                <span>Biaya Kotor (Gross)</span>
                                <span class="font-bold text-slate-900">Rp{{ number_format($grossTotal, 0, ',', '.') }}</span>
                            </div>

                            @if ($discountRate > 0)
                                <div class="flex items-center justify-between text-emerald-600 bg-emerald-50 p-2.5 rounded-xl border border-emerald-100">
                                    <span>Diskon Durasi ({{ $discountRate * 100 }}%)</span>
                                    <span class="font-bold">-Rp{{ number_format($discountAmount, 0, ',', '.') }}</span>
                                </div>
                            @endif

                            <div class="pt-3 border-t border-slate-100 flex items-baseline justify-between">
                                <div>
                                    <span class="text-xs font-bold text-slate-900 block">Total Akhir Sewa</span>
                                    <span class="text-[10px] text-slate-400">Termasuk pajak & biaya pembersihan</span>
                                </div>
                                <span class="text-xl font-black text-brand-600 font-display">
                                    Rp{{ number_format($finalTotal, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>

                        <!-- Checkout Form -->
                        @auth
                            <form method="POST" action="{{ route('cart.checkout') }}" class="space-y-3">
                                @csrf
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">
                                        Catatan / Keperluan Sewa (Opsional)
                                    </label>
                                    <textarea name="notes"
                                              rows="2"
                                              placeholder="Contoh: Pengambilan jam 09:00 pagi, untuk kebutuhan shoot wedding."
                                              class="w-full px-3 py-2 text-xs rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500"></textarea>
                                </div>

                                <button type="submit"
                                        class="w-full py-3.5 px-4 rounded-xl font-bold text-xs sm:text-sm text-white bg-brand-600 hover:bg-brand-700 shadow-md shadow-brand-500/20 transition flex items-center justify-center gap-2">
                                    <span>Lanjut ke Konfirmasi Pesanan →</span>
                                </button>
                            </form>
                        @else
                            <div class="space-y-3">
                                <div class="p-3 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-xs">
                                    Silakan masuk ke akun atau daftar untuk melanjutkan proses penyewaan gear.
                                </div>
                                <a href="{{ route('login') }}" class="block w-full py-3.5 px-4 rounded-xl font-bold text-xs sm:text-sm text-center text-white bg-brand-600 hover:bg-brand-700 transition">
                                    Masuk untuk Menyewa →
                                </a>
                            </div>
                        @endauth

                        <form method="POST" action="{{ route('cart.clear') }}" class="mt-4 pt-4 border-t border-slate-100 text-center">
                            @csrf
                            <button type="submit"
                                    onclick="return confirm('Kosongkan semua barang dari keranjang penyewaan?');"
                                    class="text-xs text-rose-600 hover:underline">
                                Kosongkan Keranjang Sewa
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        @endif

    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 py-6 text-center text-xs text-slate-500">
        &copy; 2026 Retronics. Rental Perlengkapan Fotografi & Kamera Profesional.
    </footer>

</body>
</html>
