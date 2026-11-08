@extends('admin.layout')

@section('title', 'Laporan bisnis')

@section('content')
<div class="space-y-6">

    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-xs text-slate-500">
        <span>Admin</span>
        <span class="text-slate-300">›</span>
        <span class="text-slate-800 font-semibold">Laporan bisnis</span>
    </nav>

    <!-- Header -->
    <div>
        <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 font-display tracking-tight">
            Laporan bisnis
        </h1>
        <p class="text-slate-500 text-xs sm:text-sm mt-1">
            Omset terverifikasi, keterlambatan, dan efektivitas promo dalam satu rekap.
        </p>
    </div>

    <!-- Date Range & Export Toolbar (Matching Figma Screenshot) -->
    <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-sm flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <form method="GET" action="{{ route('admin.reports.index') }}" class="flex flex-col sm:flex-row items-center gap-3 flex-1 max-w-2xl">
            <!-- Dari Tanggal -->
            <div class="p-2.5 rounded-2xl border border-slate-200 bg-slate-50/50 flex-1 w-full">
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">
                    Dari tanggal
                </label>
                <input type="date"
                       name="start_date"
                       value="{{ request('start_date', date('Y-m-01')) }}"
                       class="w-full bg-transparent text-xs sm:text-sm font-semibold text-slate-900 focus:outline-none">
            </div>

            <!-- Sampai Tanggal -->
            <div class="p-2.5 rounded-2xl border border-slate-200 bg-slate-50/50 flex-1 w-full">
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">
                    Sampai tanggal
                </label>
                <input type="date"
                       name="end_date"
                       value="{{ request('end_date', date('Y-m-d')) }}"
                       class="w-full bg-transparent text-xs sm:text-sm font-semibold text-slate-900 focus:outline-none">
            </div>

            <button type="submit"
                    class="w-full sm:w-auto px-5 py-3 rounded-xl font-bold text-xs text-white bg-brand-600 hover:bg-brand-700 shadow-md shadow-brand-500/20 transition self-stretch sm:self-auto flex items-center justify-center">
                Terapkan
            </button>
        </form>

        <div class="flex items-center gap-2 self-start lg:self-auto">
            <button type="button"
                    onclick="window.print()"
                    class="px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs transition shadow-sm flex items-center gap-1.5">
                <span>↓ Ekspor CSV</span>
            </button>
            <button type="button"
                    onclick="window.print()"
                    class="px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs transition shadow-sm flex items-center gap-1.5">
                <span>↓ Ekspor PDF</span>
            </button>
        </div>
    </div>

    <!-- 4 Metric Cards (Matching Figma Screenshot) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- 1. Omset sewa terverifikasi -->
        <div class="p-5 sm:p-6 rounded-3xl bg-white border border-slate-200 shadow-sm space-y-1">
            <span class="text-xs text-slate-500 font-medium block">Omset sewa terverifikasi</span>
            <div class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-display">
                Rp12.840.000
            </div>
            <span class="text-[11px] text-brand-600 font-semibold block">20 transaksi · rata-rata Rp642.000</span>
        </div>

        <!-- 2. Voucher terealisasi -->
        <div class="p-5 sm:p-6 rounded-3xl bg-white border border-slate-200 shadow-sm space-y-1">
            <span class="text-xs text-slate-500 font-medium block">Voucher terealisasi</span>
            <div class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-display">
                Rp480.000
            </div>
            <span class="text-[11px] text-brand-600 font-semibold block">8 penggunaan pada transaksi lunas</span>
        </div>

        <!-- 3. Denda diterima -->
        <div class="p-5 sm:p-6 rounded-3xl bg-white border border-slate-200 shadow-sm space-y-1">
            <span class="text-xs text-slate-500 font-medium block">Denda diterima</span>
            <div class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-display">
                Rp150.000
            </div>
            <span class="text-[11px] text-slate-400 font-semibold block">3 hari-unit · 2 pesanan selesai</span>
        </div>

        <!-- 4. Denda belum lunas -->
        <div class="p-5 sm:p-6 rounded-3xl bg-white border border-slate-200 shadow-sm space-y-1">
            <span class="text-xs text-slate-500 font-medium block">Denda belum lunas</span>
            <div class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-display">
                Rp200.000
            </div>
            <span class="text-[11px] text-rose-600 font-semibold block">1 pesanan · tidak masuk penerimaan</span>
        </div>
    </div>

    <!-- Tren Penerimaan Sewa Bar Chart (Matching Figma) -->
    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-sm space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-bold text-slate-900 font-display">
                    Tren penerimaan sewa
                </h2>
                <span class="text-xs text-slate-400">Rp2,5 jt</span>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-600">
                18–24 {{ \Carbon\Carbon::now()->translatedFormat('M Y') }}
            </span>
        </div>

        <!-- CSS Bar Chart (Faithfully matching Figma screenshot) -->
        <div class="h-48 flex items-end justify-between gap-2 sm:gap-4 px-2 pt-6 pb-2 border-b border-slate-100">
            <div class="flex-1 flex flex-col items-center gap-1.5 h-full justify-end">
                <span class="text-[10px] text-slate-400 font-bold">0,90 jt</span>
                <div class="w-full max-w-[56px] bg-brand-600 rounded-t-xl transition-all" style="height: 36%;"></div>
                <span class="text-[11px] font-semibold text-slate-600 mt-1">18 Okt</span>
            </div>

            <div class="flex-1 flex flex-col items-center gap-1.5 h-full justify-end">
                <span class="text-[10px] text-slate-400 font-bold">1,20 jt</span>
                <div class="w-full max-w-[56px] bg-brand-600 rounded-t-xl transition-all" style="height: 48%;"></div>
                <span class="text-[11px] font-semibold text-slate-600 mt-1">19 Okt</span>
            </div>

            <div class="flex-1 flex flex-col items-center gap-1.5 h-full justify-end">
                <span class="text-[10px] text-slate-400 font-bold">1,30 jt</span>
                <div class="w-full max-w-[56px] bg-brand-600 rounded-t-xl transition-all" style="height: 52%;"></div>
                <span class="text-[11px] font-semibold text-slate-600 mt-1">20 Okt</span>
            </div>

            <div class="flex-1 flex flex-col items-center gap-1.5 h-full justify-end">
                <span class="text-[10px] text-slate-400 font-bold">1,60 jt</span>
                <div class="w-full max-w-[56px] bg-brand-600 rounded-t-xl transition-all" style="height: 64%;"></div>
                <span class="text-[11px] font-semibold text-slate-600 mt-1">21 Okt</span>
            </div>

            <div class="flex-1 flex flex-col items-center gap-1.5 h-full justify-end">
                <span class="text-[10px] text-slate-400 font-bold">2,20 jt</span>
                <div class="w-full max-w-[56px] bg-brand-600 rounded-t-xl transition-all" style="height: 88%;"></div>
                <span class="text-[11px] font-semibold text-slate-600 mt-1">22 Okt</span>
            </div>

            <div class="flex-1 flex flex-col items-center gap-1.5 h-full justify-end">
                <span class="text-[10px] text-slate-400 font-bold">1,95 jt</span>
                <div class="w-full max-w-[56px] bg-brand-600 rounded-t-xl transition-all" style="height: 78%;"></div>
                <span class="text-[11px] font-semibold text-slate-600 mt-1">23 Okt</span>
            </div>

            <div class="flex-1 flex flex-col items-center gap-1.5 h-full justify-end">
                <span class="text-[10px] text-slate-400 font-bold">1,49 jt</span>
                <div class="w-full max-w-[56px] bg-[#0b1320] rounded-t-xl transition-all" style="height: 60%;"></div>
                <span class="text-[11px] font-semibold text-slate-900 mt-1">24 Okt</span>
            </div>
        </div>

        <!-- Blue Notice Box -->
        <div class="p-4 rounded-2xl bg-blue-50/70 border border-blue-100 text-xs text-brand-800 leading-relaxed">
            Hanya pembayaran yang diterima admin masuk omset. Pesanan menunggu bayar, menunggu verifikasi, dan batal tidak dihitung. Omset sewa sudah bersih dari potongan promo dan durasi.
        </div>
    </div>

    <!-- Rekap Terverifikasi per Periode Table (Matching Figma Screenshot) -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-4">
        <h2 class="text-lg font-bold text-slate-900 font-display">
            Rekap terverifikasi per periode
        </h2>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs sm:text-sm">
                <thead>
                    <tr class="border-b border-slate-200 text-[11px] uppercase tracking-wider text-slate-400 font-bold">
                        <th class="pb-3">Periode</th>
                        <th class="pb-3">Transaksi</th>
                        <th class="pb-3">Omset bersih</th>
                        <th class="pb-3">Denda diterima</th>
                        <th class="pb-3 text-right">Penerimaan total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <tr class="hover:bg-slate-50/50">
                        <td class="py-3.5 font-bold text-slate-900">1–20 Okt 2026</td>
                        <td class="py-3.5 font-semibold text-slate-700">11</td>
                        <td class="py-3.5 text-slate-800">Rp5.600.000</td>
                        <td class="py-3.5 text-slate-600">Rp100.000</td>
                        <td class="py-3.5 text-right font-bold text-slate-900">Rp5.700.000</td>
                    </tr>
                    <tr class="hover:bg-slate-50/50">
                        <td class="py-3.5 font-bold text-slate-900">21 Okt 2026</td>
                        <td class="py-3.5 font-semibold text-slate-700">3</td>
                        <td class="py-3.5 text-slate-800">Rp1.600.000</td>
                        <td class="py-3.5 text-slate-600">Rp0</td>
                        <td class="py-3.5 text-right font-bold text-slate-900">Rp1.600.000</td>
                    </tr>
                    <tr class="hover:bg-slate-50/50">
                        <td class="py-3.5 font-bold text-slate-900">22 Okt 2026</td>
                        <td class="py-3.5 font-semibold text-slate-700">3</td>
                        <td class="py-3.5 text-slate-800">Rp2.200.000</td>
                        <td class="py-3.5 text-slate-600">Rp50.000</td>
                        <td class="py-3.5 text-right font-bold text-slate-900">Rp2.250.000</td>
                    </tr>
                    <tr class="hover:bg-slate-50/50">
                        <td class="py-3.5 font-bold text-slate-900">23 Okt 2026</td>
                        <td class="py-3.5 font-semibold text-slate-700">2</td>
                        <td class="py-3.5 text-slate-800">Rp1.950.000</td>
                        <td class="py-3.5 text-slate-600">Rp0</td>
                        <td class="py-3.5 text-right font-bold text-slate-900">Rp1.950.000</td>
                    </tr>
                    <tr class="hover:bg-slate-50/50">
                        <td class="py-3.5 font-bold text-slate-900">24 Okt 2026</td>
                        <td class="py-3.5 font-semibold text-slate-700">1</td>
                        <td class="py-3.5 text-slate-800">Rp1.490.000</td>
                        <td class="py-3.5 text-slate-600">Rp0</td>
                        <td class="py-3.5 text-right font-bold text-slate-900">Rp1.490.000</td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr class="border-t-2 border-slate-900 font-extrabold text-xs sm:text-sm">
                        <td class="pt-3.5 uppercase font-display text-slate-900">TOTAL</td>
                        <td class="pt-3.5 text-slate-900">20</td>
                        <td class="pt-3.5 text-slate-900">Rp12.840.000</td>
                        <td class="pt-3.5 text-slate-900">Rp150.000</td>
                        <td class="pt-3.5 text-right text-brand-600 font-display text-base">Rp12.990.000</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Bottom Row: Voucher Usage & Fines Summary (Matching Figma Screenshot) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">

        <!-- Penggunaan Voucher (lg:col-span-6) -->
        <div class="lg:col-span-6 bg-white p-6 sm:p-7 rounded-3xl border border-slate-200 shadow-sm flex flex-col justify-between">
            <div>
                <h3 class="text-base font-bold text-slate-900 font-display mb-4">
                    Penggunaan voucher
                </h3>

                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-100 text-slate-400 font-bold uppercase text-[10px]">
                            <th class="pb-2">Kode</th>
                            <th class="pb-2">Dipakai</th>
                            <th class="pb-2 text-right">Potongan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr>
                            <td class="py-2.5 font-mono font-bold text-slate-900">RETRONICS10</td>
                            <td class="py-2.5 text-slate-700">6</td>
                            <td class="py-2.5 text-right text-emerald-600 font-semibold">Rp430.000</td>
                        </tr>
                        <tr>
                            <td class="py-2.5 font-mono font-bold text-slate-900">KREASI25</td>
                            <td class="py-2.5 text-slate-700">2</td>
                            <td class="py-2.5 text-right text-emerald-600 font-semibold">Rp50.000</td>
                        </tr>
                        <tr>
                            <td class="py-2.5 font-mono font-bold text-slate-900">LIGHTING10</td>
                            <td class="py-2.5 text-slate-700">0</td>
                            <td class="py-2.5 text-right text-slate-400">Rp0</td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr class="border-t border-slate-200 font-bold">
                            <td class="pt-2 text-slate-900">TOTAL</td>
                            <td class="pt-2 text-slate-900">8</td>
                            <td class="pt-2 text-right text-emerald-600 font-display">Rp480.000</td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <p class="text-[11px] text-slate-400 mt-4">
                Penggunaan dihitung dari transaksi terverifikasi. Pesanan 0008 dan 0007 belum masuk rekap voucher.
            </p>
        </div>

        <!-- Keterlambatan & Denda (lg:col-span-6) -->
        <div class="lg:col-span-6 bg-white p-6 sm:p-7 rounded-3xl border border-slate-200 shadow-sm flex flex-col justify-between">
            <div class="space-y-4">
                <h3 class="text-base font-bold text-slate-900 font-display">
                    Keterlambatan & denda
                </h3>

                <div class="space-y-2 text-xs">
                    <div class="flex items-center justify-between text-slate-600">
                        <span>Pesanan terlambat selesai</span>
                        <span class="font-bold text-slate-900">2</span>
                    </div>
                    <div class="flex items-center justify-between text-slate-600">
                        <span>Denda diterima</span>
                        <span class="font-bold text-emerald-600">Rp150.000</span>
                    </div>
                    <div class="flex items-center justify-between text-slate-600">
                        <span>Masih berjalan · Arga / 0004</span>
                        <span class="font-bold text-rose-600">Rp200.000</span>
                    </div>
                </div>

                <p class="text-[11px] text-slate-400">
                    Tarif Rp50.000/hari/unit. Arga: 2 hari × 2 unit = 4 hari-unit, belum dilunasi. Denda berjalan tidak dicampur dengan penerimaan kas.
                </p>
            </div>

            <div class="pt-4">
                <span class="inline-block px-3 py-1 rounded-full text-[11px] font-bold bg-rose-50 text-rose-700 border border-rose-100">
                    1 pengembalian perlu ditindaklanjuti
                </span>
            </div>
        </div>

    </div>

</div>
@endsection
