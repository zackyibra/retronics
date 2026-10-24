@extends('admin.layout')

@section('title', 'Kelola Penyewaan')

@section('content')
<div class="space-y-8">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <span class="text-xs font-bold uppercase tracking-wider text-emerald-600 bg-emerald-50 px-3 py-1 rounded-full">
                Pilar 3 Admin
            </span>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-display mt-2">
                Penyewaan & Verifikasi Transaksi
            </h1>
            <p class="text-slate-500 text-xs sm:text-sm mt-1">
                Sesuai flowchart admin: Periksa permintaan sewa (multi-barang), cek ketersediaan stok, verifikasi pembayaran manual, siapkan barang, dan serahkan unit.
            </p>
        </div>
    </div>

    <!-- Filter Status Tabs -->
    <div class="flex items-center gap-2 overflow-x-auto pb-2 text-xs font-bold">
        <a href="{{ route('admin.rentals.index') }}" class="px-3.5 py-2 rounded-xl transition whitespace-nowrap {{ empty($statusFilter) ? 'bg-slate-900 text-white' : 'bg-white text-slate-600 hover:bg-slate-100' }}">
            Semua Pesanan
        </a>
        <a href="{{ route('admin.rentals.index', ['status' => 'waiting_verification']) }}" class="px-3.5 py-2 rounded-xl transition whitespace-nowrap {{ $statusFilter === 'waiting_verification' ? 'bg-amber-500 text-slate-950 font-black' : 'bg-white text-slate-600 hover:bg-slate-100' }}">
            ⚡ Butuh Verifikasi Pembayaran
        </a>
        <a href="{{ route('admin.rentals.index', ['status' => 'confirmed']) }}" class="px-3.5 py-2 rounded-xl transition whitespace-nowrap {{ $statusFilter === 'confirmed' ? 'bg-indigo-600 text-white' : 'bg-white text-slate-600 hover:bg-slate-100' }}">
            Dikonfirmasi (Siapkan Unit)
        </a>
        <a href="{{ route('admin.rentals.index', ['status' => 'ready_pickup']) }}" class="px-3.5 py-2 rounded-xl transition whitespace-nowrap {{ $statusFilter === 'ready_pickup' ? 'bg-cyan-600 text-white' : 'bg-white text-slate-600 hover:bg-slate-100' }}">
            Siap Diambil / Diterima
        </a>
        <a href="{{ route('admin.rentals.index', ['status' => 'in_use']) }}" class="px-3.5 py-2 rounded-xl transition whitespace-nowrap {{ $statusFilter === 'in_use' ? 'bg-emerald-600 text-white' : 'bg-white text-slate-600 hover:bg-slate-100' }}">
            Sedang Digunakan
        </a>
        <a href="{{ route('admin.rentals.index', ['status' => 'completed']) }}" class="px-3.5 py-2 rounded-xl transition whitespace-nowrap {{ $statusFilter === 'completed' ? 'bg-emerald-600 text-white' : 'bg-white text-slate-600 hover:bg-slate-100' }}">
            Selesai
        </a>
    </div>

    <!-- Rentals Table -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-200 text-slate-400 uppercase font-bold tracking-wider">
                        <th class="py-3 px-3">Kode & Tanggal</th>
                        <th class="py-3 px-3">Penyewa & Kontak</th>
                        <th class="py-3 px-3">Rincian Multi-Barang</th>
                        <th class="py-3 px-3">Total & Metode</th>
                        <th class="py-3 px-3">Status Alur</th>
                        <th class="py-3 px-3 text-right">Tindakan Alur Admin</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($rentals as $rental)
                        <tr class="hover:bg-slate-50 transition items-start">
                            <!-- Kode & Tanggal -->
                            <td class="py-4 px-3 align-top">
                                <span class="font-mono font-bold text-slate-900 block">{{ $rental->rental_code }}</span>
                                <span class="text-[10px] text-slate-400 block mt-0.5">
                                    {{ $rental->created_at->translatedFormat('d M Y, H:i') }}
                                </span>
                                <span class="text-[10px] text-brand-600 font-semibold block mt-1">
                                    Sewa: {{ $rental->duration_days }} Hari ({{ $rental->start_date->translatedFormat('d M') }} - {{ $rental->end_date->translatedFormat('d M') }})
                                </span>
                            </td>

                            <!-- Penyewa & Kontak -->
                            <td class="py-4 px-3 align-top">
                                <strong class="text-slate-900 block">{{ $rental->user->name }}</strong>
                                <span class="text-[11px] text-slate-500 block">{{ $rental->user->phone ?? '-' }}</span>
                                <span class="text-[10px] text-slate-400 block truncate max-w-[150px]">{{ $rental->user->address ?? 'Alamat belum diisi' }}</span>
                            </td>

                            <!-- Multi-Barang & Cek Stok -->
                            <td class="py-4 px-3 align-top">
                                <div class="space-y-1">
                                    @foreach ($rental->items as $item)
                                        <div class="flex items-center justify-between gap-2 text-[11px]">
                                            <span class="font-medium text-slate-800">{{ $item->product->name }} ({{ $item->quantity }}x)</span>
                                            <span class="text-[10px] {{ $item->product->stock >= $item->quantity ? 'text-emerald-600 font-semibold' : 'text-rose-600 font-bold' }}">
                                                [Stok sisa: {{ $item->product->stock }}]
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </td>

                            <!-- Total & Pembayaran -->
                            <td class="py-4 px-3 align-top">
                                <span class="font-bold text-slate-900 block text-sm">
                                    Rp{{ number_format($rental->total_price, 0, ',', '.') }}
                                </span>
                                <span class="text-[10px] text-slate-500 block mt-0.5">
                                    {{ ucfirst(str_replace('_', ' ', $rental->payment_method ?? 'Belum bayar')) }}
                                </span>
                                @if ($rental->payment_proof)
                                    <a href="{{ asset('storage/' . $rental->payment_proof) }}" target="_blank" class="text-[10px] text-brand-600 underline font-semibold mt-1 block">
                                        Lihat Bukti Transfer
                                    </a>
                                @endif
                                @if ($rental->total_fines > 0)
                                    <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200 mt-1">
                                        Denda: Rp{{ number_format($rental->total_fines, 0, ',', '.') }}
                                    </span>
                                @endif
                            </td>

                            <!-- Status Badge -->
                            <td class="py-4 px-3 align-top">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold border inline-block {{ $rental->status_badge_class }}">
                                    {{ $rental->status_label }}
                                </span>
                                @if ($rental->admin_notes)
                                    <span class="text-[10px] text-slate-400 block mt-1 italic max-w-xs">
                                        "{{ $rental->admin_notes }}"
                                    </span>
                                @endif
                            </td>

                            <!-- Tindakan Alur Admin Sesuai Flowchart -->
                            <td class="py-4 px-3 align-top text-right space-y-2">

                                <!-- Tahap 1: Verifikasi Pembayaran Manual -->
                                @if ($rental->status === 'waiting_verification')
                                    <div class="flex flex-col gap-1.5 items-end">
                                        <form method="POST" action="{{ route('admin.rentals.verify', $rental->id) }}" class="inline">
                                            @csrf
                                            <input type="hidden" name="action" value="approve">
                                            <button type="submit" class="px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[11px] transition shadow-sm">
                                                ✓ Konfirmasi Pembayaran Valid
                                            </button>
                                        </form>

                                        <form method="POST" action="{{ route('admin.rentals.verify', $rental->id) }}" class="inline">
                                            @csrf
                                            <input type="hidden" name="action" value="reject">
                                            <button type="submit" class="px-3 py-1 rounded-lg bg-amber-100 hover:bg-amber-200 text-amber-800 font-bold text-[10px] transition">
                                                ✕ Minta Perbaikan Bukti
                                            </button>
                                        </form>
                                    </div>

                                <!-- Tahap 2: Siapkan Barang -->
                                @elseif ($rental->status === 'confirmed')
                                    <form method="POST" action="{{ route('admin.rentals.update_status', $rental->id) }}">
                                        @csrf
                                        <input type="hidden" name="status" value="preparing">
                                        <button type="submit" class="px-3 py-1.5 rounded-lg bg-purple-600 hover:bg-purple-700 text-white font-bold text-[11px] transition">
                                            📦 Tandai Barang Disiapkan
                                        </button>
                                    </form>

                                <!-- Tahap 3: Barang Siap Diambil -->
                                @elseif ($rental->status === 'preparing')
                                    <form method="POST" action="{{ route('admin.rentals.update_status', $rental->id) }}">
                                        @csrf
                                        <input type="hidden" name="status" value="ready_pickup">
                                        <button type="submit" class="px-3 py-1.5 rounded-lg bg-cyan-600 hover:bg-cyan-700 text-white font-bold text-[11px] transition">
                                            🛵 Siap Diambil / Kirim
                                        </button>
                                    </form>

                                <!-- Tahap 4: Serahkan Barang (Sedang Digunakan) -->
                                @elseif ($rental->status === 'ready_pickup')
                                    <form method="POST" action="{{ route('admin.rentals.update_status', $rental->id) }}">
                                        @csrf
                                        <input type="hidden" name="status" value="in_use">
                                        <button type="submit" class="px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[11px] transition">
                                            🤝 Serahkan Unit ke Penyewa
                                        </button>
                                    </form>

                                <!-- Tahap 5: Sedang Digunakan -> Menuju Pengembalian -->
                                @elseif ($rental->status === 'in_use')
                                    <a href="{{ route('admin.returns.index') }}" class="px-3 py-1.5 rounded-lg bg-slate-900 text-white font-bold text-[11px] hover:bg-slate-800 transition inline-block">
                                        🔄 Menuju Pengembalian →
                                    </a>

                                @elseif ($rental->status === 'completed')
                                    <span class="text-emerald-600 font-bold text-[11px]">Selesai Ditutup</span>

                                @else
                                    <span class="text-slate-400 text-[11px]">Menunggu respon penyewa</span>
                                @endif

                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-10 text-center text-slate-400 italic">
                                Tidak ada data transaksi penyewaan untuk filter ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $rentals->links() }}
        </div>
    </div>

</div>
@endsection
