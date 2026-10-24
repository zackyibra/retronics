@extends('admin.layout')

@section('title', 'Kelola Pengembalian')

@section('content')
<div class="space-y-8">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <span class="text-xs font-bold uppercase tracking-wider text-purple-600 bg-purple-50 px-3 py-1 rounded-full">
                Pilar 4 Admin
            </span>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-display mt-2">
                Pemeriksaan Pengembalian Barang &amp; Denda
            </h1>
            <p class="text-slate-500 text-xs sm:text-sm mt-1">
                Sesuai alur admin: Periksa kondisi barang yang dikembalikan, hitung denda keterlambatan secara otomatis, tetapkan denda kerusakan jika ada, dan perbarui stok/kondisi barang.
            </p>
        </div>
    </div>

    <!-- 1. Daftar Unit Sedang Digunakan & Butuh Pemeriksaan Pengembalian -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm">
        <h2 class="text-base font-bold text-slate-900 pb-3 border-b border-slate-100 mb-6 flex items-center justify-between">
            <span>Unit Menunggu Pengembalian &amp; Pemeriksaan Fisik</span>
            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-800">
                {{ $pendingReturns->count() }} Transaksi Aktif
            </span>
        </h2>

        <div class="space-y-6">
            @forelse ($pendingReturns as $rental)
                <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200 flex flex-col lg:flex-row lg:items-start justify-between gap-6">
                    <div class="flex-grow space-y-3">
                        <div class="flex items-center gap-3">
                            <span class="font-mono text-sm font-black text-slate-900">{{ $rental->rental_code }}</span>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $rental->status_badge_class }}">
                                {{ $rental->status_label }}
                            </span>
                        </div>
                        <div class="text-xs text-slate-600">
                            Penyewa: <strong>{{ $rental->user->name }}</strong> ({{ $rental->user->phone ?? '-' }})
                        </div>
                        <div class="text-xs text-slate-500">
                            Batas Sewa: <strong>{{ $rental->end_date->translatedFormat('d F Y') }}</strong> ({{ $rental->duration_days }} Hari)
                        </div>

                        <!-- Items to inspect -->
                        <div class="pt-2">
                            <span class="text-[11px] font-bold text-slate-600 block mb-1.5">Rincian Barang yang Disewa:</span>
                            <div class="flex flex-wrap gap-2">
                                @foreach ($rental->items as $item)
                                    <span class="px-2.5 py-1 rounded-lg bg-white border border-slate-200 text-xs font-semibold text-slate-800">
                                        📷 {{ $item->product->name }} ({{ $item->quantity }}x) - Rp{{ number_format($item->subtotal, 0, ',', '.') }}
                                    </span>
                                @endforeach
                            </div>
                        </div>

                        <!-- Info Tarif Denda -->
                        <div class="p-3 bg-white rounded-xl border border-slate-200 text-[11px] text-slate-500 space-y-1">
                            <div class="font-semibold text-slate-700">Kebijakan Denda Retronics:</div>
                            <div>• Keterlambatan: <strong>Rp{{ number_format(config('rental.late_fee_per_day', 50000), 0, ',', '.') }}</strong> / hari keterlambatan per transaksi.</div>
                            <div>• Kerusakan: Ditetapkan oleh admin berdasarkan hasil pemeriksaan fisik per item barang.</div>
                        </div>
                    </div>

                    <!-- Flowchart Decision Form: Form Pemeriksaan & Penetapan Denda -->
                    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm max-w-lg w-full shrink-0">
                        <span class="text-xs font-bold text-slate-900 block mb-3 pb-2 border-b border-slate-100">
                            📋 Form Pengembalian &amp; Pemeriksaan Denda
                        </span>

                        <form method="POST" action="{{ route('admin.returns.process', $rental->id) }}" class="space-y-4">
                            @csrf

                            <!-- Tanggal Pengembalian & Info Keterlambatan -->
                            <div>
                                <label for="returned_at_{{ $rental->id }}" class="block text-[11px] font-bold text-slate-700 mb-1">
                                    Waktu Barang Dikembalikan:
                                </label>
                                <input type="datetime-local"
                                       id="returned_at_{{ $rental->id }}"
                                       name="returned_at"
                                       value="{{ now()->format('Y-m-d\TH:i') }}"
                                       class="w-full px-3 py-1.5 rounded-lg bg-slate-50 border border-slate-200 text-xs font-medium focus:outline-none focus:ring-1 focus:ring-purple-500">

                                @php
                                    $ratePerDay = (float) config('rental.late_fee_per_day', 50000);
                                    $nowStart = now()->startOfDay();
                                    $endStart = $rental->end_date->copy()->startOfDay();
                                    $daysLatePreview = $nowStart->greaterThan($endStart) ? (int) $endStart->diffInDays($nowStart) : 0;
                                @endphp

                                @if ($daysLatePreview > 0)
                                    <div class="mt-2 p-2.5 rounded-lg bg-amber-50 border border-amber-200 text-amber-800 text-[11px] space-y-0.5">
                                        <div class="font-bold flex items-center gap-1">
                                            <span>⚠️ Terlambat {{ $daysLatePreview }} Hari</span>
                                            <span>(Batas sewa: {{ $rental->end_date->translatedFormat('d M Y') }})</span>
                                        </div>
                                        <div>
                                            Estimasi denda keterlambatan: <strong>Rp{{ number_format($daysLatePreview * $ratePerDay, 0, ',', '.') }}</strong>
                                            <span class="text-[10px] text-amber-600">({{ $daysLatePreview }} hari × Rp{{ number_format($ratePerDay, 0, ',', '.') }}/hari)</span>
                                        </div>
                                    </div>
                                @else
                                    <p class="text-[10px] text-emerald-600 mt-1 font-medium">
                                        ✓ Pengembalian hari ini tepat waktu (Tarif keterlambatan: Rp{{ number_format($ratePerDay, 0, ',', '.') }}/hari).
                                    </p>
                                @endif
                            </div>

                            <!-- Pemeriksaan Kerusakan Barang (Per Item) -->
                            <div class="space-y-2 pt-2 border-t border-slate-100">
                                <span class="block text-[11px] font-bold text-slate-700">
                                    Pemeriksaan Fisik &amp; Denda Kerusakan per Barang:
                                </span>
                                <div class="space-y-2.5">
                                    @foreach ($rental->items as $item)
                                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 text-xs space-y-2">
                                            <div class="font-semibold text-slate-800 flex items-center justify-between">
                                                <span>📷 {{ $item->product->name }} ({{ $item->quantity }}x)</span>
                                                <span class="text-[10px] text-slate-400">ID: #{{ $item->id }}</span>
                                            </div>
                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                                <div>
                                                    <label class="block text-[10px] text-slate-500 font-medium mb-0.5">Denda Kerusakan (Rp):</label>
                                                    <input type="number"
                                                           name="damage_fines[{{ $item->id }}]"
                                                           min="0"
                                                           step="1000"
                                                           value="0"
                                                           placeholder="0 jika tidak rusak"
                                                           class="w-full px-2.5 py-1.5 rounded-lg bg-white border border-slate-200 text-xs focus:outline-none focus:ring-1 focus:ring-purple-500">
                                                </div>
                                                <div>
                                                    <label class="block text-[10px] text-slate-500 font-medium mb-0.5">Keterangan Kerusakan:</label>
                                                    <input type="text"
                                                           name="damage_reasons[{{ $item->id }}]"
                                                           placeholder="Contoh: lecet body, sensor kotor"
                                                           class="w-full px-2.5 py-1.5 rounded-lg bg-white border border-slate-200 text-xs focus:outline-none focus:ring-1 focus:ring-purple-500">
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Hasil Kelayakan Kondisi Unit -->
                            <div class="pt-2 border-t border-slate-100">
                                <label for="condition_{{ $rental->id }}" class="block text-[11px] font-bold text-slate-700 mb-1">
                                    Hasil Kelayakan Fisik Keseluruhan:
                                </label>
                                <select id="condition_{{ $rental->id }}" name="item_condition_after" required class="w-full px-3 py-2 rounded-lg bg-slate-50 border border-slate-200 text-xs font-medium focus:outline-none focus:ring-1 focus:ring-purple-500">
                                    <option value="layak_tersedia">
                                        ✓ Layak Disewakan Kembali (Stok Kembali Tersedia)
                                    </option>
                                    <option value="perawatan_rusak">
                                        ✕ Ada Kendala / Butuh Perawatan &amp; Servis
                                    </option>
                                </select>
                            </div>

                            <!-- Catatan Admin -->
                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 mb-1">
                                    Catatan Pemeriksaan Admin:
                                </label>
                                <input type="text"
                                       name="admin_notes"
                                       placeholder="Catatan fisik unit (contoh: sensor bersih, baterai lengkap)"
                                       class="w-full px-3 py-1.5 rounded-lg bg-slate-50 border border-slate-200 text-xs focus:outline-none focus:ring-1 focus:ring-purple-500">
                            </div>

                            <button type="submit" class="w-full py-2.5 px-3 rounded-lg bg-purple-600 hover:bg-purple-700 text-white font-bold text-xs transition shadow-sm">
                                Simpan Denda &amp; Tutup Transaksi
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="py-8 text-center text-slate-400 italic">
                    Tidak ada unit kamera/gear yang sedang dalam status pemakaian atau menunggu pengembalian saat ini.
                </div>
            @endforelse
        </div>
    </div>

    <!-- 2. Riwayat Transaksi yang Telah Ditutup & Selesai -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm">
        <h2 class="text-base font-bold text-slate-900 pb-3 border-b border-slate-100 mb-6">
            Riwayat Pengembalian &amp; Transaksi Ditutup
        </h2>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b border-slate-200 text-slate-400 uppercase font-bold tracking-wider">
                        <th class="py-3 px-3">Kode Sewa</th>
                        <th class="py-3 px-3">Penyewa</th>
                        <th class="py-3 px-3">Barang</th>
                        <th class="py-3 px-3">Kondisi Pengembalian</th>
                        <th class="py-3 px-3">Rincian Denda</th>
                        <th class="py-3 px-3">Catatan Pemeriksaan</th>
                        <th class="py-3 px-3 text-right">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($completedHistory as $item)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-3.5 px-3 font-mono font-bold text-slate-900">
                                <div>{{ $item->rental_code }}</div>
                                @if ($item->returned_at)
                                    <div class="text-[10px] text-slate-400 font-normal mt-0.5">
                                        Kembali: {{ $item->returned_at->translatedFormat('d M Y, H:i') }}
                                    </div>
                                @endif
                            </td>
                            <td class="py-3.5 px-3 font-semibold text-slate-800">{{ $item->user->name }}</td>
                            <td class="py-3.5 px-3 text-slate-600">{{ $item->items->pluck('product.name')->join(', ') }}</td>
                            <td class="py-3.5 px-3">
                                @if ($item->item_condition_after === 'layak_tersedia')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700">
                                        ✓ Layak (Tersedia)
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-800">
                                        Perawatan / Rusak
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-3">
                                @if ($item->fines->isNotEmpty())
                                    <div class="space-y-1">
                                        <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-50 text-rose-700 border border-rose-200">
                                            Total: Rp{{ number_format($item->total_fines, 0, ',', '.') }}
                                        </span>
                                        <div class="text-[10px] text-slate-600 space-y-0.5">
                                            @if ($item->late_fine)
                                                <div class="text-amber-700 font-medium">
                                                    ⏱️ Terlambat {{ $item->late_fine->days_late }} hr: Rp{{ number_format($item->late_fine->amount, 0, ',', '.') }}
                                                </div>
                                            @endif
                                            @foreach ($item->damage_fines as $df)
                                                <div class="text-rose-600">
                                                    🛠️ {{ $df->rentalItem?->product?->name ?? 'Barang' }}: Rp{{ number_format($df->amount, 0, ',', '.') }}
                                                    @if ($df->reason)
                                                        <span class="text-slate-400 italic">({{ $df->reason }})</span>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @else
                                    <span class="text-[11px] text-slate-400 font-medium">Rp0 (Tidak ada)</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-3 text-slate-500 italic">{{ $item->admin_notes ?? '-' }}</td>
                            <td class="py-3.5 px-3 text-right">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700">
                                    Transaksi Selesai
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-6 text-center text-slate-400 italic">
                                Belum ada riwayat pengembalian yang tercatat selesai.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
