@extends('admin.layout')

@section('title', 'Kelola Pembayaran')

@section('content')
<div class="space-y-6"
     x-data="{
        selectedProof: null,
        verifyModalOpen: false,
        rejectModalOpen: false,
        deleteProofModalOpen: false,
        replaceProofModalOpen: false,
        activePayment: null,
        deleteProofPayment: null,
        replaceProofPayment: null,
        deleteProofReason: '',
        rejectionReason: '',
        openVerify(payment) {
            this.activePayment = payment;
            this.verifyModalOpen = true;
        },
        openReject(payment) {
            this.activePayment = payment;
            this.rejectionReason = '';
            this.rejectModalOpen = true;
        },
        confirmDeleteProof(id, code, filename) {
            this.deleteProofPayment = { id: id, code: code, filename: filename };
            this.deleteProofReason = '';
            this.deleteProofModalOpen = true;
        },
        openReplaceProof(payment) {
            this.replaceProofPayment = payment;
            this.replaceProofModalOpen = true;
        }
     }">

    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-xs text-slate-500">
        <a href="{{ route('admin.dashboard') }}" class="hover:text-brand-600 transition">Admin</a>
        <span class="text-slate-300">›</span>
        <span class="text-slate-800 font-semibold">Kelola Pembayaran</span>
    </nav>

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 font-display tracking-tight">
                Kelola Pembayaran
            </h1>
            <p class="text-slate-500 text-xs sm:text-sm mt-1">
                Catatan terpisah untuk setiap transaksi pembayaran, bukti transfer, dan verifikasi admin.
            </p>
        </div>
        <div class="flex items-center gap-2 self-start sm:self-auto">
            <a href="{{ route('admin.rentals.index') }}"
               class="px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs transition shadow-sm">
                Lihat Penyewaan
            </a>
        </div>
    </div>

    <!-- Stat Cards Overview -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Pembayaran Berhasil -->
        <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-sm space-y-1">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Pembayaran Lunas</span>
            <div class="text-2xl font-black text-slate-900 font-display">
                Rp{{ number_format($totalVerifiedAmount, 0, ',', '.') }}
            </div>
            <div class="text-[11px] text-emerald-600 font-semibold">
                {{ $successCount }} transaksi terverifikasi
            </div>
        </div>

        <!-- Menunggu Verifikasi -->
        <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-sm space-y-1">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Butuh Verifikasi</span>
            <div class="text-2xl font-black text-blue-600 font-display">
                {{ $pendingCount }}
            </div>
            <div class="text-[11px] text-slate-500">
                Menunggu konfirmasi admin
            </div>
        </div>

        <!-- Pembayaran Berhasil -->
        <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-sm space-y-1">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Pembayaran Berhasil</span>
            <div class="text-2xl font-black text-emerald-600 font-display">
                {{ $successCount }}
            </div>
            <div class="text-[11px] text-slate-500">
                Telah disetujui & diverifikasi
            </div>
        </div>

        <!-- Pembayaran Ditolak -->
        <div class="p-5 rounded-2xl bg-white border border-slate-200 shadow-sm space-y-1">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Pembayaran Ditolak</span>
            <div class="text-2xl font-black text-rose-600 font-display">
                {{ $rejectedCount }}
            </div>
            <div class="text-[11px] text-slate-500">
                Riwayat pembayaran tidak valid
            </div>
        </div>
    </div>

    <!-- Filters Toolbar -->
    <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <!-- Search -->
        <form method="GET" action="{{ route('admin.payments.index') }}" class="flex-1 max-w-md">
            <div class="relative">
                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Cari kode pembayaran, kode sewa, atau nama..."
                       class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-slate-200 text-xs font-medium text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 text-xs">
                    ⌕
                </div>
            </div>
        </form>

        <!-- Status Filter Tabs -->
        <div class="flex items-center gap-2 overflow-x-auto text-xs font-bold">
            <a href="{{ route('admin.payments.index', array_filter(['search' => request('search')])) }}"
               class="px-3.5 py-2 rounded-xl transition whitespace-nowrap {{ empty(request('status')) ? 'bg-brand-600 text-white shadow-sm' : 'bg-slate-50 border border-slate-200 text-slate-600 hover:bg-slate-100' }}">
                Semua ({{ $payments->total() }})
            </a>
            <a href="{{ route('admin.payments.index', array_filter(['status' => 'waiting_verification', 'search' => request('search')])) }}"
               class="px-3.5 py-2 rounded-xl transition whitespace-nowrap {{ request('status') === 'waiting_verification' ? 'bg-brand-600 text-white shadow-sm' : 'bg-slate-50 border border-slate-200 text-slate-600 hover:bg-slate-100' }}">
                Menunggu Verifikasi ({{ $pendingCount }})
            </a>
            <a href="{{ route('admin.payments.index', array_filter(['status' => 'success', 'search' => request('search')])) }}"
               class="px-3.5 py-2 rounded-xl transition whitespace-nowrap {{ request('status') === 'success' ? 'bg-brand-600 text-white shadow-sm' : 'bg-slate-50 border border-slate-200 text-slate-600 hover:bg-slate-100' }}">
                Berhasil ({{ $successCount }})
            </a>
            <a href="{{ route('admin.payments.index', array_filter(['status' => 'rejected', 'search' => request('search')])) }}"
               class="px-3.5 py-2 rounded-xl transition whitespace-nowrap {{ request('status') === 'rejected' ? 'bg-brand-600 text-white shadow-sm' : 'bg-slate-50 border border-slate-200 text-slate-600 hover:bg-slate-100' }}">
                Ditolak ({{ $rejectedCount }})
            </a>
        </div>
    </div>

    <!-- Payments Table -->
    <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm overflow-x-auto">
        <table class="w-full text-left text-xs sm:text-sm">
            <thead>
                <tr class="border-b border-slate-200 text-[11px] uppercase tracking-wider text-slate-400 font-bold">
                    <th class="pb-3">Kode Pembayaran</th>
                    <th class="pb-3">Nomor Sewa</th>
                    <th class="pb-3">Pelanggan</th>
                    <th class="pb-3">Metode & Nominal</th>
                    <th class="pb-3">Bukti</th>
                    <th class="pb-3">Waktu Bayar</th>
                    <th class="pb-3">Status</th>
                    <th class="pb-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($payments as $payment)
                    <tr class="hover:bg-slate-50/70 transition">
                        <!-- Kode Pembayaran -->
                        <td class="py-4">
                            <span class="font-mono font-bold text-slate-900 block">
                                {{ $payment->payment_code }}
                            </span>
                            <span class="text-[11px] text-slate-400">ID: #{{ $payment->id }}</span>
                        </td>

                        <!-- Kode Sewa -->
                        <td class="py-4">
                            @if($payment->rental)
                                <a href="{{ route('rentals.show', $payment->rental_id) }}"
                                   target="_blank"
                                   class="font-mono font-bold text-brand-600 hover:underline">
                                    {{ $payment->rental->rental_code }}
                                </a>
                                <span class="text-[11px] text-slate-500 block">
                                    {{ $payment->rental->items->sum('quantity') }} unit · {{ $payment->rental->duration_days }} hari
                                </span>
                            @else
                                <span class="text-slate-400">#{{ $payment->rental_id }}</span>
                            @endif
                        </td>

                        <!-- Pelanggan -->
                        <td class="py-4">
                            @if($payment->rental && $payment->rental->user)
                                <span class="font-bold text-slate-900 block">
                                    {{ $payment->rental->user->name }}
                                </span>
                                <span class="text-[11px] text-slate-500">
                                    {{ $payment->rental->user->phone ?? $payment->rental->user->email }}
                                </span>
                            @else
                                <span class="text-slate-400">-</span>
                            @endif
                        </td>

                        <!-- Nominal & Metode -->
                        <td class="py-4">
                            <span class="font-extrabold text-slate-900 block">
                                Rp{{ number_format($payment->amount, 0, ',', '.') }}
                            </span>
                            <span class="text-[11px] text-slate-500 font-medium">
                                {{ $payment->payment_method_label }}
                            </span>
                        </td>

                        <!-- Bukti Transfer -->
                        <td class="py-4">
                            @if($payment->payment_proof)
                                <div class="flex items-center gap-2">
                                    <button type="button"
                                            @click="selectedProof = '{{ asset('storage/' . $payment->payment_proof) }}'"
                                            title="Lihat Pratinjau Berkas"
                                            class="group relative inline-block rounded-xl overflow-hidden border border-slate-200 bg-slate-100 hover:ring-2 hover:ring-brand-500 transition shrink-0">
                                        <img src="{{ asset('storage/' . $payment->payment_proof) }}"
                                             alt="Bukti {{ $payment->payment_code }}"
                                             class="w-11 h-11 object-cover">
                                        <span class="absolute inset-0 bg-black/30 group-hover:bg-black/10 transition flex items-center justify-center text-white text-[10px] font-bold">
                                            ⌕
                                        </span>
                                    </button>

                                    <div class="flex flex-col gap-1">
                                        <div class="flex items-center gap-1">
                                            <a href="{{ route('admin.payments.download_proof', $payment->id) }}"
                                               title="Unduh Berkas Bukti"
                                               class="p-1 rounded-lg text-slate-500 hover:text-brand-600 hover:bg-slate-100 transition inline-flex items-center"
                                               download>
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                            </a>

                                            <button type="button"
                                                    @click="openReplaceProof({ id: '{{ $payment->id }}', code: '{{ $payment->payment_code }}' })"
                                                    title="Ganti Berkas Bukti"
                                                    class="p-1 rounded-lg text-slate-500 hover:text-amber-600 hover:bg-amber-50 transition inline-flex items-center">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                            </button>

                                            <button type="button"
                                                    @click="confirmDeleteProof('{{ $payment->id }}', '{{ $payment->payment_code }}', '{{ basename($payment->payment_proof) }}')"
                                                    title="Hapus Berkas Bukti"
                                                    class="p-1 rounded-lg text-rose-500 hover:text-rose-700 hover:bg-rose-50 transition inline-flex items-center">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-slate-100 text-slate-500">
                                    Tanpa berkas
                                </span>
                            @endif
                        </td>

                        <!-- Waktu Bayar -->
                        <td class="py-4 text-xs text-slate-600">
                            <div>{{ $payment->payment_date ? $payment->payment_date->translatedFormat('d M Y') : $payment->created_at->translatedFormat('d M Y') }}</div>
                            <div class="text-[11px] text-slate-400">{{ $payment->payment_date ? $payment->payment_date->format('H.i') : $payment->created_at->format('H.i') }} WIB</div>
                        </td>

                        <!-- Status & Verifikasi -->
                        <td class="py-4">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold border {{ $payment->status_badge_class }}">
                                {{ $payment->status_label }}
                            </span>
                            @if($payment->verifiedBy)
                                <div class="text-[10px] text-slate-400 mt-1">
                                    Verifikator: {{ $payment->verifiedBy->name }}
                                </div>
                            @endif
                            @if($payment->notes)
                                <div class="text-[10px] text-slate-500 italic mt-0.5 max-w-[200px] truncate" title="{{ $payment->notes }}">
                                    "{{ $payment->notes }}"
                                </div>
                            @endif
                        </td>

                        <!-- Aksi -->
                        <td class="py-4 text-right">
                            @if($payment->status === \App\Models\Payment::STATUS_WAITING_VERIFICATION)
                                <div class="flex items-center justify-end gap-1.5">
                                    <form method="POST" action="{{ route('admin.payments.verify', $payment->id) }}">
                                        @csrf
                                        <input type="hidden" name="action" value="approve">
                                        @if($payment->rental?->verification?->verification_status === \App\Models\RentalVerification::STATUS_MENUNGGU)
                                            <input type="hidden" name="approve_document" value="1">
                                            <button type="submit"
                                                    onclick="return confirm('Apakah Anda yakin ingin menyetujui pembayaran dan dokumen KTP untuk {{ $payment->payment_code }}?')"
                                                    class="px-2.5 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-sm transition"
                                                    title="Setujui dokumen & pembayaran">
                                                Setujui ✓
                                            </button>
                                        @else
                                            <button type="submit"
                                                    onclick="return confirm('Apakah Anda yakin ingin menyetujui pembayaran {{ $payment->payment_code }}?')"
                                                    class="px-2.5 py-1.5 rounded-lg bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs shadow-sm transition">
                                                Setujui ✓
                                            </button>
                                        @endif
                                    </form>

                                    <button type="button"
                                            @click="openReject({ id: {{ $payment->id }}, code: '{{ $payment->payment_code }}' })"
                                            class="px-2.5 py-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 font-bold text-xs transition">
                                        Tolak ✕
                                    </button>
                                </div>
                            @else
                                <span class="text-xs text-slate-400 font-semibold">Tuntas</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="py-12 text-center text-slate-400 text-xs">
                            Tidak ada catatan pembayaran ditemukan dengan kriteria filter saat ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-4 pt-3 border-t border-slate-100">
            {{ $payments->links() }}
        </div>
    </div>

    <!-- Modal Lightbox Bukti Transfer -->
    <div x-show="selectedProof"
         class="fixed inset-0 z-50 bg-black/80 backdrop-blur-xs flex items-center justify-center p-4"
         @click="selectedProof = null"
         style="display: none;">
        <div class="bg-white rounded-3xl p-4 max-w-xl w-full max-h-[90vh] overflow-hidden flex flex-col" @click.stop>
            <div class="flex items-center justify-between pb-3 border-b border-slate-200">
                <span class="font-bold text-sm text-slate-900 font-display">Pratinjau Bukti Pembayaran</span>
                <button type="button" @click="selectedProof = null" class="text-slate-400 hover:text-slate-700 text-lg font-bold">✕</button>
            </div>
            <div class="flex-1 overflow-auto p-2 bg-slate-900 rounded-2xl my-3 flex items-center justify-center">
                <img :src="selectedProof" alt="Bukti Transfer" class="max-h-[70vh] object-contain rounded-lg">
            </div>
            <div class="flex items-center justify-end gap-2 pt-2">
                <a :href="selectedProof" target="_blank" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition">
                    Buka Tab Baru ↗
                </a>
                <button type="button" @click="selectedProof = null" class="px-4 py-2 rounded-xl bg-brand-600 hover:bg-brand-700 text-white text-xs font-bold transition">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <!-- Modal Tolak Pembayaran -->
    <div x-show="rejectModalOpen"
         class="fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-4"
         @click="rejectModalOpen = false"
         style="display: none;">
        <div class="bg-white rounded-3xl p-6 sm:p-7 max-w-md w-full space-y-4 shadow-xl border border-slate-200" @click.stop>
            <div>
                <h3 class="text-lg font-extrabold text-slate-900 font-display">
                    Tolak Pembayaran
                </h3>
                <p class="text-xs text-slate-500 mt-1">
                    Tuliskan alasan penolakan untuk kode pembayaran <strong x-text="activePayment ? activePayment.code : ''"></strong>.
                </p>
            </div>

            <form method="POST" :action="activePayment ? '{{ url('admin/pembayaran') }}/' + activePayment.id + '/verifikasi' : '#'">
                @csrf
                <input type="hidden" name="action" value="reject">

                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">
                        Alasan Penolakan (wajib)
                    </label>
                    <textarea name="rejection_reason"
                              x-model="rejectionReason"
                              rows="3"
                              required
                              placeholder="Contoh: Bukti mutasi tidak terbaca atau nominal transfer kurang dari tagihan..."
                              class="w-full px-4 py-3 rounded-xl border border-slate-200 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-rose-500"></textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-4">
                    <button type="button"
                            @click="rejectModalOpen = false"
                            class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-bold text-xs hover:bg-slate-50 transition">
                        Batal
                    </button>
                    <button type="submit"
                            :disabled="!rejectionReason.trim()"
                            class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shadow-md shadow-rose-600/20 transition disabled:opacity-50">
                        Tolak Pembayaran Ini
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Konfirmasi Hapus Bukti Pembayaran -->
    <div x-show="deleteProofModalOpen"
         class="fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-4"
         @click="deleteProofModalOpen = false"
         style="display: none;">
        <div class="bg-white rounded-3xl p-6 sm:p-7 max-w-md w-full space-y-4 shadow-xl border border-slate-200" @click.stop>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-rose-50 border border-rose-200 text-rose-600 flex items-center justify-center font-bold text-lg shrink-0">
                    🗑️
                </div>
                <div>
                    <h3 class="text-base font-extrabold text-slate-900 font-display">
                        Hapus Berkas Bukti Transfer?
                    </h3>
                    <p class="text-xs text-slate-500">
                        Penghapusan hanya berlaku untuk file bukti, data pembayaran tetap aman.
                    </p>
                </div>
            </div>

            <div class="bg-slate-50 rounded-2xl p-3.5 border border-slate-200 text-xs space-y-1.5">
                <div class="flex justify-between">
                    <span class="text-slate-500">Kode Pembayaran:</span>
                    <span class="font-mono font-bold text-slate-800" x-text="deleteProofPayment ? deleteProofPayment.code : ''"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Nama Berkas:</span>
                    <span class="font-mono text-slate-700 truncate max-w-[200px]" x-text="deleteProofPayment ? deleteProofPayment.filename : ''"></span>
                </div>
                <div class="pt-1 text-[11px] text-amber-700 font-medium">
                    ℹ️ Nominal transaksi, metode, dan status pembayaran tidak akan terhapus.
                </div>
            </div>

            <form method="POST" :action="deleteProofPayment ? '{{ url('admin/pembayaran') }}/' + deleteProofPayment.id + '/bukti' : '#'">
                @csrf
                @method('DELETE')

                <div>
                    <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1">
                        Alasan Penghapusan Berkas (Audit Log)
                    </label>
                    <input type="text"
                           name="reason"
                           x-model="deleteProofReason"
                           placeholder="Contoh: Bukti buram, pelanggan revisi bukti baru..."
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500">
                </div>

                <div class="flex items-center justify-end gap-2 pt-4">
                    <button type="button"
                            @click="deleteProofModalOpen = false"
                            class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-bold text-xs hover:bg-slate-50 transition">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shadow-md shadow-rose-600/20 transition">
                        Ya, Hapus Berkas Ini
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Ganti Bukti Pembayaran -->
    <div x-show="replaceProofModalOpen"
         class="fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-4"
         @click="replaceProofModalOpen = false"
         style="display: none;">
        <div class="bg-white rounded-3xl p-6 sm:p-7 max-w-md w-full space-y-4 shadow-xl border border-slate-200" @click.stop>
            <div>
                <h3 class="text-base font-extrabold text-slate-900 font-display">
                    Ganti Berkas Bukti Transfer
                </h3>
                <p class="text-xs text-slate-500 mt-1">
                    Unggah bukti pembayaran pengganti untuk transaksi <strong x-text="replaceProofPayment ? replaceProofPayment.code : ''"></strong>.
                </p>
            </div>

            <form method="POST"
                  :action="replaceProofPayment ? '{{ url('admin/pembayaran') }}/' + replaceProofPayment.id + '/bukti' : '#'"
                  enctype="multipart/form-data">
                @csrf

                <div>
                    <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1">
                        Pilih Gambar Pengganti (JPEG, PNG, JPG, WEBP, Maks. 3MB)
                    </label>
                    <input type="file"
                           name="payment_proof"
                           required
                           accept="image/*"
                           class="w-full text-xs text-slate-600 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100 border border-slate-200 rounded-xl p-2">
                </div>

                <div class="flex items-center justify-end gap-2 pt-4">
                    <button type="button"
                            @click="replaceProofModalOpen = false"
                            class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-bold text-xs hover:bg-slate-50 transition">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-5 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs shadow-md shadow-brand-600/20 transition">
                        Unggah & Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
