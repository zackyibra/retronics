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

    <!-- Date Range & Export Toolbar -->
    <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-sm flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <form method="GET" action="{{ route('admin.reports.index') }}" class="flex flex-col sm:flex-row items-center gap-3 flex-1 max-w-2xl">
            <!-- Dari Tanggal -->
            <div class="p-2.5 rounded-2xl border border-slate-200 bg-slate-50/50 flex-1 w-full">
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">
                    Dari tanggal
                </label>
                <input type="date"
                       name="start_date"
                       value="{{ $startDate ?? '' }}"
                       class="w-full bg-transparent text-xs sm:text-sm font-semibold text-slate-900 focus:outline-none">
            </div>

            <!-- Sampai Tanggal -->
            <div class="p-2.5 rounded-2xl border border-slate-200 bg-slate-50/50 flex-1 w-full">
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">
                    Sampai tanggal
                </label>
                <input type="date"
                       name="end_date"
                       value="{{ $endDate ?? '' }}"
                       class="w-full bg-transparent text-xs sm:text-sm font-semibold text-slate-900 focus:outline-none">
            </div>

            <button type="submit"
                    class="w-full sm:w-auto px-5 py-3 rounded-xl font-bold text-xs text-white bg-brand-600 hover:bg-brand-700 shadow-md shadow-brand-500/20 transition self-stretch sm:self-auto flex items-center justify-center">
                Terapkan
            </button>

            @if($startDate || $endDate)
                <a href="{{ route('admin.reports.index') }}"
                   class="w-full sm:w-auto px-4 py-3 rounded-xl font-bold text-xs text-slate-600 hover:bg-slate-100 transition self-stretch sm:self-auto flex items-center justify-center">
                    Reset
                </a>
            @endif
        </form>

        <div class="flex items-center gap-2 self-start lg:self-auto">
            <a href="{{ route('admin.reports.index', array_merge(request()->query(), ['export' => 'csv'])) }}"
               class="px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs transition shadow-sm flex items-center gap-1.5">
                <span>↓ Ekspor CSV</span>
            </a>
            <button type="button"
                    onclick="window.print()"
                    class="px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs transition shadow-sm flex items-center gap-1.5">
                <span>↓ Ekspor PDF</span>
            </button>
        </div>
    </div>

    <!-- 4 Metric Cards (100% Real Database Aggregations) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- 1. Omset sewa terverifikasi -->
        <div class="p-5 sm:p-6 rounded-3xl bg-white border border-slate-200 shadow-sm space-y-1">
            <span class="text-xs text-slate-500 font-medium block">Omset sewa terverifikasi</span>
            <div class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-display">
                Rp{{ number_format($totalRevenue, 0, ',', '.') }}
            </div>
            <span class="text-[11px] text-brand-600 font-semibold block">
                {{ $verifiedCount }} transaksi · rata-rata Rp{{ number_format($avgRevenue, 0, ',', '.') }}
            </span>
        </div>

        <!-- 2. Voucher terealisasi -->
        <div class="p-5 sm:p-6 rounded-3xl bg-white border border-slate-200 shadow-sm space-y-1">
            <span class="text-xs text-slate-500 font-medium block">Voucher terealisasi</span>
            <div class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-display">
                Rp{{ number_format($voucherTotalDiscount, 0, ',', '.') }}
            </div>
            <span class="text-[11px] text-brand-600 font-semibold block">
                {{ $voucherUsedCount }} penggunaan pada transaksi lunas
            </span>
        </div>

        <!-- 3. Denda diterima -->
        <div class="p-5 sm:p-6 rounded-3xl bg-white border border-slate-200 shadow-sm space-y-1">
            <span class="text-xs text-slate-500 font-medium block">Denda diterima</span>
            <div class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-display">
                Rp{{ number_format($totalFinesPaid, 0, ',', '.') }}
            </div>
            <span class="text-[11px] text-emerald-600 font-semibold block">
                {{ $countFinesPaid }} pembayaran denda lunas
            </span>
        </div>

        <!-- 4. Denda belum lunas -->
        <div class="p-5 sm:p-6 rounded-3xl bg-white border border-slate-200 shadow-sm space-y-1">
            <span class="text-xs text-slate-500 font-medium block">Denda belum lunas</span>
            <div class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-display">
                Rp{{ number_format($totalFinesUnpaid, 0, ',', '.') }}
            </div>
            <span class="text-[11px] {{ $countFinesUnpaid > 0 ? 'text-rose-600' : 'text-slate-400' }} font-semibold block">
                {{ $countFinesUnpaid }} denda belum lunas · tidak masuk penerimaan
            </span>
        </div>
    </div>

    <!-- Tren Penerimaan Sewa Bar Chart -->
    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-sm space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-bold text-slate-900 font-display">
                    Tren penerimaan sewa
                </h2>
                <span class="text-xs text-slate-400">Total Periode: Rp{{ number_format($totalRevenue, 0, ',', '.') }}</span>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-600">
                @if($startDate && $endDate)
                    {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d M') }} – {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d M Y') }}
                @elseif($startDate)
                    Mulai {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d M Y') }}
                @elseif($endDate)
                    Hingga {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d M Y') }}
                @else
                    Semua Periode Transaksi
                @endif
            </span>
        </div>

        @if($trendData->isNotEmpty())
            <!-- CSS Bar Chart (Dynamic from DB) -->
            <div class="h-48 flex items-end justify-start gap-2 sm:gap-4 px-2 pt-6 pb-2 border-b border-slate-100 overflow-x-auto">
                @foreach($trendData as $trend)
                    @php
                        $barHeight = max(15, min(95, round(($trend->revenue / $maxTrendRevenue) * 90)));
                    @endphp
                    <div class="flex-1 min-w-[50px] max-w-[75px] flex flex-col items-center gap-1.5 h-full justify-end">
                        <span class="text-[10px] text-slate-500 font-bold whitespace-nowrap">
                            @if($trend->revenue >= 1000000)
                                {{ number_format($trend->revenue / 1000000, 1, ',', '.') }} jt
                            @else
                                {{ number_format($trend->revenue / 1000, 0, ',', '.') }} rb
                            @endif
                        </span>
                        <div class="w-full bg-brand-600 hover:bg-brand-700 rounded-t-xl transition-all cursor-pointer"
                             style="height: {{ $barHeight }}%;"
                             title="{{ \Carbon\Carbon::parse($trend->date)->translatedFormat('d M Y') }}: Rp{{ number_format($trend->revenue, 0, ',', '.') }} ({{ $trend->count }} transaksi)">
                        </div>
                        <span class="text-[11px] font-semibold text-slate-600 mt-1 whitespace-nowrap">
                            {{ \Carbon\Carbon::parse($trend->date)->translatedFormat('d M') }}
                        </span>
                    </div>
                @endforeach
            </div>
        @else
            <div class="py-12 text-center text-slate-400 text-xs sm:text-sm bg-slate-50/50 rounded-2xl border border-dashed border-slate-200">
                Belum ada transaksi sewa terverifikasi pada rentang tanggal yang dipilih.
            </div>
        @endif

        <!-- Blue Notice Box -->
        <div class="p-4 rounded-2xl bg-blue-50/70 border border-blue-100 text-xs text-brand-800 leading-relaxed">
            Hanya pembayaran yang telah diterima & diverifikasi admin yang dihitung sebagai omset. Pesanan berstatus menunggu bayar, menunggu verifikasi, dan batal tidak dihitung. Omset sewa sudah bersih dari potongan diskon durasi dan voucher promo.
        </div>
    </div>

    <!-- Rekap Terverifikasi per Periode Table -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-4">
        <h2 class="text-lg font-bold text-slate-900 font-display">
            Rekap terverifikasi per tanggal
        </h2>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs sm:text-sm">
                <thead>
                    <tr class="border-b border-slate-200 text-[11px] uppercase tracking-wider text-slate-400 font-bold">
                        <th class="pb-3">Tanggal Mulai</th>
                        <th class="pb-3">Jumlah Transaksi</th>
                        <th class="pb-3">Omset Bersih</th>
                        <th class="pb-3">Denda Diterima</th>
                        <th class="pb-3 text-right">Penerimaan Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($dailySummary as $summary)
                        <tr class="hover:bg-slate-50/50">
                            <td class="py-3.5 font-bold text-slate-900">{{ $summary->formatted_date }}</td>
                            <td class="py-3.5 font-semibold text-slate-700">{{ $summary->tx_count }} transaksi</td>
                            <td class="py-3.5 text-slate-800">Rp{{ number_format($summary->net_revenue, 0, ',', '.') }}</td>
                            <td class="py-3.5 text-slate-600">Rp{{ number_format($summary->fines_paid, 0, ',', '.') }}</td>
                            <td class="py-3.5 text-right font-bold text-slate-900">Rp{{ number_format($summary->total_income, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-slate-400">
                                Belum ada transaksi terverifikasi pada periode yang dipilih.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if($dailySummary->isNotEmpty())
                    <tfoot>
                        <tr class="border-t-2 border-slate-900 font-extrabold text-xs sm:text-sm">
                            <td class="pt-3.5 uppercase font-display text-slate-900">TOTAL</td>
                            <td class="pt-3.5 text-slate-900">{{ $totalPeriodicTx }} transaksi</td>
                            <td class="pt-3.5 text-slate-900">Rp{{ number_format($totalPeriodicRevenue, 0, ',', '.') }}</td>
                            <td class="pt-3.5 text-slate-900">Rp{{ number_format($totalPeriodicFines, 0, ',', '.') }}</td>
                            <td class="pt-3.5 text-right text-brand-600 font-display text-base">Rp{{ number_format($totalPeriodicIncome, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>

    <!-- Bottom Row: Voucher Usage & Fines Summary -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">

        <!-- Penggunaan Voucher (lg:col-span-6) -->
        <div class="lg:col-span-6 bg-white p-6 sm:p-7 rounded-3xl border border-slate-200 shadow-sm flex flex-col justify-between">
            <div>
                <h3 class="text-base font-bold text-slate-900 font-display mb-4">
                    Penggunaan voucher promo
                </h3>

                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-100 text-slate-400 font-bold uppercase text-[10px]">
                            <th class="pb-2">Kode Promo</th>
                            <th class="pb-2">Dipakai</th>
                            <th class="pb-2 text-right">Total Potongan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($voucherBreakdown as $voucher)
                            <tr>
                                <td class="py-2.5 font-mono font-bold text-slate-900">{{ $voucher->promo_code }}</td>
                                <td class="py-2.5 text-slate-700">{{ $voucher->used_count }}x</td>
                                <td class="py-2.5 text-right text-emerald-600 font-semibold">Rp{{ number_format($voucher->total_discount, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="py-6 text-center text-slate-400">
                                    Tidak ada voucher yang digunakan pada transaksi periode ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($voucherBreakdown->isNotEmpty())
                        <tfoot>
                            <tr class="border-t border-slate-200 font-bold">
                                <td class="pt-2 text-slate-900">TOTAL</td>
                                <td class="pt-2 text-slate-900">{{ $voucherUsedCount }}x</td>
                                <td class="pt-2 text-right text-emerald-600 font-display">Rp{{ number_format($voucherTotalDiscount, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>

            <p class="text-[11px] text-slate-400 mt-4">
                Penggunaan voucher dihitung khusus dari transaksi sewa yang telah terkonfirmasi sah oleh admin.
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
                        <span class="font-bold text-slate-900">{{ $lateOrdersCount }}</span>
                    </div>
                    <div class="flex items-center justify-between text-slate-600">
                        <span>Denda diterima (lunas)</span>
                        <span class="font-bold text-emerald-600">Rp{{ number_format($totalFinesPaid, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex items-center justify-between text-slate-600">
                        <span>Denda belum lunas</span>
                        <span class="font-bold {{ $totalFinesUnpaid > 0 ? 'text-rose-600' : 'text-slate-800' }}">Rp{{ number_format($totalFinesUnpaid, 0, ',', '.') }}</span>
                    </div>
                </div>

                @if($unpaidFinesList->isNotEmpty())
                    <div class="pt-3 border-t border-slate-100">
                        <span class="text-[10px] uppercase tracking-wider font-bold text-slate-400 block mb-1.5">Denda belum lunas terbaru:</span>
                        <div class="space-y-1.5 max-h-32 overflow-y-auto pr-1">
                            @foreach($unpaidFinesList as $unpaidFine)
                                <div class="text-[11px] text-slate-600 flex items-center justify-between bg-slate-50 p-1.5 rounded-lg">
                                    <span class="truncate max-w-[200px]">
                                        {{ $unpaidFine->rental->user->name ?? 'Penyewa' }} ({{ $unpaidFine->rental->rental_code ?? '-' }})
                                    </span>
                                    <span class="font-bold text-rose-600 ml-2">
                                        Rp{{ number_format($unpaidFine->amount, 0, ',', '.') }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <p class="text-[11px] text-slate-400">
                    Tarif denda keterlambatan Rp50.000/hari/unit. Denda belum lunas tidak dimasukkan ke dalam penerimaan kas omset sewa.
                </p>
            </div>

            <div class="pt-4">
                @if($countFinesUnpaid > 0)
                    <span class="inline-block px-3 py-1 rounded-full text-[11px] font-bold bg-rose-50 text-rose-700 border border-rose-100">
                        {{ $countFinesUnpaid }} denda belum lunas perlu ditindaklanjuti
                    </span>
                @else
                    <span class="inline-block px-3 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">
                        Semua denda selesai / lunas
                    </span>
                @endif
            </div>
        </div>

    </div>

</div>
@endsection
