@extends('admin.layout')

@section('title', 'Pengembalian & Denda')

@section('content')
@php
    $totalUnpaidFines = $totalUnpaidFines ?? \App\Models\Fine::where('is_paid', false)->sum('amount');
    $overdueRentals = $pendingReturns->filter(fn($r) => $r->end_date->isPast());
    $dueTomorrowRentals = $pendingReturns->filter(fn($r) => $r->end_date->isTomorrow());
    $selectedRental = $pendingReturns->first() ?? $completedHistory->first();
@endphp

<div class="space-y-8" x-data="{ selectedId: {{ $pendingReturns->isNotEmpty() ? $pendingReturns->first()->id : 'null' }} }">

    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-xs text-slate-500">
        <a href="{{ route('admin.dashboard') }}" class="hover:text-brand-600 transition">Admin</a>
        <span class="text-slate-300">›</span>
        <span class="text-slate-800 font-semibold">Pengembalian & Denda</span>
    </nav>

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 font-display tracking-tight">
                Pengembalian & Denda
            </h1>
            <p class="text-slate-500 text-xs sm:text-sm mt-1">
                Periksa fisik peralatan, cocokkan kelengkapan, hitung denda secara otomatis, dan selesaikan transaksi secara transparan.
            </p>
        </div>
    </div>

    <!-- Alert Success / Error -->
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm flex items-start gap-3 shadow-sm">
            <svg class="w-5 h-5 text-emerald-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div>
                <strong class="font-bold block">Berhasil!</strong>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs sm:text-sm flex items-start gap-3 shadow-sm">
            <svg class="w-5 h-5 text-rose-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div>
                <strong class="font-bold block">Perhatian:</strong>
                <span>{{ session('error') }}</span>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs sm:text-sm">
            <p class="font-bold mb-1">Ada kesalahan input:</p>
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- 3 KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <!-- 1. Terlambat -->
        <div class="p-5 sm:p-6 rounded-3xl bg-white border border-slate-200 shadow-sm space-y-1.5">
            <span class="text-xs text-slate-500 font-medium block">Penyewaan Terlambat</span>
            <div class="text-3xl font-extrabold text-slate-900 font-display">
                {{ $overdueRentals->count() }} <span class="text-sm font-normal text-slate-400">pesanan</span>
            </div>
            <span class="text-[11px] {{ $overdueRentals->isNotEmpty() ? 'text-rose-600 font-bold' : 'text-slate-400 font-medium' }} block">
                @if($overdueRentals->isNotEmpty())
                    {{ $overdueRentals->first()->user->name }} · {{ $overdueRentals->first()->rental_code }}
                @else
                    Tidak ada unit yang melewati batas sewa
                @endif
            </span>
        </div>

        <!-- 2. Jatuh tempo besok -->
        <div class="p-5 sm:p-6 rounded-3xl bg-white border border-slate-200 shadow-sm space-y-1.5">
            <span class="text-xs text-slate-500 font-medium block">Jatuh Tempo Besok</span>
            <div class="text-3xl font-extrabold text-slate-900 font-display">
                {{ $dueTomorrowRentals->count() }} <span class="text-sm font-normal text-slate-400">pesanan</span>
            </div>
            <span class="text-[11px] text-slate-500 font-medium block">
                Batas pengembalian {{ \Carbon\Carbon::tomorrow()->translatedFormat('d F') }}
            </span>
        </div>

        <!-- 3. Denda berjalan -->
        <div class="p-5 sm:p-6 rounded-3xl bg-white border border-slate-200 shadow-sm space-y-1.5">
            <span class="text-xs text-slate-500 font-medium block">Denda Belum Dilunasi</span>
            <div class="text-3xl font-extrabold text-slate-900 font-display">
                Rp{{ number_format($totalUnpaidFines, 0, ',', '.') }}
            </div>
            <span class="text-[11px] {{ $totalUnpaidFines > 0 ? 'text-amber-600 font-bold' : 'text-emerald-600 font-medium' }} block">
                {{ $totalUnpaidFines > 0 ? 'Menunggu pelunasan di store' : 'Semua denda lunas' }}
            </span>
        </div>
    </div>

    <!-- TABEL UNIT SEDANG DISEWA / MENUNGGU INSPEKSI PENGEMBALIAN -->
    <div class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-200 shadow-sm space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-bold text-slate-900 font-display">
                Unit Sedang Disewa & Siap Diperiksa ({{ $pendingReturns->count() }})
            </h2>
            <span class="text-xs text-slate-400 font-medium">Klik baris untuk mulai inspeksi</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs sm:text-sm">
                <thead>
                    <tr class="border-b border-slate-200 text-[11px] uppercase tracking-wider text-slate-400 font-bold">
                        <th class="pb-3">Pesanan / Pelanggan</th>
                        <th class="pb-3">Gear Fotografi</th>
                        <th class="pb-3">Jadwal Kembali</th>
                        <th class="pb-3">Status Keterlambatan</th>
                        <th class="pb-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($pendingReturns as $rental)
                        @php
                            $isOverdue = $rental->end_date->isPast();
                            $diffDays = $isOverdue ? max(1, (int) $rental->end_date->diffInDays(now())) : 0;
                        @endphp
                        <tr class="hover:bg-blue-50/40 transition cursor-pointer"
                            :class="selectedId === {{ $rental->id }} ? 'bg-blue-50/70 border-l-4 border-l-brand-600' : ''"
                            @click="selectedId = {{ $rental->id }}">
                            <td class="py-4">
                                <div class="font-bold text-slate-900 font-mono text-sm">{{ $rental->rental_code }}</div>
                                <div class="text-xs text-slate-500">{{ $rental->user->name }} · {{ $rental->user->phone ?? '-' }}</div>
                            </td>
                            <td class="py-4 font-semibold text-slate-800">
                                {{ $rental->items->map(fn($it) => $it->product->name . ' (' . $it->quantity . 'x)')->join(' + ') }}
                            </td>
                            <td class="py-4 text-slate-600">
                                <div>{{ $rental->end_date->translatedFormat('d M Y') }}</div>
                                <div class="text-[11px] text-slate-400">Pukul 10.00 WIB</div>
                            </td>
                            <td class="py-4">
                                @if($isOverdue)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-700">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-600"></span>
                                        Terlambat {{ $diffDays }} hari
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-700">
                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
                                        Sedang Disewa (Tepat Waktu)
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 text-right">
                                <button type="button"
                                        @click.stop="selectedId = {{ $rental->id }}"
                                        class="px-3.5 py-1.5 rounded-xl font-bold text-xs bg-brand-50 text-brand-600 hover:bg-brand-100 transition">
                                    Inspeksi Fisik ↗
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-slate-400 text-xs sm:text-sm">
                                Tidak ada peralatan yang sedang disewa atau menunggu proses pengembalian saat ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- FORMULIR INSPEKSI FISIK, KALKULASI DENDA & TUTUP TRANSAKSI -->
    @foreach($pendingReturns as $rental)
        @php
            $isOverdue = $rental->end_date->isPast();
            $diffDays = $isOverdue ? max(1, (int) $rental->end_date->diffInDays(now())) : 0;
            $ratePerDay = (float) config('rental.late_fee_per_day', 50000);
            $calculatedLateFee = $diffDays * $ratePerDay;
        @endphp

        <div x-show="selectedId === {{ $rental->id }}"
             x-cloak
             x-data="{
                 itemCondition: 'layak_tersedia',
                 finePaid: false,
                 lateFee: {{ $calculatedLateFee }},
                 damageTotal: 0,
                 actualReturnDate: '{{ now()->format('Y-m-d\TH:i') }}',
                 recalculateDamage() {
                     let sum = 0;
                     document.querySelectorAll('.damage-input-{{ $rental->id }}').forEach(el => {
                         let val = parseFloat(el.value);
                         if (!isNaN(val) && val > 0) sum += val;
                     });
                     this.damageTotal = sum;
                 }
             }"
             class="space-y-6">

            <!-- Formula Banner Keterlambatan jika Terlambat -->
            @if($isOverdue)
                <div class="p-4 sm:p-5 rounded-3xl bg-rose-50 border border-rose-200 text-xs sm:text-sm text-rose-900 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 shadow-sm">
                    <div class="flex items-center gap-3">
                        <span class="text-2xl">⚠️</span>
                        <div>
                            <strong class="font-bold text-sm block">Keterlambatan Pengembalian Terdeteksi</strong>
                            <span>Pesanan <code class="font-mono font-bold bg-rose-100 px-1 py-0.5 rounded text-rose-900">{{ $rental->rental_code }}</code>: Terlambat {{ $diffDays }} hari × Rp{{ number_format($ratePerDay, 0, ',', '.') }} = <strong class="font-extrabold text-rose-950 font-mono">Rp{{ number_format($calculatedLateFee, 0, ',', '.') }}</strong></span>
                        </div>
                    </div>
                    <span class="px-3 py-1 bg-rose-200/80 rounded-xl text-xs font-bold text-rose-900">
                        Tarif Toko: Rp50.000 / hari
                    </span>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.returns.process', $rental->id) }}" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

                    <!-- KOLOM KIRI (lg:col-span-7): Inspeksi Fisik & Kondisi -->
                    <div class="lg:col-span-7 bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-sm space-y-6">
                        <div class="border-b border-slate-100 pb-4">
                            <span class="inline-block px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider text-brand-600 bg-brand-50 border border-brand-100 mb-2">
                                Form Pemeriksaan Fisik
                            </span>
                            <h2 class="text-2xl font-extrabold text-slate-900 font-display">
                                Inspeksi · {{ $rental->rental_code }}
                            </h2>
                            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                                Pelanggan: <strong class="text-slate-800">{{ $rental->user->name }}</strong> · WhatsApp: <span class="font-mono text-slate-700">{{ $rental->user->phone ?? 'Belum ada' }}</span>
                            </p>
                        </div>

                        <!-- Jadwal & Tanggal Pengembalian Aktual -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="p-4 rounded-2xl border border-slate-200 bg-slate-50/50">
                                <span class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">
                                    Batas Jadwal Sewa
                                </span>
                                <div class="text-sm font-bold text-slate-900">
                                    {{ $rental->end_date->translatedFormat('d M Y') }} · 10.00 WIB
                                </div>
                            </div>

                            <div class="p-4 rounded-2xl border border-slate-200 bg-slate-50/50 focus-within:border-brand-500 focus-within:ring-2 focus-within:ring-brand-500/10">
                                <label for="returned_at_{{ $rental->id }}" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">
                                    Waktu Kembali Aktual *
                                </label>
                                <input type="datetime-local"
                                       id="returned_at_{{ $rental->id }}"
                                       name="returned_at"
                                       x-model="actualReturnDate"
                                       required
                                       class="w-full bg-transparent text-sm font-bold text-slate-900 focus:outline-none">
                            </div>
                        </div>

                        <!-- Checklist Fisik Peralatan -->
                        <div class="space-y-3">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                Checklist Kelengkapan Unit & Aksesoris
                            </label>
                            <div class="border border-slate-200 rounded-2xl overflow-hidden text-xs">
                                <table class="w-full text-left">
                                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase font-bold text-[10px]">
                                        <tr>
                                            <th class="p-3">Gear & Kelengkapan</th>
                                            <th class="p-3 text-center">Fisik Sesuai</th>
                                            <th class="p-3 text-center">Fungsi Normal</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        @foreach($rental->items as $item)
                                            <tr class="hover:bg-slate-50/50">
                                                <td class="p-3">
                                                    <div class="font-bold text-slate-800 text-sm">{{ $item->product->name }}</div>
                                                    <div class="text-[11px] text-slate-500">Jumlah: {{ $item->quantity }} unit · Kelengkapan standar bawaan</div>
                                                </td>
                                                <td class="p-3 text-center">
                                                    <input type="checkbox" checked class="w-4 h-4 text-emerald-600 rounded border-slate-300 focus:ring-emerald-500">
                                                </td>
                                                <td class="p-3 text-center">
                                                    <input type="checkbox" checked class="w-4 h-4 text-emerald-600 rounded border-slate-300 focus:ring-emerald-500">
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Keputusan Kelayakan Kondisi Akhir (item_condition_after) -->
                        <div class="space-y-3">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                Kesimpulan Kelayakan Kondisi Barang *
                            </label>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <label class="p-4 rounded-2xl border cursor-pointer transition flex items-start gap-3"
                                       :class="itemCondition === 'layak_tersedia' ? 'border-emerald-500 bg-emerald-50/40 ring-2 ring-emerald-500/20' : 'border-slate-200 bg-white hover:bg-slate-50'">
                                    <input type="radio"
                                           name="item_condition_after"
                                           value="layak_tersedia"
                                           x-model="itemCondition"
                                           required
                                           class="mt-1 text-emerald-600 focus:ring-emerald-500">
                                    <div>
                                        <strong class="text-sm font-bold text-slate-900 block">Kondisi Prima & Layak</strong>
                                        <p class="text-xs text-slate-500 mt-0.5">Barang bersih, fungsi normal, stok otomatis ditambah kembali ke etalase.</p>
                                    </div>
                                </label>

                                <label class="p-4 rounded-2xl border cursor-pointer transition flex items-start gap-3"
                                       :class="itemCondition === 'perawatan_rusak' ? 'border-rose-500 bg-rose-50/40 ring-2 ring-rose-500/20' : 'border-slate-200 bg-white hover:bg-slate-50'">
                                    <input type="radio"
                                           name="item_condition_after"
                                           value="perawatan_rusak"
                                           x-model="itemCondition"
                                           required
                                           class="mt-1 text-rose-600 focus:ring-rose-500">
                                    <div>
                                        <strong class="text-sm font-bold text-slate-900 block">Butuh Servis / Rusak</strong>
                                        <p class="text-xs text-slate-500 mt-0.5">Ada cacat/kerusakan. Stok ditahan untuk perbaikan dan kenakan denda perbaikan.</p>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Form Input Denda Kerusakan (Muncul jika perawatan_rusak dipilih) -->
                        <div x-show="itemCondition === 'perawatan_rusak'" x-cloak class="p-4 rounded-2xl bg-rose-50/60 border border-rose-200 space-y-4">
                            <div class="flex items-center gap-2 text-rose-900 font-bold text-xs uppercase tracking-wider">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <span>Rincian Biaya Perbaikan / Denda Kerusakan</span>
                            </div>

                            @foreach($rental->items as $item)
                                <div class="bg-white p-3.5 rounded-xl border border-slate-200 space-y-2">
                                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
                                        <div class="sm:col-span-4 text-xs font-bold text-slate-800">
                                            {{ $item->product->name }}
                                        </div>
                                        <div class="sm:col-span-4">
                                            <input type="number"
                                                   name="damage_fines[{{ $item->id }}]"
                                                   placeholder="Nominal denda (Rp)"
                                                   min="0"
                                                   step="1000"
                                                   @input="recalculateDamage()"
                                                   class="damage-input-{{ $rental->id }} w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold focus:outline-none focus:border-rose-500">
                                        </div>
                                        <div class="sm:col-span-4">
                                            <input type="text"
                                                   name="damage_reasons[{{ $item->id }}]"
                                                   placeholder="Alasan / jenis kerusakan..."
                                                   class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:outline-none focus:border-rose-500">
                                        </div>
                                    </div>
                                    <div class="pt-1">
                                        <label class="block text-[10px] text-slate-500 mb-0.5">Foto Bukti Kerusakan (Opsional):</label>
                                        <input type="file"
                                               name="damage_photos[{{ $item->id }}]"
                                               accept="image/*"
                                               class="w-full text-xs text-slate-500 file:mr-2 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-[11px] file:bg-rose-50 file:text-rose-700">
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Foto Kondisi Pengembalian Barang -->
                        <div class="space-y-1.5 p-3.5 rounded-2xl border border-slate-200 bg-slate-50/50">
                            <label for="return_photo_{{ $rental->id }}" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                Foto Kondisi Pengembalian (Opsional)
                            </label>
                            <input type="file"
                                   id="return_photo_{{ $rental->id }}"
                                   name="return_photo"
                                   accept="image/*"
                                   class="w-full text-xs text-slate-600 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100 border border-slate-200 rounded-xl p-1.5 bg-white">
                            <span class="block text-[11px] text-slate-400">Lampirkan foto fisik keseluruhan gear saat diterima kembali di store.</span>
                        </div>

                        <!-- Catatan Pemeriksaan Admin -->
                        <div class="space-y-1.5">
                            <label for="admin_notes_{{ $rental->id }}" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">
                                Catatan Pemeriksaan Admin
                            </label>
                            <textarea id="admin_notes_{{ $rental->id }}"
                                      name="admin_notes"
                                      rows="2"
                                      placeholder="Contoh: Unit dan seluruh aksesoris lengkap dikembalikan dalam kondisi prima."
                                      class="w-full p-3.5 rounded-2xl border border-slate-200 text-xs sm:text-sm font-medium focus:outline-none focus:ring-4 focus:ring-brand-500/10 focus:border-brand-500 transition">Pemeriksaan fisik selesai. Seluruh unit dan aksesoris lengkap sesuai daftar sewa.</textarea>
                        </div>
                    </div>

                    <!-- KOLOM KANAN (lg:col-span-5): Kalkulasi Denda & Pelunasan di Store -->
                    <div class="lg:col-span-5 space-y-6">

                        <!-- Kartu Kalkulasi Denda Otomatis -->
                        <div class="bg-white p-6 sm:p-7 rounded-3xl border border-slate-200 shadow-sm space-y-4">
                            <h3 class="text-base font-bold text-slate-900 font-display">
                                Kalkulasi Denda
                            </h3>

                            <div class="space-y-2.5 text-xs sm:text-sm">
                                <div class="flex items-center justify-between text-slate-600">
                                    <span>Keterlambatan</span>
                                    <span class="font-bold text-slate-900">{{ $diffDays }} hari</span>
                                </div>
                                <div class="flex items-center justify-between text-slate-600">
                                    <span>Tarif denda keterlambatan</span>
                                    <span class="font-bold text-slate-900">Rp{{ number_format($ratePerDay, 0, ',', '.') }} / hari</span>
                                </div>
                                <div class="flex items-center justify-between text-slate-600">
                                    <span>Subtotal Denda Terlambat</span>
                                    <span class="font-bold text-rose-600 font-mono">Rp{{ number_format($calculatedLateFee, 0, ',', '.') }}</span>
                                </div>

                                <template x-if="itemCondition === 'perawatan_rusak'">
                                    <div class="flex items-center justify-between text-slate-600 pt-1 border-t border-dashed border-slate-200">
                                        <span>Denda Kerusakan Fisik</span>
                                        <span class="font-bold text-rose-600 font-mono" x-text="'Rp' + damageTotal.toLocaleString('id-ID')"></span>
                                    </div>
                                </template>

                                <div class="pt-3 border-t border-slate-200 flex items-baseline justify-between">
                                    <span class="text-xs font-extrabold text-slate-800 uppercase tracking-wider">Total Denda</span>
                                    <span class="text-2xl font-extrabold text-slate-900 font-display"
                                          x-text="'Rp' + (lateFee + damageTotal).toLocaleString('id-ID')">
                                        Rp{{ number_format($calculatedLateFee, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Kartu Pelunasan di Store -->
                        <div class="bg-white p-6 sm:p-7 rounded-3xl border border-slate-200 shadow-sm space-y-4">
                            <h3 class="text-base font-bold text-slate-900 font-display">
                                Pelunasan di Store
                            </h3>

                            <input type="hidden" name="late_fee_paid" :value="finePaid ? 1 : 0">

                            <!-- Catatan Referensi Pembayaran Kasir -->
                            <div class="space-y-1.5">
                                <label for="fine_notes_{{ $rental->id }}" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                    Referensi Pembayaran Denda (Opsional)
                                </label>
                                <input type="text"
                                       id="fine_notes_{{ $rental->id }}"
                                       name="fine_notes"
                                       placeholder="Contoh: Tunai di kasir / QRIS Ref: #12345"
                                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs focus:outline-none focus:border-brand-500">
                            </div>

                            <!-- Checkbox Konfirmasi Denda Lunas -->
                            <div class="space-y-2 pt-1 text-xs text-slate-700 font-semibold">
                                <label class="flex items-center gap-2.5 cursor-pointer">
                                    <input type="checkbox"
                                           x-model="finePaid"
                                           class="w-4 h-4 text-emerald-600 rounded border-slate-300 focus:ring-emerald-500">
                                    <span>Tandai denda telah diterima/dilunasi di store</span>
                                </label>
                            </div>

                            <!-- Tombol Submit Form -->
                            <div class="pt-3 space-y-2">
                                <button type="submit"
                                        class="w-full py-3.5 px-4 rounded-2xl font-bold text-sm text-white bg-brand-600 hover:bg-brand-700 shadow-lg shadow-brand-500/25 transition flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    <span>Tutup Transaksi & Selesaikan</span>
                                </button>
                            </div>

                            <p class="text-[11px] text-slate-400 leading-relaxed">
                                Transaksi akan ditandai selesai (<code class="bg-slate-100 px-1 py-0.5 rounded text-slate-600 font-mono">completed</code>), stok barang kembali tersedia, dan denda akan dicatat ke laporan toko.
                            </p>
                        </div>

                    </div>

                </div>
            </form>
        </div>
    @endforeach

    <!-- TABEL RIWAYAT PENGEMBALIAN & DENDA YANG SELESAI DIPROSES -->
    <div class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-200 shadow-sm space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-bold text-slate-900 font-display">
                    Riwayat Pengembalian & Denda Selesai
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">
                    15 transaksi terakhir yang telah selesai diinspeksi dan ditutup.
                </p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs sm:text-sm">
                <thead>
                    <tr class="border-b border-slate-200 text-[11px] uppercase tracking-wider text-slate-400 font-bold">
                        <th class="pb-3">Pesanan & Pelanggan</th>
                        <th class="pb-3">Gear Fotografi</th>
                        <th class="pb-3">Waktu Selesai</th>
                        <th class="pb-3">Kondisi Akhir</th>
                        <th class="pb-3">Total Denda</th>
                        <th class="pb-3">Status Pelunasan</th>
                        <th class="pb-3">Berkas Foto</th>
                        <th class="pb-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($completedHistory as $comp)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="py-4">
                                <div class="font-bold text-slate-900 font-mono">{{ $comp->rental_code }}</div>
                                <div class="text-xs text-slate-500">{{ $comp->user->name }}</div>
                            </td>
                            <td class="py-4 text-slate-700">
                                {{ $comp->items->pluck('product.name')->join(', ') }}
                            </td>
                            <td class="py-4 text-slate-600 text-xs">
                                {{ $comp->returned_at ? $comp->returned_at->translatedFormat('d M Y · H:i') : $comp->updated_at->translatedFormat('d M Y') }}
                            </td>
                            <td class="py-4">
                                @if($comp->item_condition_after === 'layak_tersedia')
                                    <span class="inline-block px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        ✓ Layak & Tersedia
                                    </span>
                                @else
                                    <span class="inline-block px-2.5 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                        ⚙ Dalam Perawatan
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 font-mono font-bold text-slate-900">
                                @if($comp->fines->isNotEmpty())
                                    Rp{{ number_format($comp->fines->sum('amount'), 0, ',', '.') }}
                                @else
                                    <span class="text-slate-400 font-normal">Rp0</span>
                                @endif
                            </td>
                            <td class="py-4">
                                @if($comp->fines->isEmpty())
                                    <span class="text-xs text-slate-400">Tanpa Denda</span>
                                @else
                                    @php
                                        $hasUnpaid = $comp->fines->contains('is_paid', false);
                                    @endphp
                                    @if($hasUnpaid)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-700">
                                            Belum Lunas
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700">
                                            ✓ Lunas
                                        </span>
                                    @endif
                                @endif
                            </td>
                            <td class="py-4">
                                @php
                                    $hasPhotos = !empty($comp->return_photo) || $comp->fines->whereNotNull('photo')->isNotEmpty();
                                @endphp
                                @if($hasPhotos)
                                    <div class="flex items-center gap-2 flex-wrap">
                                        @if(!empty($comp->return_photo))
                                            <div class="flex items-center gap-1 bg-slate-50 border border-slate-200 rounded-lg p-1">
                                                <a href="{{ asset('storage/' . $comp->return_photo) }}" target="_blank" title="Lihat Foto Pengembalian">
                                                    <img src="{{ asset('storage/' . $comp->return_photo) }}" alt="Foto Pengembalian" class="w-7 h-7 object-cover rounded">
                                                </a>
                                                <form method="POST" action="{{ route('admin.returns.delete_return_photo', $comp->id) }}" onsubmit="return confirm('Hapus foto pengembalian barang {{ $comp->rental_code }}? Data sewa dan pengembalian tetap aman.');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" title="Hapus Foto Pengembalian" class="text-rose-500 hover:text-rose-700 p-0.5">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                    </button>
                                                </form>
                                            </div>
                                        @endif
                                        @foreach($comp->fines->whereNotNull('photo') as $fineWithPhoto)
                                            <div class="flex items-center gap-1 bg-rose-50 border border-rose-200 rounded-lg p-1">
                                                <a href="{{ asset('storage/' . $fineWithPhoto->photo) }}" target="_blank" title="Foto Kerusakan Denda #{{ $fineWithPhoto->id }}">
                                                    <img src="{{ asset('storage/' . $fineWithPhoto->photo) }}" alt="Foto Kerusakan" class="w-7 h-7 object-cover rounded">
                                                </a>
                                                <form method="POST" action="{{ route('admin.returns.delete_fine_photo', $fineWithPhoto->id) }}" onsubmit="return confirm('Hapus lampiran foto denda #{{ $fineWithPhoto->id }}? Data denda tetap aman.');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" title="Hapus Foto Denda" class="text-rose-500 hover:text-rose-700 p-0.5">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                    </button>
                                                </form>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-slate-100 text-slate-500">
                                        Tanpa berkas
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 text-right">
                                @if($comp->fines->isNotEmpty() && $comp->fines->contains('is_paid', false))
                                    @foreach($comp->fines->where('is_paid', false) as $unpaidFine)
                                        <form method="POST" action="{{ route('admin.returns.pay_fine', $unpaidFine->id) }}" class="inline-block">
                                            @csrf
                                            <button type="submit"
                                                    onclick="return confirm('Tandai denda Rp{{ number_format($unpaidFine->amount, 0, ',', '.') }} untuk {{ $comp->rental_code }} telah dilunasi?');"
                                                    class="px-2.5 py-1 rounded-lg text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white transition">
                                                Tandai Lunas
                                            </button>
                                        </form>
                                    @endforeach
                                @else
                                    <span class="text-xs text-slate-400">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400 text-xs">
                                Belum ada riwayat pengembalian yang diselesaikan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
