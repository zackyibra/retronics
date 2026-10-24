<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan {{ $rental->rental_code }} | Retronics</title>

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
                        <span class="text-[10px] uppercase tracking-widest font-semibold text-slate-500 -mt-1 block">Detail Pesanan</span>
                    </div>
                </a>

                <div class="flex items-center gap-4 text-xs font-semibold">
                    <a href="{{ route('account.rentals') }}" class="text-slate-600 hover:text-brand-600">
                        ← Riwayat Penyewaan
                    </a>
                    <a href="{{ route('account.index') }}" class="px-3.5 py-2 rounded-xl bg-blue-50 text-brand-600 font-bold">
                        Akun Saya
                    </a>
                </div>
            </div>
        </div>
    </header>

    <main class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10 w-full flex-grow">

        <!-- Header Info -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
            <div>
                <div class="flex items-center gap-3 mb-1">
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-display">
                        Pesanan {{ $rental->rental_code }}
                    </h1>
                    <span class="px-3 py-1 rounded-full text-xs font-bold border {{ $rental->status_badge_class }}">
                        {{ $rental->status_label }}
                    </span>
                </div>
                <p class="text-slate-500 text-xs sm:text-sm">
                    Dibuat pada {{ $rental->created_at->translatedFormat('d F Y, H:i') }} WIB oleh <strong>{{ $rental->user->name }}</strong>
                </p>
            </div>

            <!-- WhatsApp Admin Contact -->
            <a href="https://wa.me/6281234567890?text=Halo%20Admin%20Retronics%2C%20saya%20ingin%20konfirmasi%20pesanan%20sewa%20nomor%20{{ $rental->rental_code }}"
               target="_blank"
               class="px-4 py-2.5 rounded-xl bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200 text-xs font-bold transition flex items-center gap-2 self-start sm:self-auto">
                <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.711 2.598 2.669-.699c.969.586 1.838.88 2.791.88h.005c3.18 0 5.767-2.586 5.768-5.766 0-3.18-2.587-5.766-5.773-5.766zm8.814 5.765c0 4.87-3.953 8.823-8.82 8.823-1.503 0-2.915-.382-4.15-1.047l-4.575 1.199 1.226-4.464c-.75-1.309-1.196-2.825-1.196-4.511 0-4.87 3.953-8.824 8.82-8.824 4.869 0 8.82 3.954 8.82 8.824z"/></svg>
                <span>Hubungi Admin WhatsApp</span>
            </a>
        </div>

        @if (session('success'))
            <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm flex items-center justify-between">
                <span>✓ {{ session('success') }}</span>
                <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900">✕</button>
            </div>
        @endif

        @if (session('info'))
            <div class="mb-6 p-4 rounded-xl bg-blue-50 border border-blue-200 text-blue-800 text-xs sm:text-sm">
                ℹ️ {{ session('info') }}
            </div>
        @endif

        <!-- Flowchart Timeline: Status Tahapan Penyewaan -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-sm mb-8">
            <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-6">
                Tahapan Alur Sewa (Flowchart Retronics)
            </h2>

            @php
                $stages = [
                    'pending_payment' => ['title' => 'Menunggu Pembayaran', 'desc' => 'Pengguna menyelesaikan pembayaran'],
                    'waiting_verification' => ['title' => 'Verifikasi Admin', 'desc' => 'Admin mengecek bukti transfer / pembayaran'],
                    'confirmed' => ['title' => 'Penyewaan Dikonfirmasi', 'desc' => 'Stok direservasi dan jadwal disetujui'],
                    'preparing' => ['title' => 'Barang Disiapkan', 'desc' => 'Pembersihan sensor, cek optik, baterai full'],
                    'ready_pickup' => ['title' => 'Siap Diambil / Diterima', 'desc' => 'Unit siap diambil di store atau delivery'],
                    'in_use' => ['title' => 'Sedang Digunakan', 'desc' => 'Penyewa menggunakan gear untuk produksi'],
                    'completed' => ['title' => 'Pemeriksaan & Selesai', 'desc' => 'Kondisi barang diperiksa, transaksi ditutup'],
                ];

                $statusOrder = array_keys($stages);
                $currentIndex = array_search($rental->status, $statusOrder);
                if ($rental->status === 'cancelled' || $rental->status === 'rejected') {
                    $currentIndex = -1;
                }
            @endphp

            <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-3">
                @foreach ($stages as $key => $stage)
                    @php
                        $index = array_search($key, $statusOrder);
                        $isPassed = ($currentIndex !== false && $currentIndex >= $index);
                        $isCurrent = ($rental->status === $key);
                    @endphp
                    <div class="p-3 rounded-2xl border text-center transition flex flex-col justify-between {{ $isCurrent ? 'bg-brand-50 border-brand-500 shadow-sm' : ($isPassed ? 'bg-emerald-50/50 border-emerald-200' : 'bg-slate-50 border-slate-200 opacity-60') }}">
                        <div>
                            <div class="w-6 h-6 rounded-full mx-auto mb-2 flex items-center justify-center text-xs font-black {{ $isCurrent ? 'bg-brand-600 text-white' : ($isPassed ? 'bg-emerald-600 text-white' : 'bg-slate-300 text-slate-600') }}">
                                {{ $loop->iteration }}
                            </div>
                            <span class="text-[11px] font-bold block leading-tight {{ $isCurrent ? 'text-brand-700' : ($isPassed ? 'text-emerald-800' : 'text-slate-600') }}">
                                {{ $stage['title'] }}
                            </span>
                        </div>
                        <span class="text-[9px] text-slate-400 mt-2 block">
                            {{ $isCurrent ? '👉 Tahap Saat Ini' : ($isPassed ? '✓ Selesai' : 'Belum') }}
                        </span>
                    </div>
                @endforeach
            </div>

            @if ($rental->admin_notes)
                <div class="mt-4 p-3.5 bg-amber-50 rounded-xl border border-amber-200 text-xs text-amber-900 flex items-start gap-2">
                    <span class="font-bold">📝 Catatan Admin:</span>
                    <span>{{ $rental->admin_notes }}</span>
                </div>
            @endif
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

            <!-- Left: Rincian Barang yang Disewa (7 Cols) -->
            <div class="lg:col-span-7 space-y-6">
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm">
                    <h2 class="text-base font-bold text-slate-900 pb-3 border-b border-slate-100 mb-4">
                        Daftar Barang yang Disewa
                    </h2>

                    <div class="space-y-4">
                        @foreach ($rental->items as $item)
                            <div class="flex items-center justify-between p-4 rounded-2xl bg-slate-50 border border-slate-100 gap-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-xl shrink-0">
                                        📷
                                    </div>
                                    <div>
                                        <a href="{{ route('products.show', $item->product->slug) }}" class="text-xs sm:text-sm font-bold text-slate-900 hover:text-brand-600 transition block">
                                            {{ $item->product->name }}
                                        </a>
                                        <span class="text-[11px] text-slate-500">
                                            {{ $item->product->category->name }} • Rp{{ number_format($item->price_per_day, 0, ',', '.') }}/hari × {{ $item->quantity }} unit
                                        </span>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="text-xs font-bold text-slate-800 block">
                                        Rp{{ number_format($item->subtotal, 0, ',', '.') }}
                                    </span>
                                    <span class="text-[10px] text-slate-400">Total Durasi</span>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Jadwal Sewa -->
                    <div class="mt-6 pt-6 border-t border-slate-100 grid grid-cols-3 gap-3 text-center">
                        <div class="p-3 bg-slate-50 rounded-xl">
                            <span class="text-[10px] text-slate-400 uppercase font-bold block">Mulai Sewa</span>
                            <span class="text-xs font-bold text-slate-800">{{ $rental->start_date->translatedFormat('d M Y') }}</span>
                        </div>
                        <div class="p-3 bg-slate-50 rounded-xl">
                            <span class="text-[10px] text-slate-400 uppercase font-bold block">Selesai Sewa</span>
                            <span class="text-xs font-bold text-slate-800">{{ $rental->end_date->translatedFormat('d M Y') }}</span>
                        </div>
                        <div class="p-3 bg-slate-50 rounded-xl">
                            <span class="text-[10px] text-slate-400 uppercase font-bold block">Durasi</span>
                            <span class="text-xs font-bold text-brand-600">{{ $rental->duration_days }} Hari</span>
                        </div>
                    </div>

                    @if ($rental->notes)
                        <div class="mt-4 p-3 bg-slate-50 rounded-xl text-xs text-slate-600">
                            <strong>Catatan Penyewa:</strong> {{ $rental->notes }}
                        </div>
                    @endif
                </div>

                <!-- Option: Batalkan Permintaan (Flowchart: Revisi / Batalkan Permintaan) -->
                @if (in_array($rental->status, ['pending_payment', 'waiting_verification']))
                    <div class="bg-rose-50/60 p-5 rounded-2xl border border-rose-100 flex items-center justify-between">
                        <div>
                            <h4 class="text-xs font-bold text-rose-800">Ada ketidaksesuaian data?</h4>
                            <p class="text-[11px] text-rose-600 mt-0.5">Anda dapat membatalkan pesanan ini dan membuat pesanan baru.</p>
                        </div>
                        <form method="POST" action="{{ route('rentals.cancel', $rental->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pesanan sewa ini?');">
                            @csrf
                            <button type="submit" class="px-3.5 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold transition">
                                Batalkan Pesanan
                            </button>
                        </form>
                    </div>
                @endif
            </div>

            <!-- Right: Pembayaran & Rincian Tagihan (5 Cols) -->
            <div class="lg:col-span-5 space-y-6">

                <!-- Tagihan -->
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400 block mb-1">Total Tagihan Sewa</span>
                    <div class="text-3xl font-black text-brand-600 font-display mb-6">
                        Rp{{ number_format($rental->total_price, 0, ',', '.') }}
                    </div>

                    <!-- Flowchart Step: Pengguna Melakukan Pembayaran -->
                    @if ($rental->status === 'pending_payment')
                        <h3 class="text-sm font-bold text-slate-900 mb-3 flex items-center gap-2">
                            <span>💳</span>
                            <span>Lakukan Pembayaran</span>
                        </h3>

                        <!-- Info Rekening -->
                        <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200 mb-5 space-y-2 text-xs">
                            <div class="flex justify-between items-center pb-2 border-b border-slate-200">
                                <span class="text-slate-500 font-medium">BCA (PT Retronics Visual):</span>
                                <strong class="text-slate-900 font-mono">123-456-7890</strong>
                            </div>
                            <div class="flex justify-between items-center pb-2 border-b border-slate-200">
                                <span class="text-slate-500 font-medium">Mandiri:</span>
                                <strong class="text-slate-900 font-mono">137-00-1234567-8</strong>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-slate-500 font-medium">QRIS All Payment:</span>
                                <span class="text-brand-600 font-bold">Tersedia via WA</span>
                            </div>
                        </div>

                        <!-- Form Pembayaran -->
                        <form method="POST" action="{{ route('rentals.pay', $rental->id) }}" enctype="multipart/form-data" class="space-y-4">
                            @csrf

                            <div>
                                <label for="payment_method" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Metode Pembayaran
                                </label>
                                <select id="payment_method" name="payment_method" required class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                                    <option value="transfer_bca">Transfer Bank BCA</option>
                                    <option value="transfer_mandiri">Transfer Bank Mandiri</option>
                                    <option value="qris">QRIS (Gopay, OVO, ShopeePay, Dana)</option>
                                    <option value="cash_store">Bayar Tunai / EDC di Store</option>
                                </select>
                            </div>

                            <div>
                                <label for="payment_proof" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                    Upload Bukti Pembayaran (Opsional)
                                </label>
                                <input type="file"
                                       id="payment_proof"
                                       name="payment_proof"
                                       accept="image/*"
                                       class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-brand-700 hover:file:bg-blue-100">
                                <span class="text-[10px] text-slate-400 mt-1 block">Format: JPG, PNG maks. 3MB</span>
                            </div>

                            <button type="submit"
                                    class="w-full mt-3 py-3.5 px-4 rounded-xl font-bold text-xs sm:text-sm text-white bg-emerald-600 hover:bg-emerald-700 shadow-lg shadow-emerald-600/25 transition flex items-center justify-center gap-2">
                                <span>Konfirmasi Sudah Membayar</span>
                                <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.711 2.598 2.669-.699c.969.586 1.838.88 2.791.88h.005c3.18 0 5.767-2.586 5.768-5.766 0-3.18-2.587-5.766-5.773-5.766zm8.814 5.765c0 4.87-3.953 8.823-8.82 8.823-1.503 0-2.915-.382-4.15-1.047l-4.575 1.199 1.226-4.464c-.75-1.309-1.196-2.825-1.196-4.511 0-4.87 3.953-8.824 8.82-8.824 4.869 0 8.82 3.954 8.82 8.824z"/></svg>
                            </button>
                        </form>

                    @elseif ($rental->status === 'waiting_verification')
                        <div class="p-4 bg-blue-50 rounded-2xl border border-blue-200 text-xs text-blue-900 space-y-2">
                            <div class="font-bold text-brand-700 flex items-center gap-1.5">
                                <span class="animate-spin">⏳</span>
                                <span>Sedang Diverifikasi oleh Admin</span>
                            </div>
                            <p class="leading-relaxed">
                                Pembayaran Anda sedang diperiksa secara manual oleh staf kami. Setelah valid, pesanan akan dikonfirmasi dan barang disiapkan.
                            </p>
                        </div>

                    @elseif (in_array($rental->status, ['confirmed', 'preparing', 'ready_pickup', 'in_use', 'completed']))
                        <div class="p-4 bg-emerald-50 rounded-2xl border border-emerald-200 text-xs text-emerald-900 space-y-2">
                            <div class="font-bold text-emerald-700 flex items-center gap-1.5">
                                <span>✓</span>
                                <span>Pembayaran Terverifikasi Lunas</span>
                            </div>
                            <p class="leading-relaxed">
                                Metode: <strong>{{ ucfirst(str_replace('_', ' ', $rental->payment_method ?? 'Transfer')) }}</strong>
                            </p>
                            @if ($rental->payment_proof)
                                <a href="{{ asset('storage/' . $rental->payment_proof) }}" target="_blank" class="text-brand-600 underline font-semibold block">
                                    Lihat Bukti Transfer
                                </a>
                            @endif
                        </div>
                    @endif

                </div>

                @if ($rental->fines->isNotEmpty())
                    <!-- Rincian Denda Pengembalian (Keterlambatan & Kerusakan) -->
                    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-rose-200 shadow-sm space-y-4">
                        <div class="flex items-center justify-between pb-3 border-b border-rose-100">
                            <div>
                                <span class="text-xs font-bold uppercase tracking-wider text-rose-600 block">
                                    Catatan Denda Pengembalian
                                </span>
                                <h3 class="text-base font-extrabold text-slate-900 font-display">
                                    Rincian Denda Transaksi
                                </h3>
                            </div>
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                Perlu Penyelesaian
                            </span>
                        </div>

                        <!-- Total Denda Nominal -->
                        <div>
                            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Total Denda</span>
                            <div class="text-2xl font-black text-rose-600 font-display">
                                Rp{{ number_format($rental->total_fines, 0, ',', '.') }}
                            </div>
                            <span class="text-[10px] text-slate-400 block mt-0.5">
                                Terpisah dari biaya sewa utama (Rp{{ number_format($rental->total_price, 0, ',', '.') }})
                            </span>
                        </div>

                        <!-- Rincian Komponen Denda -->
                        <div class="space-y-2.5 pt-2">
                            @if ($rental->late_fine)
                                <div class="p-3 rounded-2xl bg-amber-50/70 border border-amber-200 text-xs space-y-1">
                                    <div class="flex items-center justify-between font-bold text-amber-900">
                                        <span>⏱️ Denda Keterlambatan</span>
                                        <span>Rp{{ number_format($rental->late_fine->amount, 0, ',', '.') }}</span>
                                    </div>
                                    <p class="text-[11px] text-amber-800">
                                        {{ $rental->late_fine->days_late }} hari terlambat × Rp{{ number_format($rental->late_fine->rate_per_day, 0, ',', '.') }}/hari
                                    </p>
                                    <div class="text-[10px] text-amber-700">
                                        Batas Sewa: {{ $rental->end_date->translatedFormat('d M Y') }}
                                        @if ($rental->returned_at)
                                            • Dikembalikan: {{ $rental->returned_at->translatedFormat('d M Y, H:i') }} WIB
                                        @endif
                                    </div>
                                </div>
                            @endif

                            @foreach ($rental->damage_fines as $damageFine)
                                <div class="p-3 rounded-2xl bg-rose-50/70 border border-rose-200 text-xs space-y-1">
                                    <div class="flex items-center justify-between font-bold text-rose-900">
                                        <span>🛠️ Denda Kerusakan: {{ $damageFine->rentalItem?->product?->name ?? 'Barang' }}</span>
                                        <span>Rp{{ number_format($damageFine->amount, 0, ',', '.') }}</span>
                                    </div>
                                    @if ($damageFine->reason)
                                        <p class="text-[11px] text-rose-700 italic">
                                            "{{ $damageFine->reason }}"
                                        </p>
                                    @endif
                                    @if ($damageFine->assessor)
                                        <div class="text-[10px] text-slate-400">
                                            Pemeriksa: {{ $damageFine->assessor->name }}
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        <!-- Catatan Penyelesaian Denda -->
                        <div class="p-3 bg-slate-50 rounded-2xl border border-slate-200 text-[11px] text-slate-600 leading-relaxed">
                            ℹ️ <strong>Pemberitahuan:</strong> Denda di atas ditetapkan berdasarkan hasil pemeriksaan fisik unit saat pengembalian. Silakan lakukan penyelesaian administrasi denda secara langsung di gerai store Retronics atau konfirmasi via WhatsApp.
                        </div>
                    </div>
                @endif

                <!-- Kontak Bantuan -->
                <div class="p-5 bg-white rounded-2xl border border-slate-200 text-xs text-slate-600 space-y-2">
                    <h4 class="font-bold text-slate-800">Butuh Bantuan atau Panduan Pengambilan?</h4>
                    <p class="leading-relaxed">
                        Store Retronics buka setiap hari 08.00 - 21.00 WIB di Jl. Kreatif Visual No. 88. Bawa foto identitas KTP/KTM saat pengambilan unit.
                    </p>
                </div>

            </div>

        </div>

    </main>

    <footer class="bg-white border-t border-slate-200 py-6 text-center text-xs text-slate-500">
        &copy; {{ date('Y') }} Retronics. Rental Perlengkapan Fotografi & Kamera Profesional.
    </footer>

</body>
</html>
