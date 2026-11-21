@extends('admin.layout')

@section('title', 'Ringkasan operasional')

@section('content')
<div class="space-y-6">

    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-xs text-slate-500">
        <span>Admin</span>
        <span class="text-slate-300">›</span>
        <span class="text-slate-800 font-semibold">Ringkasan operasional</span>
    </nav>

    <!-- Header Title -->
    <div>
        <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 font-display tracking-tight">
            Ringkasan operasional
        </h1>
        <p class="text-slate-500 text-xs sm:text-sm mt-1">
            Pantau unit, pembayaran, dan pekerjaan store hari ini.
        </p>
    </div>

    <!-- 4 Top KPI Cards (Matching Figma Screenshot) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- 1. Omset terverifikasi -->
        <div class="p-5 sm:p-6 rounded-3xl bg-white border border-slate-200 shadow-sm space-y-2">
            <span class="text-xs text-slate-500 font-medium block">
                Omset terverifikasi · {{ \Carbon\Carbon::now()->translatedFormat('F') }}
            </span>
            <div class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-display tracking-tight">
                Rp{{ number_format($stats['verified_revenue'], 0, ',', '.') }}
            </div>
            <span class="text-[11px] text-brand-600 font-semibold block">
                {{ $stats['paid_transactions_count'] }} transaksi lunas · tidak termasuk denda
            </span>
        </div>

        <!-- 2. Unit sedang disewa -->
        <div class="p-5 sm:p-6 rounded-3xl bg-white border border-slate-200 shadow-sm space-y-2">
            <span class="text-xs text-slate-500 font-medium block">
                Unit sedang disewa
            </span>
            <div class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-display tracking-tight">
                {{ $stats['units_in_rent'] }} unit
            </div>
            <span class="text-[11px] text-slate-400 font-medium block">
                Dari {{ $stats['total_physical_stock'] }} unit fisik
            </span>
        </div>

        <!-- 3. Pesanan aktif -->
        <div class="p-5 sm:p-6 rounded-3xl bg-white border border-slate-200 shadow-sm space-y-2">
            <span class="text-xs text-slate-500 font-medium block">
                Pesanan aktif
            </span>
            <div class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-display tracking-tight">
                {{ $stats['active_orders'] }}
            </div>
            <span class="text-[11px] text-slate-400 font-medium block">
                {{ $stats['pending_payment_count'] }} bayar · {{ $stats['pending_verifications'] }} verifikasi · {{ $stats['ongoing_rentals'] }} berjalan
            </span>
        </div>

        <!-- 4. Perlu tindakan -->
        <div class="p-5 sm:p-6 rounded-3xl bg-white border border-slate-200 shadow-sm space-y-2">
            <span class="text-xs text-slate-500 font-medium block">
                Perlu tindakan
            </span>
            <div class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-display tracking-tight">
                {{ $stats['action_required'] }}
            </div>
            <span class="text-[11px] text-brand-600 font-semibold block">
                {{ $stats['pending_doc_verifications'] }} verif dokumen · {{ $stats['pending_verifications'] }} bayar · {{ $stats['pending_returns'] }} sewa
            </span>
        </div>
    </div>

    <!-- Alert Banner Tindakan Operasional -->
    @php
        $alerts = [];
        if (($stats['pending_doc_verifications'] ?? 0) > 0) {
            $alerts[] = '<strong>' . $stats['pending_doc_verifications'] . ' berkas dokumen identitas</strong> menunggu verifikasi admin';
        }
        if (($stats['pending_verifications'] ?? 0) > 0) {
            $alerts[] = '<strong>' . $stats['pending_verifications'] . ' bukti transfer</strong> menunggu verifikasi pembayaran';
        }
        if (($stats['overdue_rentals'] ?? 0) > 0) {
            $alerts[] = '<strong>' . $stats['overdue_rentals'] . ' pesanan sewa terlambat</strong> perlu ditindaklanjuti';
        }
    @endphp

    @if(!empty($alerts))
        <div class="p-4 rounded-2xl bg-amber-50/90 border border-amber-200 text-xs sm:text-sm text-amber-900 leading-relaxed flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-sm">
            <div class="flex items-center gap-3">
                <span class="w-8 h-8 rounded-xl bg-amber-500/20 text-amber-700 font-bold flex items-center justify-center shrink-0">⚠️</span>
                <div>
                    {!! implode(' &middot; ', $alerts) !!}.
                </div>
            </div>
            <a href="{{ route('admin.rentals.index') }}" class="inline-flex items-center justify-center shrink-0 px-3.5 py-1.5 rounded-xl bg-amber-600 text-white font-bold text-xs hover:bg-amber-700 transition">
                Tinjau Sekarang &rarr;
            </a>
        </div>
    @endif

    <!-- Middle Grid: 7-Day Revenue Trend & Most Rented Gear -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">

        <!-- 7-Day Bar Chart (lg:col-span-8) -->
        <div class="lg:col-span-8 bg-white p-6 sm:p-7 rounded-3xl border border-slate-200 shadow-sm flex flex-col justify-between">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-lg font-bold text-slate-900 font-display">
                        Tren omset terverifikasi
                    </h2>
                    <span class="text-xs text-slate-400">
                        Rp{{ number_format($revenueTrendTotal, 0, ',', '.') }}
                    </span>
                </div>
                <span class="px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-600">
                    {{ $revenueTrendRange }}
                </span>
            </div>

            <!-- CSS Bar Chart (Dinamis dari Omset Nyata Database) -->
            <div class="h-48 flex items-end justify-between gap-2 sm:gap-4 px-2 pt-6 pb-2 border-b border-slate-100">
                @foreach($revenueTrendDays as $day)
                    <div class="flex-1 flex flex-col items-center gap-1.5 h-full justify-end group">
                        <span class="text-[10px] {{ $day['amount'] > 0 ? 'text-slate-800 font-bold' : 'text-slate-400 font-medium' }} whitespace-nowrap">
                            {{ $day['label_amount'] }}
                        </span>
                        <div class="w-full max-w-[48px] {{ $day['amount'] > 0 ? ($day['is_today'] ? 'bg-[#0b1320] hover:bg-slate-900' : 'bg-brand-600 hover:bg-brand-700') : 'bg-slate-100 hover:bg-slate-200' }} rounded-t-xl transition-all duration-300"
                             style="height: {{ max(4, $day['percentage']) }}%;"
                             title="{{ $day['date_label'] }}: Rp{{ number_format($day['amount'], 0, ',', '.') }}"></div>
                        <span class="text-[11px] font-semibold {{ $day['is_today'] ? 'text-slate-900 font-extrabold' : 'text-slate-600' }} mt-1 whitespace-nowrap">
                            {{ $day['date_label'] }}
                        </span>
                    </div>
                @endforeach
            </div>

            <p class="text-[11px] text-slate-400 mt-4">
                Omset tercatat setelah pembayaran diterima admin, bukan saat booking dibuat.
            </p>
        </div>

        <!-- Gear Paling Disewa (lg:col-span-4) -->
        <div class="lg:col-span-4 bg-white p-6 sm:p-7 rounded-3xl border border-slate-200 shadow-sm flex flex-col justify-between">
            <div>
                <h2 class="text-lg font-bold text-slate-900 font-display mb-4">
                    Gear paling disewa
                </h2>

                <div class="divide-y divide-slate-100">
                    @foreach($mostRented as $item)
                        <div class="py-3 flex items-center justify-between text-xs">
                            <span class="font-bold text-slate-800">{{ $item->name }}</span>
                            <span class="font-extrabold text-slate-900">{{ $item->total_rented }} sewa</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <p class="text-[11px] text-slate-400 pt-4 border-t border-slate-100">
                Jumlah pemakaian unit pada transaksi sewa aktif; satu pesanan dapat berisi beberapa gear.
            </p>
        </div>

    </div>

    <!-- Antrian Kerja Store (Matching Figma Table) -->
    <div class="bg-white p-6 sm:p-7 rounded-3xl border border-slate-200 shadow-sm space-y-4">
        <h2 class="text-lg font-bold text-slate-900 font-display">
            Antrian kerja store
        </h2>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs sm:text-sm">
                <thead>
                    <tr class="border-b border-slate-200 text-[11px] uppercase tracking-wider text-slate-400 font-bold">
                        <th class="pb-3">Pesanan / pelanggan</th>
                        <th class="pb-3">Pekerjaan</th>
                        <th class="pb-3">Verifikasi Dokumen</th>
                        <th class="pb-3">Jadwal / nominal</th>
                        <th class="pb-3">Status Sewa</th>
                        <th class="pb-3 text-right">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($workQueue as $queue)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="py-4">
                                <div class="font-bold text-slate-900">{{ $queue->rental_code }}</div>
                                <div class="text-xs text-slate-500 font-medium">{{ $queue->user->name }}</div>
                            </td>
                            <td class="py-4">
                                @if(optional($queue->verification)->verification_status === 'menunggu')
                                    <div class="font-bold text-amber-700 flex items-center gap-1.5">
                                        <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                                        Periksa Berkas & SPK
                                    </div>
                                    <div class="text-[11px] text-slate-400">
                                        Verifikasi KTP (≥17 th)
                                    </div>
                                @elseif($queue->status === 'waiting_verification')
                                    <span class="font-semibold text-slate-700">Verifikasi transfer</span>
                                @elseif($queue->status === 'in_use')
                                    <span class="font-semibold text-slate-700">Pengembalian {{ $queue->items->sum('quantity') }} unit</span>
                                @else
                                    <span class="font-semibold text-slate-500">Menunggu bayar</span>
                                @endif
                            </td>
                            <td class="py-4">
                                @if($queue->verification)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold border {{ $queue->verification->status_badge_class }}">
                                        {{ $queue->verification->status_label }}
                                    </span>
                                    @if($queue->verification->requires_stamp_duty)
                                        <span class="inline-block mt-0.5 text-[10px] text-brand-600 font-extrabold uppercase">Bea Meterai</span>
                                    @endif
                                @else
                                    <span class="text-slate-400 text-xs italic">Tanpa berkas</span>
                                @endif
                            </td>
                            <td class="py-4 text-slate-600">
                                <div class="font-semibold">Rp{{ number_format($queue->total_price, 0, ',', '.') }}</div>
                                <div class="text-[11px] text-slate-400">
                                    {{ $queue->status === 'in_use' ? 'Jatuh tempo ' . $queue->end_date->translatedFormat('d M') : $queue->duration_days . ' hari' }}
                                </div>
                            </td>
                            <td class="py-4">
                                @if($queue->status === 'waiting_verification')
                                    <span class="text-amber-600 font-bold">Menunggu Bayar Verif</span>
                                @elseif($queue->end_date->isPast() && $queue->status === 'in_use')
                                    <span class="text-rose-600 font-bold">Terlambat {{ (int) $queue->end_date->diffInDays(now()) }} hari</span>
                                @elseif($queue->status === 'in_use')
                                    <span class="text-blue-600 font-bold">Sedang Disewa</span>
                                @else
                                    <span class="text-slate-500 font-bold">{{ $queue->status_label }}</span>
                                @endif
                            </td>
                            <td class="py-4 text-right space-y-1">
                                @if(optional($queue->verification)->verification_status === 'menunggu')
                                    <a href="{{ route('admin.rentals.index', ['search' => $queue->rental_code]) }}" class="inline-block px-2.5 py-1 rounded-lg bg-amber-50 text-amber-800 border border-amber-200 text-xs font-bold hover:bg-amber-100 transition">
                                        Verifikasi Dokumen &rarr;
                                    </a>
                                @elseif($queue->status === 'waiting_verification')
                                    <a href="{{ route('admin.rentals.index', ['search' => $queue->rental_code]) }}" class="text-xs font-bold text-brand-600 hover:text-brand-700 underline">
                                        Periksa Bukti
                                    </a>
                                @elseif($queue->status === 'in_use')
                                    <a href="{{ route('admin.returns.index') }}" class="text-xs font-bold text-rose-600 hover:text-rose-700 underline">
                                        Proses Kembali
                                    </a>
                                @else
                                    <a href="{{ route('rentals.show', $queue->id) }}" class="text-xs font-bold text-slate-600 hover:text-slate-800 underline">
                                        Lihat Detail
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400 text-xs">
                                Tidak ada antrian pekerjaan mendesak saat ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Bottom 3 Metric Counters (Matching Figma) -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="p-5 sm:p-6 rounded-3xl bg-white border border-slate-200 shadow-sm space-y-1">
            <span class="text-xs text-slate-500 font-medium block">Siap sewa</span>
            <div class="text-2xl font-extrabold text-slate-900 font-display">
                {{ $stats['ready_stock'] }} unit
            </div>
            <span class="text-[11px] text-brand-600 font-semibold block">Tampil pada katalog</span>
        </div>

        <div class="p-5 sm:p-6 rounded-3xl bg-white border border-slate-200 shadow-sm space-y-1">
            <span class="text-xs text-slate-500 font-medium block">Pemeriksaan / perawatan</span>
            <div class="text-2xl font-extrabold text-slate-900 font-display">
                {{ $stats['maintenance_stock'] }} unit
            </div>
            <span class="text-[11px] text-slate-400 font-semibold block">Tidak ditawarkan untuk booking</span>
        </div>

        <div class="p-5 sm:p-6 rounded-3xl bg-white border border-slate-200 shadow-sm space-y-1">
            <span class="text-xs text-slate-500 font-medium block">Voucher dipakai</span>
            <div class="text-2xl font-extrabold text-slate-900 font-display">
                {{ $stats['voucher_used_count'] }} kali
            </div>
            <span class="text-[11px] text-brand-600 font-semibold block">Total potongan Rp{{ number_format($stats['voucher_total_discount'], 0, ',', '.') }}</span>
        </div>
    </div>

</div>
@endsection
