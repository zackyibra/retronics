@extends('admin.layout')

@section('title', 'Dashboard Overview')

@section('content')
<div class="space-y-8">

    <!-- Welcome Header -->
    <div class="bg-gradient-to-r from-brand-900 via-slate-900 to-blue-950 rounded-3xl p-6 sm:p-8 text-white shadow-xl">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <span class="inline-block px-3 py-1 rounded-full text-xs font-bold uppercase bg-amber-400/20 text-amber-300 border border-amber-400/30 mb-2">
                    👑 Panel Administrator
                </span>
                <h1 class="text-2xl sm:text-3xl font-extrabold font-display">
                    Sistem Manajemen Penyewaan Retronics
                </h1>
                <p class="text-slate-300 text-xs sm:text-sm mt-1 max-w-xl">
                    Kelola 4 pilar utama alur sewa sesuai flowchart: Kategori, Barang & Stok, Penyewaan (Multi-Barang), dan Pengembalian.
                </p>
            </div>

            <div class="flex items-center gap-2 self-start sm:self-auto">
                <a href="{{ route('admin.rentals.index', ['status' => 'waiting_verification']) }}" class="px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold text-xs shadow-md transition flex items-center gap-1.5">
                    <span>⚡ Verifikasi Pembayaran</span>
                    @if ($stats['pending_verifications'] > 0)
                        <span class="w-5 h-5 rounded-full bg-slate-950 text-white text-[10px] flex items-center justify-center font-black">
                            {{ $stats['pending_verifications'] }}
                        </span>
                    @endif
                </a>
            </div>
        </div>
    </div>

    <!-- 4 Pilar Quick Access Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- 1. Kategori -->
        <a href="{{ route('admin.categories.index') }}" class="p-5 rounded-2xl bg-white border border-slate-200 shadow-sm hover:border-brand-400 hover:shadow-md transition flex items-start gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-brand-600 flex items-center justify-center text-2xl shrink-0">
                📁
            </div>
            <div>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Pilar 1</span>
                <h3 class="text-base font-bold text-slate-900 mt-0.5">Kelola Kategori</h3>
                <span class="text-xs text-brand-600 font-bold mt-1 block">{{ $stats['total_categories'] }} Kategori Aktif →</span>
            </div>
        </a>

        <!-- 2. Barang & Stok -->
        <a href="{{ route('admin.products.index') }}" class="p-5 rounded-2xl bg-white border border-slate-200 shadow-sm hover:border-brand-400 hover:shadow-md transition flex items-start gap-4">
            <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-2xl shrink-0">
                📷
            </div>
            <div>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Pilar 2</span>
                <h3 class="text-base font-bold text-slate-900 mt-0.5">Kelola Barang</h3>
                <span class="text-xs text-indigo-600 font-bold mt-1 block">{{ $stats['total_products'] }} Model ({{ $stats['total_stock'] }} Unit) →</span>
            </div>
        </a>

        <!-- 3. Penyewaan -->
        <a href="{{ route('admin.rentals.index') }}" class="p-5 rounded-2xl bg-white border border-slate-200 shadow-sm hover:border-brand-400 hover:shadow-md transition flex items-start gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-2xl shrink-0">
                📝
            </div>
            <div>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Pilar 3</span>
                <h3 class="text-base font-bold text-slate-900 mt-0.5">Penyewaan</h3>
                <span class="text-xs text-emerald-600 font-bold mt-1 block">{{ $stats['active_rentals'] }} Sewa Sedang Berjalan →</span>
            </div>
        </a>

        <!-- 4. Pengembalian -->
        <a href="{{ route('admin.returns.index') }}" class="p-5 rounded-2xl bg-white border border-slate-200 shadow-sm hover:border-brand-400 hover:shadow-md transition flex items-start gap-4">
            <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-2xl shrink-0">
                🔄
            </div>
            <div>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Pilar 4</span>
                <h3 class="text-base font-bold text-slate-900 mt-0.5">Pengembalian</h3>
                <span class="text-xs text-purple-600 font-bold mt-1 block">{{ $stats['pending_returns'] }} Unit Butuh Pemeriksaan →</span>
            </div>
        </a>
    </div>

    <!-- Latest Rentals Management -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-6">
            <div>
                <h2 class="text-lg font-bold text-slate-900">Permintaan Sewa Terbaru</h2>
                <p class="text-xs text-slate-400">Daftar transaksi yang masuk dan membutuhkan tindakan admin.</p>
            </div>
            <a href="{{ route('admin.rentals.index') }}" class="text-xs font-bold text-brand-600 hover:underline">
                Lihat Semua Penyewaan →
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-200 text-slate-400 uppercase font-bold tracking-wider">
                        <th class="py-3 px-4">Kode Sewa</th>
                        <th class="py-3 px-4">Penyewa</th>
                        <th class="py-3 px-4">Barang & Durasi</th>
                        <th class="py-3 px-4">Total Biaya</th>
                        <th class="py-3 px-4">Status Alur</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($latestRentals as $rental)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-3.5 px-4 font-mono font-bold text-slate-900">
                                {{ $rental->rental_code }}
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="font-bold text-slate-800 block">{{ $rental->user->name }}</span>
                                <span class="text-[10px] text-slate-400">{{ $rental->user->phone ?? $rental->user->email }}</span>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="font-semibold text-slate-700 block">
                                    {{ $rental->items->pluck('product.name')->join(', ') }}
                                </span>
                                <span class="text-[10px] text-slate-400">
                                    {{ $rental->start_date->translatedFormat('d M') }} - {{ $rental->end_date->translatedFormat('d M Y') }} ({{ $rental->duration_days }} Hari)
                                </span>
                            </td>
                            <td class="py-3.5 px-4 font-bold text-slate-900">
                                Rp{{ number_format($rental->total_price, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $rental->status_badge_class }}">
                                    {{ $rental->status_label }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <a href="{{ route('rentals.show', $rental->id) }}" target="_blank" class="px-3 py-1.5 rounded-lg bg-blue-50 text-brand-700 font-bold hover:bg-blue-100 transition inline-block">
                                    Detail Alur →
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400 italic">
                                Belum ada transaksi penyewaan yang tercatat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
