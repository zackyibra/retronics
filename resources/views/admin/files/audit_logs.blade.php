@extends('admin.layout')

@section('title', 'Audit Log Pengelolaan Berkas')
@section('page-title', 'Audit Log Berkas')
@section('page-subtitle', 'Pencatatan dan transparansi aktivitas penghapusan berkas operasional store oleh administrator.')

@section('content')
<div class="space-y-6">

    <!-- Header & Filter -->
    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
        <form method="GET" action="{{ route('admin.files.audit_logs') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="search" class="block text-xs font-semibold text-slate-600 mb-1">Cari Catatan / Dokumen</label>
                <input type="text"
                       id="search"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Cari kode transaksi, nama file, alasan..."
                       class="w-full text-xs rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 py-2.5 px-3">
            </div>

            <div>
                <label for="file_type" class="block text-xs font-semibold text-slate-600 mb-1">Jenis Berkas</label>
                <select id="file_type"
                        name="file_type"
                        class="w-full text-xs rounded-xl border-slate-200 focus:border-brand-500 focus:ring-brand-500 py-2.5 px-3">
                    <option value="">Semua Jenis Berkas</option>
                    <option value="payment_proof" {{ request('file_type') === 'payment_proof' ? 'selected' : '' }}>Bukti Pembayaran / Transfer</option>
                    <option value="id_card_file" {{ request('file_type') === 'id_card_file' ? 'selected' : '' }}>Dokumen KTP / Verifikasi Identitas</option>
                    <option value="product_image" {{ request('file_type') === 'product_image' ? 'selected' : '' }}>Foto Barang (Inventaris)</option>
                    <option value="promotion_image" {{ request('file_type') === 'promotion_image' ? 'selected' : '' }}>Banner / Gambar Promo</option>
                    <option value="fine_photo" {{ request('file_type') === 'fine_photo' ? 'selected' : '' }}>Foto Kerusakan / Lampiran Denda</option>
                    <option value="return_photo" {{ request('file_type') === 'return_photo' ? 'selected' : '' }}>Foto Kondisi Pengembalian</option>
                    <option value="qris_image" {{ request('file_type') === 'qris_image' ? 'selected' : '' }}>Arsip Gambar QRIS</option>
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit"
                        class="px-5 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs shadow-sm transition">
                    Terapkan Filter
                </button>
                @if(request()->hasAny(['search', 'file_type']))
                    <a href="{{ route('admin.files.audit_logs') }}"
                       class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold text-xs transition">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Audit Log Table -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-sm text-slate-800">Riwayat Penghapusan Berkas (Audit Log)</h3>
            <span class="text-xs text-slate-500">Total: <strong>{{ $auditLogs->total() }}</strong> catatan</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 uppercase tracking-wider font-semibold border-b border-slate-100">
                        <th class="py-3.5 px-4">Waktu Eksekusi</th>
                        <th class="py-3.5 px-4">Admin Eksekutor</th>
                        <th class="py-3.5 px-4">Jenis Berkas</th>
                        <th class="py-3.5 px-4">Data / Transaksi Terkait</th>
                        <th class="py-3.5 px-4">Nama File Asli</th>
                        <th class="py-3.5 px-4">Alasan Penghapusan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($auditLogs as $log)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <span class="font-semibold text-slate-800">{{ $log->deleted_at ? $log->deleted_at->timezone('Asia/Jakarta')->format('d M Y') : '-' }}</span>
                                <span class="block text-[11px] text-slate-400">{{ $log->deleted_at ? $log->deleted_at->timezone('Asia/Jakarta')->format('H:i:s \W\I\B') : '' }}</span>
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <span class="font-bold text-slate-900">{{ $log->admin->name ?? 'Admin #'.$log->admin_id }}</span>
                                <span class="block text-[11px] text-slate-400">{{ $log->admin->email ?? '-' }}</span>
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                @php
                                    $badgeStyles = [
                                        'payment_proof' => 'bg-blue-50 text-blue-700 border-blue-200',
                                        'id_card_file' => 'bg-rose-50 text-rose-700 border-rose-200',
                                        'product_image' => 'bg-amber-50 text-amber-700 border-amber-200',
                                        'promotion_image' => 'bg-purple-50 text-purple-700 border-purple-200',
                                        'fine_photo' => 'bg-orange-50 text-orange-700 border-orange-200',
                                        'return_photo' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        'qris_image' => 'bg-cyan-50 text-cyan-700 border-cyan-200',
                                    ];
                                    $typeLabels = [
                                        'payment_proof' => 'Bukti Transfer',
                                        'id_card_file' => 'KTP Pelanggan',
                                        'product_image' => 'Foto Barang',
                                        'promotion_image' => 'Banner Promo',
                                        'fine_photo' => 'Foto Kerusakan / Denda',
                                        'return_photo' => 'Foto Pengembalian',
                                        'qris_image' => 'Arsip QRIS',
                                    ];
                                @endphp
                                <span class="inline-block px-2.5 py-1 rounded-full text-[11px] font-semibold border {{ $badgeStyles[$log->file_type] ?? 'bg-slate-50 text-slate-700 border-slate-200' }}">
                                    {{ $typeLabels[$log->file_type] ?? $log->file_type }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 font-medium text-slate-800">
                                {{ $log->reference_code ?? '-' }}
                            </td>
                            <td class="py-3.5 px-4 font-mono text-[11px] text-slate-500 max-w-xs truncate" title="{{ $log->file_name ?? basename($log->file_path) }}">
                                {{ $log->file_name ?? basename($log->file_path) }}
                            </td>
                            <td class="py-3.5 px-4 max-w-sm">
                                <span class="text-slate-600 line-clamp-2" title="{{ $log->reason }}">
                                    {{ $log->reason ?? 'Tanpa alasan tercantum' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400">
                                <svg class="w-12 h-12 mx-auto mb-3 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <p class="font-medium text-slate-600">Belum ada riwayat berkas yang dihapus</p>
                                <p class="text-xs text-slate-400 mt-1">Seluruh aktivitas penghapusan berkas oleh administrator akan tercatat otomatis di sini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($auditLogs->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                {{ $auditLogs->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
