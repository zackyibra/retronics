@extends('layouts.app')

@section('title', 'Keranjang Sewa Peralatan')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full flex-grow">

    <!-- Breadcrumb -->
    <x-breadcrumb :items="[['label' => 'Keranjang', 'url' => '']]" />

    <!-- Page Title & Flow Stepper -->
    <div class="mb-8">
        <h1 class="text-3xl sm:text-4xl font-black text-slate-900 font-display">
            Keranjang sewa kamu
        </h1>
        <p class="text-xs sm:text-sm text-slate-500 mt-1">
            Periksa unit dan durasi sebelum melanjutkan ke konfirmasi checkout.
        </p>

        <!-- Stepper Indicator (Matching Design) -->
        <div class="flex items-center gap-2 mt-4 text-xs font-bold">
            <span class="px-3 py-1 rounded-full bg-blue-50 text-brand-700 border border-blue-200">
                1 · Keranjang
            </span>
            <span class="text-slate-300">—</span>
            <span class="text-slate-400">2 · Konfirmasi Checkout</span>
            <span class="text-slate-300">—</span>
            <span class="text-slate-400">3 · Pembayaran</span>
        </div>
    </div>

    @if(auth()->check() && auth()->user()->isAdmin())
        <div class="mb-6 p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 flex items-start gap-3">
            <span class="text-xl">👑</span>
            <div>
                <h4 class="font-bold text-xs uppercase tracking-wider">Akses Administrator Terdeteksi</h4>
                <p class="text-xs text-amber-800 mt-0.5 leading-relaxed">
                    Akun administrator hanya digunakan untuk pengelolaan operasional toko Retronics. Anda tidak dapat melakukan checkout atau membuat transaksi penyewaan dengan akun ini. Silakan gunakan akun pelanggan untuk menyewa gear.
                </p>
                <div class="mt-2">
                    <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-amber-600 hover:bg-amber-700 text-white font-bold text-[11px] transition shadow-xs">
                        <span>Kembali ke Dashboard Admin ↗</span>
                    </a>
                </div>
            </div>
        </div>
    @endif

    @if (empty($items))
        <!-- Empty State -->
        <div class="py-16 text-center bg-white rounded-3xl border border-slate-200 shadow-xs p-8 max-w-xl mx-auto">
            <div class="w-16 h-16 rounded-2xl bg-blue-50 text-brand-600 text-3xl flex items-center justify-center mx-auto mb-4">
                🛒
            </div>
            <h2 class="text-lg font-bold text-slate-900 font-display">Keranjang Sewa Masih Kosong</h2>
            <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto leading-relaxed">
                Belum ada gear yang kamu pilih. Temukan kamera, lensa, atau stabilizer terbaik di katalog kami.
            </p>
            <div class="mt-6">
                <a href="{{ route('products.index') }}" class="px-6 py-3 rounded-xl bg-brand-600 hover:bg-brand-700 text-white text-xs font-bold transition shadow-xs">
                    Jelajahi Katalog Gear →
                </a>
            </div>
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

            <!-- LEFT COLUMN: Items List & Common Schedule (8 Cols) -->
            <div class="lg:col-span-8 space-y-6">

                <!-- Gear Pilihan Card -->
                <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm p-6 sm:p-8">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
                        <span class="font-display font-bold text-base text-slate-900">
                            Gear pilihan · {{ $totalItemsCount }} unit
                        </span>
                        <div class="hidden sm:flex items-center gap-12 text-[10px] uppercase font-bold text-slate-400 tracking-wider">
                            <span>Harga / Hari</span>
                            <span class="pr-2">Jumlah</span>
                        </div>
                    </div>

                    <div class="divide-y divide-slate-100">
                        @foreach ($items as $item)
                            <div class="py-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                                <div class="flex items-center gap-4">
                                    <div class="w-20 h-20 rounded-2xl bg-slate-50 border border-slate-100 p-2 flex items-center justify-center shrink-0">
                                        @if ($item['product']->image)
                                            <img src="{{ asset('storage/' . $item['product']->image) }}"
                                                 alt="{{ $item['product']->name }}"
                                                 class="max-h-16 max-w-full object-contain"
                                                 onerror="this.onerror=null; this.src='{{ asset('images/products/' . $item['product']->image) }}';">
                                        @else
                                            <span class="text-2xl">📷</span>
                                        @endif
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-slate-900 text-sm">
                                            {{ $item['product']->name }}
                                        </h3>
                                        <span class="text-[11px] text-slate-400 block mt-0.5">
                                            {{ ucfirst($item['product']->condition) }} · Sensor Bersih
                                        </span>
                                        <form method="POST" action="{{ route('cart.remove', $item['product']->id) }}" class="mt-1">
                                            @csrf
                                            <button type="submit" class="text-[11px] font-semibold text-rose-600 hover:underline">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between sm:justify-end gap-6 w-full sm:w-auto">
                                    <span class="text-xs font-bold text-slate-900 sm:w-28 text-left sm:text-right">
                                        Rp{{ number_format($item['product']->price_per_day, 0, ',', '.') }}
                                    </span>

                                    <!-- Quantity Input -->
                                    <div class="flex items-center border border-slate-200 rounded-xl bg-slate-50 px-2 py-1">
                                        <span class="text-xs font-bold text-slate-700 px-2">{{ $item['quantity'] }} unit</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Jadwal Untuk Semua Gear Card -->
                <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm p-6 sm:p-8 space-y-4">
                    <h3 class="font-display font-bold text-base text-slate-900">
                        Jadwal untuk semua gear
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="cart-start-date" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Tanggal Mulai
                            </label>
                            <input type="date"
                                   id="cart-start-date"
                                   value="{{ date('Y-m-d') }}"
                                   min="{{ date('Y-m-d') }}"
                                   class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs sm:text-sm font-medium">
                        </div>

                        <div>
                            <label for="cart-duration" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Durasi Bersama
                            </label>
                            <select id="cart-duration" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs sm:text-sm font-medium">
                                <option value="1">1 hari</option>
                                <option value="2">2 hari</option>
                                <option value="3" selected>3 hari ▾</option>
                                <option value="7">7 hari</option>
                            </select>
                        </div>
                    </div>

                    <!-- Blue Notification Box -->
                    <div class="p-3.5 rounded-2xl bg-blue-50 text-brand-700 text-xs border border-blue-100 flex items-center gap-2">
                        <span>ℹ️</span>
                        <span id="cart-schedule-hint">
                            Ambil hari ini · Kedua unit memakai jadwal yang sama.
                        </span>
                    </div>
                </div>

                <div>
                    <a href="{{ route('products.index') }}"
                       class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs border border-slate-200 transition shadow-xs">
                        <span>← Tambah Gear Lain</span>
                    </a>
                </div>

            </div>

            <!-- RIGHT COLUMN: Estimasi Biaya Card (4 Cols) -->
            <div class="lg:col-span-4 space-y-4">
                
                <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm p-6 sm:p-8 space-y-4">
                    <h2 class="font-display font-bold text-lg text-slate-900 pb-3 border-b border-slate-100">
                        Estimasi biaya
                    </h2>

                    <!-- Items Breakdown -->
                    <div class="space-y-2 text-xs text-slate-600">
                        @foreach ($items as $item)
                            <div class="flex justify-between">
                                <span class="truncate max-w-[180px]">{{ $item['product']->name }} · {{ $item['quantity'] }}x</span>
                                <span class="font-bold text-slate-800">
                                    Rp{{ number_format($item['product']->price_per_day * $item['quantity'], 0, ',', '.') }}/hari
                                </span>
                            </div>
                        @endforeach
                    </div>

                    <!-- Cost Totals -->
                    <div class="pt-3 border-t border-slate-100 space-y-2 text-xs">
                        <div class="flex justify-between text-slate-600">
                            <span>Subtotal sewa:</span>
                            <span class="font-bold text-slate-800" id="cart-subtotal-val">
                                Rp{{ number_format($grossTotal ?? 0, 0, ',', '.') }}
                            </span>
                        </div>
                        <div class="flex justify-between text-emerald-600 font-semibold">
                            <span>Diskon durasi (simulasi):</span>
                            <span id="cart-discount-val">-Rp0</span>
                        </div>
                        <div class="pt-3 border-t border-slate-200 flex justify-between items-baseline">
                            <span class="font-bold text-slate-700 text-sm">Total akhir:</span>
                            <span class="text-2xl font-black text-slate-900 font-display" id="cart-net-val">
                                Rp{{ number_format($finalTotal ?? $grossTotal ?? 0, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>

                    <p class="text-[11px] text-slate-400 leading-relaxed">
                        *Simulasi durasi dan potongan sewa. Tarif durasi final menunggu ketentuan store.
                    </p>

                    <!-- Checkout Button -->
                    @if (auth()->check() && auth()->user()->isAdmin())
                        <div class="p-3.5 bg-amber-50 border border-amber-200 rounded-xl text-center space-y-1">
                            <span class="text-xs font-bold text-amber-900 block">👑 Khusus Akun Pelanggan</span>
                            <p class="text-[11px] text-amber-700">Akun Admin tidak diperkenankan melakukan transaksi sewa.</p>
                        </div>
                    @else
                        <button type="button"
                                id="btn-proceed-checkout"
                                class="w-full py-3.5 px-4 rounded-xl font-bold text-xs sm:text-sm text-white bg-brand-600 hover:bg-brand-700 shadow-md shadow-brand-500/25 transition flex items-center justify-center gap-2">
                            <span>Lanjut ke Konfirmasi Checkout</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </button>
                    @endif

                    <p class="text-[11px] text-slate-400 text-center">
                        Voucher promo dapat digunakan pada langkah checkout.
                    </p>
                </div>

                <!-- Info Box -->
                <div class="p-4 rounded-2xl bg-blue-50/70 border border-blue-100 text-xs text-brand-800 leading-relaxed">
                    Belum ada tagihan. Ketersediaan final dikonfirmasi saat permintaan pesanan diproses oleh admin store.
                </div>

            </div>

        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const durationSelect = document.getElementById('cart-duration');
                const startDateInput = document.getElementById('cart-start-date');
                const btnCheckout = document.getElementById('btn-proceed-checkout');
                const dailySubtotal = {{ (int) ($totalPerDay ?? $totalDailyPrice ?? 0) }};

                function updateCartTotals() {
                    const days = parseInt(durationSelect.value || 1);
                    let discountPercent = 0;
                    if (days >= 7) discountPercent = 0.25;
                    else if (days >= 3) discountPercent = 0.15;
                    else if (days >= 2) discountPercent = 0.05;

                    const gross = dailySubtotal * days;
                    const disc = Math.round(gross * discountPercent);
                    const net = gross - disc;

                    document.getElementById('cart-subtotal-val').innerText = formatIDR(gross);
                    document.getElementById('cart-discount-val').innerText = disc > 0 ? '-' + formatIDR(disc) + ' (' + (discountPercent * 100) + '%)' : 'Rp0';
                    document.getElementById('cart-net-val').innerText = formatIDR(net);
                }

                if (durationSelect) {
                    durationSelect.addEventListener('change', updateCartTotals);
                    updateCartTotals();
                }

                if (btnCheckout && startDateInput && durationSelect) {
                    btnCheckout.addEventListener('click', function() {
                        const sDate = startDateInput.value;
                        const days = durationSelect.value;
                        window.location.href = "{{ route('rentals.create') }}?start_date=" + sDate + "&days=" + days;
                    });
                }
            });
        </script>
    @endif

</div>
@endsection
