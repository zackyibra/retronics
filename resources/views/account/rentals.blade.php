<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Penyewaan | Retronics</title>

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
                        <span class="text-[10px] uppercase tracking-widest font-semibold text-slate-500 -mt-1 block">Status & Riwayat Sewa</span>
                    </div>
                </a>

                <div class="flex items-center gap-4 text-xs font-semibold">
                    <a href="{{ route('products.index') }}" class="text-slate-600 hover:text-brand-600">Katalog</a>
                    <a href="{{ route('account.index') }}" class="px-3.5 py-2 rounded-xl bg-blue-50 text-brand-600 font-bold">
                        Akun Saya
                    </a>
                </div>
            </div>
        </div>
    </header>

    <main class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10 w-full flex-grow">

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
            <div>
                <span class="text-xs font-bold uppercase tracking-widest text-brand-600 bg-blue-50 px-3 py-1 rounded-full">
                    Aktivitas Sewa
                </span>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-display mt-2">
                    Riwayat &amp; Status Penyewaan
                </h1>
                <p class="text-slate-500 text-xs sm:text-sm mt-1">
                    Pantau tahapan pesanan Anda mulai dari pembayaran, verifikasi admin, hingga pengembalian.
                </p>
            </div>

            <a href="{{ route('products.index') }}" class="px-5 py-2.5 rounded-xl font-bold text-xs text-white bg-brand-600 hover:bg-brand-700 shadow-sm transition self-start sm:self-auto">
                + Sewa Gear Baru
            </a>
        </div>

        @if (session('success'))
            <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm flex items-center justify-between">
                <span>✓ {{ session('success') }}</span>
                <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900">✕</button>
            </div>
        @endif

        <div class="space-y-4">
            @forelse ($rentals as $rental)
                <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm hover:border-slate-300 transition">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-100">
                        <div>
                            <div class="flex items-center gap-3">
                                <span class="font-mono text-sm font-black text-slate-900">{{ $rental->rental_code }}</span>
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold border {{ $rental->status_badge_class }}">
                                    {{ $rental->status_label }}
                                </span>
                            </div>
                            <span class="text-xs text-slate-500 block mt-1">
                                Dipesan pada {{ $rental->created_at->translatedFormat('d M Y, H:i') }} WIB
                            </span>
                        </div>

                        <div class="text-left sm:text-right">
                            <span class="text-[10px] text-slate-400 block font-bold uppercase">Total Biaya Sewa</span>
                            <span class="text-lg font-black text-brand-600 font-display">
                                Rp{{ number_format($rental->total_price, 0, ',', '.') }}
                            </span>
                            @if ($rental->total_fines > 0)
                                <div class="mt-1">
                                    <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                        Denda: Rp{{ number_format($rental->total_fines, 0, ',', '.') }}
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Items Preview -->
                    <div class="py-4 grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs text-slate-700">
                        @foreach ($rental->items as $item)
                            <div class="flex items-center gap-2">
                                <span>📷</span>
                                <span class="font-semibold">{{ $item->product->name }}</span>
                                <span class="text-slate-400">({{ $item->quantity }}x)</span>
                            </div>
                        @endforeach
                    </div>

                    <!-- Footer of row -->
                    <div class="pt-4 border-t border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="text-xs text-slate-500">
                            Jadwal: <strong>{{ $rental->start_date->translatedFormat('d M Y') }}</strong> s/d <strong>{{ $rental->end_date->translatedFormat('d M Y') }}</strong> ({{ $rental->duration_days }} Hari)
                        </div>

                        <div class="flex items-center gap-3">
                            <a href="{{ route('rentals.show', $rental->id) }}" class="px-4 py-2 rounded-xl text-xs font-bold text-brand-600 bg-blue-50 hover:bg-blue-100 transition">
                                Lihat Rincian & Status Alur →
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="py-16 text-center bg-white rounded-3xl border border-dashed border-slate-300">
                    <span class="text-5xl block mb-3">📋</span>
                    <h3 class="text-base font-bold text-slate-800">Belum Ada Riwayat Penyewaan</h3>
                    <p class="text-slate-500 text-xs mt-1 max-w-sm mx-auto">
                        Anda belum pernah membuat pemesanan sewa. Pilih gear kamera favorit Anda dari katalog untuk memulai.
                    </p>
                    <a href="{{ route('products.index') }}" class="inline-block mt-4 px-5 py-2.5 bg-brand-600 text-white rounded-xl text-xs font-bold hover:bg-brand-700 transition">
                        Buka Katalog Barang
                    </a>
                </div>
            @endforelse
        </div>

        <div class="mt-8">
            {{ $rentals->links() }}
        </div>

    </main>

    <footer class="bg-white border-t border-slate-200 py-6 text-center text-xs text-slate-500">
        &copy; {{ date('Y') }} Retronics. Rental Perlengkapan Fotografi & Kamera Profesional.
    </footer>

</body>
</html>
