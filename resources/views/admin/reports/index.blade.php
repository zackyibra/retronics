@extends('admin.layout')

@section('title', 'Laporan Penyewaan')

@section('content')
<div class="space-y-6">

    <!-- Header & Print Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 font-display">
                Laporan &amp; Riwayat Penyewaan
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Rekapitulasi transaksi sewa, laporan pendapatan riil, dan status operasional inventaris.
            </p>
        </div>

        <button type="button"
                onclick="window.print();"
                class="px-4 py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition flex items-center gap-2 self-start sm:self-auto print:hidden">
            <span>🖨️ Cetak / Unduh PDF</span>
        </button>
    </div>

    <!-- Summary Stat Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 print:grid-cols-4">
        <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-sm">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Total Pendapatan</span>
            <span class="text-xl sm:text-2xl font-black text-brand-600 font-display mt-1 block">
                Rp{{ number_format($totalRevenue, 0, ',', '.') }}
            </span>
            <span class="text-[10px] text-slate-400">Dari transaksi dikonfirmasi / selesai</span>
        </div>

        <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-sm">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Total Pesanan</span>
            <span class="text-xl sm:text-2xl font-black text-slate-900 font-display mt-1 block">
                {{ $totalOrders }} Transaksi
            </span>
            <span class="text-[10px] text-slate-400">Semua pesanan masuk</span>
        </div>

        <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-sm">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Selesai Dikembalikan</span>
            <span class="text-xl sm:text-2xl font-black text-emerald-600 font-display mt-1 block">
                {{ $completedCount }} Unit
            </span>
            <span class="text-[10px] text-emerald-600 font-semibold">Transaksi tuntas</span>
        </div>

        <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-sm">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Aktif / Berjalan</span>
            <span class="text-xl sm:text-2xl font-black text-amber-500 font-display mt-1 block">
                {{ $activeCount }} Transaksi
            </span>
            <span class="text-[10px] text-slate-400">Sedang disewa pelanggan</span>
        </div>
    </div>

    <!-- Filter Toolbar -->
    <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-sm print:hidden">
        <form method="GET" action="{{ route('admin.reports.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end text-xs">
            <div class="sm:col-span-4">
                <label class="block font-bold text-slate-700 uppercase mb-1">Mulai Tanggal</label>
                <input type="date"
                       name="start_date"
                       value="{{ $startDate }}"
                       class="w-full px-3 py-2 rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none focus:ring-1 focus:ring-brand-500">
            </div>

            <div class="sm:col-span-4">
                <label class="block font-bold text-slate-700 uppercase mb-1">Sampai Tanggal</label>
                <input type="date"
                       name="end_date"
                       value="{{ $endDate }}"
                       class="w-full px-3 py-2 rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none focus:ring-1 focus:ring-brand-500">
            </div>

            <div class="sm:col-span-3">
                <label class="block font-bold text-slate-700 uppercase mb-1">Status Transaksi</label>
                <select name="status" class="w-full px-3 py-2 rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none focus:ring-1 focus:ring-brand-500">
                    <option value="">Semua Status</option>
                    <option value="pending_payment" {{ $status === 'pending_payment' ? 'selected' : '' }}>Menunggu Pembayaran</option>
                    <option value="waiting_verification" {{ $status === 'waiting_verification' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                    <option value="confirmed" {{ $status === 'confirmed' ? 'selected' : '' }}>Dikonfirmasi</option>
                    <option value="in_use" {{ $status === 'in_use' ? 'selected' : '' }}>Sedang Disewa</option>
                    <option value="completed" {{ $status === 'completed' ? 'selected' : '' }}>Selesai</option>
                    <option value="cancelled" {{ $status === 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
            </div>

            <div class="sm:col-span-1">
                <button type="submit" class="w-full py-2 px-3 bg-brand-600 hover:bg-brand-700 text-white rounded-xl font-bold transition">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase font-bold tracking-wider">
                        <th class="py-3.5 px-4">No. Pesanan</th>
                        <th class="py-3.5 px-4">Penyewa</th>
                        <th class="py-3.5 px-4">Barang Sewa</th>
                        <th class="py-3.5 px-4">Jadwal Sewa</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4 text-right">Total Biaya</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($rentals as $rental)
                        <tr class="hover:bg-slate-50/60 transition">
                            <td class="py-4 px-4 font-mono font-bold text-brand-700">
                                {{ $rental->rental_code }}
                            </td>

                            <td class="py-4 px-4">
                                <span class="font-bold text-slate-900 block">{{ $rental->user->name ?? 'Guest' }}</span>
                                <span class="text-[11px] text-slate-400">{{ $rental->user->phone ?? '-' }}</span>
                            </td>

                            <td class="py-4 px-4 text-slate-700">
                                @foreach ($rental->items as $item)
                                    <span class="inline-block bg-slate-100 px-2 py-0.5 rounded text-[11px] mr-1 mb-1">
                                        {{ $item->product->name ?? 'Produk' }} ({{ $item->quantity }}x)
                                    </span>
                                @endforeach
                            </td>

                            <td class="py-4 px-4 text-slate-600">
                                <span class="block font-medium">{{ \Carbon\Carbon::parse($rental->start_date)->format('d M') }} - {{ \Carbon\Carbon::parse($rental->end_date)->format('d M Y') }}</span>
                                <span class="text-[11px] text-slate-400">Durasi: {{ $rental->duration_days }} Hari</span>
                            </td>

                            <td class="py-4 px-4">
                                <span class="inline-block px-2.5 py-1 rounded-full text-[10px] font-bold {{ $rental->status_badge_class }}">
                                    {{ $rental->status_label }}
                                </span>
                            </td>

                            <td class="py-4 px-4 text-right font-display font-bold text-slate-900 text-sm">
                                Rp{{ number_format($rental->total_price, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400 italic">
                                Tidak ada data transaksi yang cocok dengan periode laporan ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($rentals->hasPages())
            <div class="p-4 border-t border-slate-100 print:hidden">
                {{ $rentals->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
