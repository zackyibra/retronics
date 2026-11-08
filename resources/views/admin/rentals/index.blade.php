@extends('admin.layout')

@section('title', 'Penyewaan & verifikasi')

@section('content')
@php
    $selectedRental = $rentals->firstWhere('status', 'waiting_verification') ?? $rentals->first();
@endphp

<div class="space-y-6"
     x-data="{
        selectedId: {{ $selectedRental ? $selectedRental->id : 'null' }},
        rejectReason: '',
        rejectionActive: false,
        deleteKtpModalOpen: false,
        deleteKtpData: null,
        deleteKtpReason: '',
        openDeleteKtpModal(id, code, name, filename) {
            this.deleteKtpData = { id: id, code: code, name: name, filename: filename };
            this.deleteKtpReason = '';
            this.deleteKtpModalOpen = true;
        }
     }">

    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-xs text-slate-500">
        <span>Admin</span>
        <span class="text-slate-300">›</span>
        <span class="text-slate-800 font-semibold">Penyewaan & verifikasi</span>
    </nav>

    <!-- Header -->
    <div>
        <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 font-display tracking-tight">
            Penyewaan & verifikasi
        </h1>
        <p class="text-slate-500 text-xs sm:text-sm mt-1">
            Periksa bukti transfer sebelum mengubah pembayaran menjadi lunas.
        </p>
    </div>

    <!-- Filters Toolbar (Matching Figma) -->
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
        <!-- Cari Pesanan -->
        <div class="p-3.5 rounded-2xl border border-slate-200 bg-white shadow-sm focus-within:border-brand-500">
            <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">
                Cari pesanan
            </label>
            <form method="GET" action="{{ route('admin.rentals.index') }}">
                @if(request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif
                @if(request('verification_status'))
                    <input type="hidden" name="verification_status" value="{{ request('verification_status') }}">
                @endif
                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="⌕ Kode, NIK, nama..."
                       class="w-full bg-transparent text-xs sm:text-sm font-semibold text-slate-900 focus:outline-none">
            </form>
        </div>

        <!-- Filter Status Transaksi -->
        <div class="p-3.5 rounded-2xl border border-slate-200 bg-white shadow-sm">
            <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">
                Status Transaksi
            </label>
            <form method="GET" action="{{ route('admin.rentals.index') }}" id="status-filter-form">
                @if(request('search'))
                    <input type="hidden" name="search" value="{{ request('search') }}">
                @endif
                @if(request('verification_status'))
                    <input type="hidden" name="verification_status" value="{{ request('verification_status') }}">
                @endif
                <select name="status"
                        onchange="this.form.submit()"
                        class="w-full bg-transparent text-xs sm:text-sm font-semibold text-slate-800 focus:outline-none cursor-pointer">
                    <option value="">Semua status transaksi</option>
                    <option value="waiting_verification" {{ request('status') === 'waiting_verification' ? 'selected' : '' }}>Menunggu Verifikasi Pembayaran</option>
                    <option value="pending_payment" {{ request('status') === 'pending_payment' ? 'selected' : '' }}>Menunggu Bayar</option>
                    <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Dikonfirmasi / Disiapkan</option>
                    <option value="ready_pickup" {{ request('status') === 'ready_pickup' ? 'selected' : '' }}>Siap Diambil</option>
                    <option value="in_use" {{ request('status') === 'in_use' ? 'selected' : '' }}>Sedang Disewa</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Selesai</option>
                </select>
            </form>
        </div>

        <!-- Filter Status Verifikasi Dokumen -->
        <div class="p-3.5 rounded-2xl border border-slate-200 bg-white shadow-sm">
            <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">
                Verifikasi Dokumen
            </label>
            <form method="GET" action="{{ route('admin.rentals.index') }}" id="verif-filter-form">
                @if(request('search'))
                    <input type="hidden" name="search" value="{{ request('search') }}">
                @endif
                @if(request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif
                <select name="verification_status"
                        onchange="this.form.submit()"
                        class="w-full bg-transparent text-xs sm:text-sm font-semibold text-slate-800 focus:outline-none cursor-pointer">
                    <option value="">Semua dokumen</option>
                    <option value="menunggu" {{ request('verification_status') === 'menunggu' ? 'selected' : '' }}>🟡 Menunggu Diperiksa</option>
                    <option value="perlu_revisi" {{ request('verification_status') === 'perlu_revisi' ? 'selected' : '' }}>🟠 Perlu Revisi Dokumen</option>
                    <option value="disetujui" {{ request('verification_status') === 'disetujui' ? 'selected' : '' }}>🟢 Dokumen Sah / Disetujui</option>
                    <option value="ditolak" {{ request('verification_status') === 'ditolak' ? 'selected' : '' }}>🔴 Dokumen Ditolak</option>
                </select>
            </form>
        </div>

        <!-- Tanggal -->
        <div class="p-3.5 rounded-2xl border border-slate-200 bg-white shadow-sm">
            <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">
                Bulan Operasional
            </label>
            <div class="text-xs sm:text-sm font-semibold text-slate-800">
                {{ \Carbon\Carbon::now()->translatedFormat('F Y') }}
            </div>
        </div>
    </div>

    <!-- Filter Tabs (Matching Figma Buttons) -->
    <div class="flex items-center gap-2 overflow-x-auto pb-1 text-xs font-bold">
        <a href="{{ route('admin.rentals.index') }}"
           class="px-4 py-2 rounded-xl transition {{ empty(request('status')) ? 'bg-brand-600 text-white shadow-sm' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">
            Semua aktif ({{ $rentals->total() }})
        </a>
        <a href="{{ route('admin.rentals.index', ['status' => 'waiting_verification']) }}"
           class="px-4 py-2 rounded-xl transition {{ request('status') === 'waiting_verification' ? 'bg-brand-600 text-white shadow-sm' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">
            Butuh verifikasi ({{ \App\Models\Rental::where('status', 'waiting_verification')->count() }})
        </a>
        <a href="{{ route('admin.rentals.index', ['status' => 'pending_payment']) }}"
           class="px-4 py-2 rounded-xl transition {{ request('status') === 'pending_payment' ? 'bg-brand-600 text-white shadow-sm' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">
            Menunggu Bayar ({{ \App\Models\Rental::where('status', 'pending_payment')->count() }})
        </a>
    </div>

    <!-- Rentals Table -->
    <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm overflow-x-auto">
        <table class="w-full text-left text-xs sm:text-sm">
            <thead>
                <tr class="border-b border-slate-200 text-[11px] uppercase tracking-wider text-slate-400 font-bold">
                    <th class="pb-3">Nomor pesanan</th>
                    <th class="pb-3">Pelanggan</th>
                    <th class="pb-3">Periode</th>
                    <th class="pb-3">Total sewa</th>
                    <th class="pb-3">Status Bayar/Alur</th>
                    <th class="pb-3">Verifikasi Dokumen</th>
                    <th class="pb-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($rentals as $rental)
                    <tr class="hover:bg-slate-50/70 transition cursor-pointer"
                        :class="selectedId === {{ $rental->id }} ? 'bg-blue-50/40' : ''"
                        @click="selectedId = {{ $rental->id }}">
                        <td class="py-4 font-mono font-bold text-slate-900">
                            {{ $rental->rental_code }}
                        </td>
                        <td class="py-4 font-semibold text-slate-800">
                            {{ $rental->user->name }}
                        </td>
                        <td class="py-4 text-slate-600">
                            {{ $rental->start_date->translatedFormat('d') }}–{{ $rental->end_date->translatedFormat('d M') }} · {{ $rental->duration_days }}h
                        </td>
                        <td class="py-4 font-bold text-slate-900">
                            Rp{{ number_format($rental->total_price, 0, ',', '.') }}
                        </td>
                        <td class="py-4">
                            @if($rental->status === 'waiting_verification')
                                <span class="font-bold text-amber-600">Menunggu Verifikasi</span>
                            @elseif($rental->status === 'pending_payment')
                                <span class="font-bold text-amber-600">Menunggu Bayar</span>
                            @elseif($rental->status === 'in_use')
                                @if($rental->end_date->isPast())
                                    <span class="font-bold text-rose-600">Terlambat</span>
                                @else
                                    <span class="font-bold text-blue-600">Sedang Disewa</span>
                                @endif
                            @elseif($rental->status === 'completed')
                                <span class="font-bold text-emerald-600">Selesai</span>
                            @else
                                <span class="font-bold text-slate-700">{{ $rental->status_label }}</span>
                            @endif
                        </td>
                        <td class="py-4">
                            @if($rental->verification)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold border {{ $rental->verification->status_badge_class }}">
                                    {{ $rental->verification->status_label }}
                                </span>
                            @else
                                <span class="text-slate-400 text-xs italic">Tanpa berkas</span>
                            @endif
                        </td>
                        <td class="py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.rentals.print', $rental->id) }}"
                                   target="_blank"
                                   title="Cetak Formulir A4"
                                   class="p-1.5 rounded-lg border border-slate-200 text-slate-500 hover:text-slate-900 hover:bg-slate-100 transition">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                </a>

                                @if($rental->status === 'waiting_verification')
                                    <button type="button"
                                            @click.stop="selectedId = {{ $rental->id }}"
                                            class="font-bold text-xs text-brand-600 hover:text-brand-700">
                                        Periksa ✓
                                    </button>
                                @else
                                    <button type="button"
                                            @click.stop="selectedId = {{ $rental->id }}"
                                            class="font-semibold text-xs text-slate-600 hover:text-slate-900">
                                        Detail
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-8 text-center text-slate-400 text-xs">
                            Tidak ada pesanan ditemukan dengan kriteria filter saat ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-4 pt-3 border-t border-slate-100">
            {{ $rentals->links() }}
        </div>
    </div>

    <!-- Selected Verification Detail Pane (Matching Figma Screenshot) -->
    @if($selectedRental)
        @foreach($rentals as $rental)
            <div x-show="selectedId === {{ $rental->id }}" class="space-y-6">

                <!-- Verifikasi Dipilih Card -->
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">
                                Verifikasi dipilih · {{ $rental->rental_code }}
                            </span>
                            <div class="mt-1">
                                <span class="inline-block px-3 py-1 rounded-full text-xs font-bold border {{ $rental->status_badge_class }}">
                                    {{ $rental->status_label }}
                                </span>
                            </div>
                        </div>

                        <!-- Tombol Cetak Formulir -->
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.rentals.print', $rental->id) }}"
                               target="_blank"
                               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs transition shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                <span>Cetak Formulir</span>
                            </a>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

                        <!-- Left: Transfer Proof Box (lg:col-span-5) -->
                        <div class="lg:col-span-5 space-y-3">
                            <div class="rounded-3xl border border-slate-200 p-6 bg-slate-50 text-center space-y-4">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">
                                    BUKTI TRANSFER · {{ strtoupper(str_replace('_', ' ', $rental->payment_method ?? 'TRANSFER')) }}
                                </span>

                                @if($rental->payment_proof)
                                    <div class="aspect-[4/3] rounded-2xl overflow-hidden bg-black border border-slate-200">
                                        <img src="{{ asset('storage/' . $rental->payment_proof) }}"
                                             alt="Bukti Transfer {{ $rental->rental_code }}"
                                             class="w-full h-full object-contain">
                                    </div>
                                    <div class="text-xs text-slate-500">
                                        Diunggah: {{ $rental->updated_at->translatedFormat('d M Y, H.i') }} WIB
                                    </div>
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ asset('storage/' . $rental->payment_proof) }}"
                                           target="_blank"
                                           class="inline-block px-3 py-1.5 rounded-xl bg-white border border-slate-200 text-xs font-bold text-slate-700 hover:bg-slate-100 shadow-sm transition">
                                            Perbesar Bukti ↗
                                        </a>
                                        <a href="{{ asset('storage/' . $rental->payment_proof) }}"
                                           download="Bukti-{{ $rental->rental_code }}"
                                           class="inline-block px-3 py-1.5 rounded-xl bg-slate-100 border border-slate-200 text-xs font-bold text-slate-700 hover:bg-slate-200 shadow-sm transition">
                                            Unduh ⤓
                                        </a>
                                    </div>
                                @else
                                    <div class="py-8 space-y-2">
                                        <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center mx-auto text-xl font-bold">
                                            📄
                                        </div>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-200/70 text-slate-600">
                                            Tanpa berkas
                                        </span>
                                        <div class="text-xs font-bold text-slate-600">Pelanggan belum mengunggah berkas bukti</div>
                                        <div class="text-[11px] text-slate-400">Total tagihan: Rp{{ number_format($rental->total_price, 0, ',', '.') }}</div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Right: Details, Checkboxes & Decision Actions (lg:col-span-7) -->
                        <div class="lg:col-span-7 space-y-5">
                            <div>
                                <h3 class="text-2xl font-extrabold text-slate-900 font-display">
                                    {{ $rental->user->name }}
                                </h3>
                                <p class="text-xs text-slate-500 mt-0.5">
                                    WhatsApp: {{ $rental->user->phone ?? '-' }} · Domisili: {{ $rental->user->address ?? 'Gunung Putri, Bogor' }}
                                </p>
                            </div>

                            <div class="space-y-2 text-xs sm:text-sm">
                                <div class="flex items-center justify-between text-slate-600">
                                    <span>Total pesanan</span>
                                    <span class="font-bold text-slate-900">Rp{{ number_format($rental->total_price, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex items-center justify-between text-slate-600">
                                    <span>Nominal pada bukti</span>
                                    <span class="font-bold text-slate-900">Rp{{ number_format($rental->total_price, 0, ',', '.') }}</span>
                                </div>
                                <div class="pt-2 text-xs text-slate-500">
                                    Gear: <strong>{{ $rental->items->pluck('product.name')->join(', ') }}</strong> ({{ $rental->items->sum('quantity') }} unit · {{ $rental->duration_days }} hari)
                                </div>
                            </div>

                            <!-- Admin Verification Checkboxes -->
                            <div class="space-y-2 pt-2 border-t border-slate-100 text-xs text-slate-700 font-semibold">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" checked class="w-4 h-4 text-brand-600 rounded border-slate-300">
                                    <span>Nominal sesuai total tagihan sewa</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" checked class="w-4 h-4 text-brand-600 rounded border-slate-300">
                                    <span>Tanggal dan tujuan rekening transfer diperiksa</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" class="w-4 h-4 text-brand-600 rounded border-slate-300">
                                    <span>Mutasi rekening store telah dikonfirmasi masuk</span>
                                </label>
                            </div>

                            <!-- Info Box -->
                            <div class="p-4 rounded-2xl bg-blue-50/70 border border-blue-100 text-xs text-brand-800 leading-relaxed">
                                Pembayaran belum lunas sampai admin menerima bukti. Cocokkan bukti dengan mutasi rekening store.
                            </div>

                            <!-- Rejection Reason Input -->
                            <div>
                                <label for="rejection_reason_{{ $rental->id }}" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">
                                    Alasan penolakan (wajib jika ditolak)
                                </label>
                                <input type="text"
                                       id="rejection_reason_{{ $rental->id }}"
                                       x-model="rejectReason"
                                       placeholder="Contoh: nominal tidak sesuai atau bukti transfer buram."
                                       class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs focus:outline-none focus:ring-2 focus:ring-brand-500">
                            </div>

                            @if(!$rental->verification || $rental->verification->verification_status !== \App\Models\RentalVerification::STATUS_DISETUJUI)
                                @if($rental->verification && $rental->verification->verification_status === \App\Models\RentalVerification::STATUS_MENUNGGU)
                                    <div class="p-3.5 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 text-xs space-y-2">
                                        <div class="flex items-start gap-2">
                                            <span class="text-base leading-none">⚠️</span>
                                            <div class="leading-relaxed">
                                                <strong>Dokumen KTP masih menunggu verifikasi:</strong> Anda dapat memeriksa berkas fisik KTP di bawah terlebih dahulu, atau langsung menyetujui dokumen & bukti transfer sekaligus melalui tombol hijau di bawah.
                                            </div>
                                        </div>
                                        <div class="pl-6">
                                            <a href="#section-verifikasi-dokumen-{{ $rental->id }}" class="inline-flex items-center gap-1 font-bold text-amber-800 hover:text-amber-950 underline text-[11px]">
                                                Lihat & Periksa Berkas KTP di Bawah ↓
                                            </a>
                                        </div>
                                    </div>
                                @else
                                    <div class="p-3 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 text-xs flex items-start gap-2">
                                        <span class="text-sm">⚠️</span>
                                        <div>
                                            <strong>Dokumen belum disetujui:</strong> Pembayaran hanya dapat disetujui setelah dokumen identitas diverifikasi sah pada panel di bawah (Status: {{ $rental->verification ? $rental->verification->status_label : 'Belum Ada Berkas' }}).
                                        </div>
                                    </div>
                                @endif
                            @endif

                            <!-- Verification Action Buttons -->
                            <div class="flex items-center gap-3 pt-2">
                                <!-- Terima Pembayaran -->
                                <form method="POST" action="{{ route('admin.rentals.verify', $rental->id) }}" class="flex-1">
                                    @csrf
                                    <input type="hidden" name="action" value="approve">
                                    @if($rental->verification && $rental->verification->verification_status === \App\Models\RentalVerification::STATUS_MENUNGGU)
                                        <input type="hidden" name="approve_document" value="1">
                                        <button type="submit"
                                                class="w-full py-3 px-5 rounded-xl font-bold text-xs sm:text-sm text-white bg-emerald-600 hover:bg-emerald-700 shadow-md shadow-emerald-500/20 transition flex items-center justify-center gap-1.5">
                                            <span>✓ Setujui Dokumen & Terima Pembayaran</span>
                                        </button>
                                    @else
                                        <button type="submit"
                                                class="w-full py-3 px-5 rounded-xl font-bold text-xs sm:text-sm text-white bg-brand-600 hover:bg-brand-700 shadow-md shadow-brand-500/20 transition">
                                            Terima Pembayaran
                                        </button>
                                    @endif
                                </form>

                                <!-- Tolak Bukti -->
                                <form method="POST" action="{{ route('admin.rentals.verify', $rental->id) }}" class="flex-1">
                                    @csrf
                                    <input type="hidden" name="action" value="reject">
                                    <input type="hidden" name="rejection_reason" :value="rejectReason">
                                    <button type="submit"
                                            class="w-full py-3 px-5 rounded-xl font-bold text-xs sm:text-sm text-white bg-rose-600 hover:bg-rose-700 shadow-md shadow-rose-500/20 transition"
                                            onclick="if(!document.getElementById('rejection_reason_{{ $rental->id }}').value){ alert('Harap isi alasan penolakan terlebih dahulu.'); return false; }">
                                        Tolak Bukti
                                    </button>
                                </form>
                            </div>

                            <p class="text-[11px] text-slate-400">
                                Terima → status berubah ke <strong>Disiapkan</strong> dan stok otomatis dipotong. Tolak → status kembali ke <strong>Menunggu Bayar</strong> dengan catatan alasan.
                            </p>
                        </div>

                    </div>

                    <!-- Riwayat Catatan Pembayaran (Payments Entity) -->
                    @if($rental->payments->isNotEmpty())
                        <div class="mt-6 pt-6 border-t border-slate-200 space-y-3">
                            <div class="flex items-center justify-between">
                                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500">
                                    Riwayat Catatan Pembayaran ({{ $rental->payments->count() }})
                                </h4>
                                <a href="{{ route('admin.payments.index', ['search' => $rental->rental_code]) }}"
                                   class="text-[11px] font-bold text-brand-600 hover:underline">
                                    Lihat di Menu Pembayaran →
                                </a>
                            </div>

                            <div class="space-y-2">
                                @foreach($rental->payments->sortByDesc('id') as $p)
                                    <div class="p-3 rounded-xl border {{ $p->status === 'success' ? 'border-emerald-200 bg-emerald-50/40' : ($p->status === 'rejected' ? 'border-rose-200 bg-rose-50/40' : 'border-blue-200 bg-blue-50/40') }} flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs">
                                        <div class="space-y-0.5">
                                            <div class="flex items-center gap-2">
                                                <span class="font-mono font-bold text-slate-900">{{ $p->payment_code }}</span>
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $p->status_badge_class }}">
                                                    {{ $p->status_label }}
                                                </span>
                                            </div>
                                            <div class="text-[11px] text-slate-500">
                                                {{ $p->payment_method_label }} · {{ $p->payment_date ? $p->payment_date->translatedFormat('d M Y H.i') : $p->created_at->translatedFormat('d M Y H.i') }} WIB
                                                @if($p->verifiedBy)
                                                    · Diverifikasi oleh: <strong>{{ $p->verifiedBy->name }}</strong>
                                                @endif
                                            </div>
                                            @if($p->notes)
                                                <div class="text-[11px] text-slate-600 italic">
                                                    Catatan: "{{ $p->notes }}"
                                                </div>
                                            @endif
                                        </div>
                                        <div class="text-right shrink-0">
                                            <span class="font-extrabold text-sm text-slate-900">
                                                Rp{{ number_format($p->amount, 0, ',', '.') }}
                                            </span>
                                            @if($p->payment_proof)
                                                <div>
                                                    <a href="{{ asset('storage/' . $p->payment_proof) }}" target="_blank" class="text-[11px] font-semibold text-brand-600 hover:underline">
                                                        Lihat Bukti ↗
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Verifikasi Dokumen Identitas & Surat Perjanjian Box -->
                <div id="section-verifikasi-dokumen-{{ $rental->id }}" class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-6 scroll-mt-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-4">
                        <div>
                            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">
                                Verifikasi Persyaratan Pelanggan
                            </span>
                            <h3 class="text-xl font-extrabold text-slate-900 font-display">
                                Dokumen Identitas & Surat Perjanjian Sewa
                            </h3>
                        </div>

                        <div class="flex items-center gap-2 flex-wrap">
                            @if($rental->verification)
                                <span class="px-3 py-1 rounded-full text-xs font-bold border {{ $rental->verification->status_badge_class }}">
                                    Status: {{ $rental->verification->status_label }}
                                </span>
                                <a href="{{ route('admin.rentals.print', $rental->id) }}"
                                   target="_blank"
                                   class="px-3.5 py-1.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs transition flex items-center gap-1.5 shadow-xs">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                    <span>Cetak Formulir</span>
                                </a>
                                <a href="{{ route('rentals.agreement', $rental->id) }}"
                                   target="_blank"
                                   class="px-3.5 py-1.5 rounded-xl bg-brand-50 hover:bg-brand-100 text-brand-700 font-bold text-xs border border-brand-200 transition flex items-center gap-1 shadow-xs">
                                    <span>Buka Surat Perjanjian (SPK) ↗</span>
                                </a>
                            @else
                                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-600">
                                    Belum ada data verifikasi
                                </span>
                            @endif
                        </div>
                    </div>

                    @if($rental->verification)
                        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

                            <!-- Kolom Kiri: Berkas Identitas & Formulir (lg:col-span-7) -->
                            <div class="lg:col-span-7 space-y-5">
                                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 space-y-3">
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs font-bold text-slate-700 uppercase tracking-wider">
                                            Dokumen Identitas Wajib (KTP Asli)
                                        </span>
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-brand-800">
                                            KTP Dewasa (≥ 17 Tahun)
                                        </span>
                                    </div>

                                    <div class="pt-2 border-t border-slate-200/70 text-xs text-slate-700 space-y-2">
                                        <div class="flex justify-between">
                                            <span class="text-slate-500">Nomor NIK KTP:</span>
                                            <span class="font-mono font-bold text-slate-900">{{ $rental->verification->id_card_number ?? '-' }}</span>
                                        </div>
                                        @if($rental->verification->birth_date)
                                            <div class="flex justify-between">
                                                <span class="text-slate-500">Tanggal Lahir / Usia:</span>
                                                <span class="font-semibold text-slate-900">
                                                    {{ $rental->verification->birth_date->translatedFormat('d F Y') }}
                                                    ({{ \Carbon\Carbon::parse($rental->verification->birth_date)->age }} tahun)
                                                </span>
                                            </div>
                                        @endif
                                        <div class="flex justify-between items-center flex-wrap gap-2 pt-1">
                                            <span class="text-slate-500">Berkas Foto KTP Asli:</span>
                                            @if($rental->verification->id_card_file)
                                                <div class="flex items-center gap-1.5 flex-wrap">
                                                    <a href="{{ route('rentals.document', ['id' => $rental->id, 'type' => 'id_card']) }}"
                                                       target="_blank"
                                                       class="px-2.5 py-1 rounded-lg bg-white border border-slate-200 text-brand-600 hover:text-brand-800 font-bold text-xs inline-flex items-center gap-1 shadow-2xs">
                                                        <span>Lihat</span> ↗
                                                    </a>
                                                    <a href="{{ route('rentals.document', ['id' => $rental->id, 'type' => 'id_card', 'download' => 1]) }}"
                                                       class="px-2.5 py-1 rounded-lg bg-white border border-slate-200 text-slate-700 hover:text-brand-600 font-bold text-xs inline-flex items-center gap-1 shadow-2xs"
                                                       download>
                                                        <span>Unduh</span> ⤓
                                                    </a>

                                                    @php
                                                        $isRentalActive = in_array($rental->status, ['pending', 'waiting_verification', 'active', 'in_use']) || in_array($rental->verification->verification_status, ['menunggu', 'waiting_verification']);
                                                    @endphp

                                                    @if($isRentalActive)
                                                        <span class="px-2 py-0.5 rounded-lg bg-amber-50 border border-amber-200 text-[11px] text-amber-800 font-semibold cursor-not-allowed"
                                                              title="Dokumen KTP terlindungi selama verifikasi atau sewa aktif berlangsung.">
                                                            🔒 Dilindungi
                                                        </span>
                                                    @else
                                                        <button type="button"
                                                                @click="openDeleteKtpModal('{{ $rental->id }}', '{{ $rental->rental_code }}', '{{ $rental->verification->full_name }}', '{{ basename($rental->verification->id_card_file) }}')"
                                                                title="Hapus Dokumen KTP (Perlu Alasan)"
                                                                class="p-1 rounded-lg text-rose-500 hover:text-rose-700 hover:bg-rose-50 transition inline-flex items-center">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                        </button>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-slate-100 text-slate-500">
                                                    Tanpa berkas
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Formulir Tambahan: Tujuan & Kontak Darurat -->
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                                    <div class="p-3.5 rounded-xl border border-slate-200 bg-white">
                                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">
                                            Tujuan Penggunaan Sewa
                                        </span>
                                        <p class="font-semibold text-slate-800">
                                            {{ $rental->verification->rental_purpose ?? 'Produksi Fotografi' }}
                                        </p>
                                    </div>

                                    <div class="p-3.5 rounded-xl border border-slate-200 bg-white">
                                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">
                                            Kontak Darurat Penjamin
                                        </span>
                                        <p class="font-semibold text-slate-800">
                                            {{ $rental->verification->emergency_contact_name ?? '-' }}
                                            <span class="text-slate-500 font-normal">({{ $rental->verification->emergency_contact_relation ?? '-' }})</span>
                                        </p>
                                        <p class="text-[11px] text-slate-600 font-mono mt-0.5">
                                            {{ $rental->verification->emergency_contact_phone ?? '-' }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Kolom Kanan: Tanda Tangan, Meterai & Aksi Keputusan Admin (lg:col-span-5) -->
                            <div class="lg:col-span-5 space-y-4">

                                <!-- Tanda Tangan Digital & Status Meterai Box -->
                                <div class="p-4 rounded-2xl border border-slate-200 bg-slate-50 space-y-3">
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs font-bold text-slate-700 uppercase tracking-wider">
                                            Tanda Tangan Digital Penyewa
                                        </span>
                                        @if($rental->verification->requires_stamp_duty || $rental->total_price >= 5000000)
                                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                                Bea Meterai UU 10/2020
                                            </span>
                                        @endif
                                    </div>

                                    <div class="border border-slate-200 rounded-xl bg-white p-3 text-center min-h-[100px] flex items-center justify-center">
                                        @if($rental->verification->signature_data)
                                            <img src="{{ $rental->verification->signature_data }}" alt="Tanda Tangan Digital" class="max-h-20 max-w-full object-contain mx-auto">
                                        @else
                                            <span class="text-xs text-slate-400 italic">Belum ada tanda tangan</span>
                                        @endif
                                    </div>

                                    <div class="text-[10px] text-slate-400 flex items-center justify-between">
                                        <span>Waktu: {{ $rental->verification->agreement_signed_at?->translatedFormat('d M Y, H.i') ?? '-' }} WIB</span>
                                        <span class="font-mono">IP: {{ $rental->verification->agreement_ip_address ?? '-' }}</span>
                                    </div>
                                </div>

                                <!-- FORM AKSI KEPUTUSAN VERIFIKASI ADMIN -->
                                <div class="p-4 rounded-2xl border border-slate-200 bg-white space-y-3" x-data="{ decisionStatus: '{{ $rental->verification->verification_status }}' }">
                                    <span class="text-xs font-bold text-slate-900 block">
                                        Perbarui Status Verifikasi Dokumen:
                                    </span>

                                    <form method="POST" action="{{ route('admin.rentals.verify_document', $rental->id) }}" class="space-y-3">
                                        @csrf

                                        <!-- Opsi Status Verifikasi -->
                                        <div class="grid grid-cols-3 gap-2 text-xs font-bold">
                                            <label class="p-2 rounded-xl border cursor-pointer text-center transition"
                                                   :class="decisionStatus === 'disetujui' ? 'bg-emerald-50 border-emerald-500 text-emerald-800' : 'border-slate-200 text-slate-600'">
                                                <input type="radio" name="verification_status" value="disetujui" x-model="decisionStatus" class="sr-only">
                                                <span>✓ Setujui</span>
                                            </label>

                                            <label class="p-2 rounded-xl border cursor-pointer text-center transition"
                                                   :class="decisionStatus === 'perlu_revisi' ? 'bg-purple-50 border-purple-500 text-purple-800' : 'border-slate-200 text-slate-600'">
                                                <input type="radio" name="verification_status" value="perlu_revisi" x-model="decisionStatus" class="sr-only">
                                                <span>⚠️ Revisi</span>
                                            </label>

                                            <label class="p-2 rounded-xl border cursor-pointer text-center transition"
                                                   :class="decisionStatus === 'ditolak' ? 'bg-rose-50 border-rose-500 text-rose-800' : 'border-slate-200 text-slate-600'">
                                                <input type="radio" name="verification_status" value="ditolak" x-model="decisionStatus" class="sr-only">
                                                <span>✕ Tolak</span>
                                            </label>
                                        </div>

                                        <!-- Catatan Admin / Alasan Revisi / Penolakan -->
                                        <div>
                                            <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">
                                                Catatan Admin / Detail Revisi
                                            </label>
                                            <textarea name="admin_notes"
                                                      rows="2"
                                                      placeholder="Cantumkan alasan jika perlu revisi atau ditolak..."
                                                      class="w-full px-3 py-2 text-xs rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-500 resize-none">{{ old('admin_notes', $rental->verification->admin_notes) }}</textarea>
                                        </div>

                                        <button type="submit"
                                                class="w-full py-2.5 px-4 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs transition shadow-sm">
                                            Simpan Keputusan Verifikasi Dokumen
                                        </button>
                                    </form>
                                </div>

                            </div>

                        </div>
                    @else
                        <div class="py-6 text-center text-slate-400 text-xs">
                            Pesanan ini dibuat sebelum sistem verifikasi kombinasi diaktifkan.
                        </div>
                    @endif
                </div>
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-4">
                    <h3 class="text-base font-bold text-slate-900 font-display">
                        Perubahan status operasional
                    </h3>

                    @if(!$rental->verification || $rental->verification->verification_status !== \App\Models\RentalVerification::STATUS_DISETUJUI)
                        <div class="mb-4 p-3 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 text-xs flex items-center gap-2">
                            <span>⚠️</span>
                            <span><strong>Peringatan Sistem:</strong> Dokumen identitas pelanggan belum diverifikasi sah (Status: {{ $rental->verification ? $rental->verification->status_label : 'Belum Ada Berkas' }}). Penyiapan dan penyerahan barang (<em>In Use</em>) diblokir backend sampai berkas disetujui.</span>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="p-3.5 rounded-2xl border border-slate-200 bg-slate-50/50">
                            <span class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">
                                Status sekarang
                            </span>
                            <div class="text-sm font-bold text-slate-900">
                                {{ $rental->status_label }}
                            </div>
                        </div>

                        <div class="p-3.5 rounded-2xl border border-slate-200 bg-slate-50/50">
                            <span class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">
                                Status berikutnya
                            </span>
                            <form method="POST" action="{{ route('admin.rentals.update_status', $rental->id) }}" id="status-update-form-{{ $rental->id }}" class="flex items-center gap-2">
                                @csrf
                                <select name="status" class="w-full bg-transparent text-sm font-bold text-brand-600 focus:outline-none">
                                    <option value="preparing" {{ $rental->status === 'confirmed' ? 'selected' : '' }}>Disiapkan (Pembersihan & Cek)</option>
                                    <option value="ready_pickup" {{ $rental->status === 'preparing' ? 'selected' : '' }}>Siap Diambil di Store</option>
                                    <option value="in_use" {{ $rental->status === 'ready_pickup' ? 'selected' : '' }}>Sedang Disewa (Diserahkan ke Penyewa)</option>
                                    <option value="completed" {{ $rental->status === 'in_use' ? 'selected' : '' }}>Selesai (Kembali & Bersih)</option>
                                </select>
                                <button type="submit" class="px-4 py-1.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs transition flex-shrink-0">
                                    Simpan Status
                                </button>
                            </form>
                        </div>
                    </div>

                    <p class="text-[11px] text-slate-400">
                        Urutan alur operasional: Menunggu Bayar → Menunggu Verifikasi → Disiapkan → Siap Diambil → Sedang Disewa → Pengembalian & Cek Fisik → Selesai.
                    </p>
                </div>

            </div>
        @endforeach
    @endif

    <!-- Modal Konfirmasi Hapus Dokumen KTP -->
    <div x-show="deleteKtpModalOpen"
         class="fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-4"
         @click="deleteKtpModalOpen = false"
         style="display: none;">
        <div class="bg-white rounded-3xl p-6 sm:p-7 max-w-md w-full space-y-4 shadow-xl border border-slate-200" @click.stop>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-rose-50 border border-rose-200 text-rose-600 flex items-center justify-center font-bold text-lg shrink-0">
                    🗑️
                </div>
                <div>
                    <h3 class="text-base font-extrabold text-slate-900 font-display">
                        Hapus Dokumen Identitas (KTP)?
                    </h3>
                    <p class="text-xs text-slate-500">
                        Dokumen identitas adalah data sensitif. Wajib menyertakan alasan.
                    </p>
                </div>
            </div>

            <div class="bg-slate-50 rounded-2xl p-3.5 border border-slate-200 text-xs space-y-1.5">
                <div class="flex justify-between">
                    <span class="text-slate-500">Kode Transaksi:</span>
                    <span class="font-mono font-bold text-slate-800" x-text="deleteKtpData ? deleteKtpData.code : ''"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Nama Pelanggan:</span>
                    <span class="font-bold text-slate-800" x-text="deleteKtpData ? deleteKtpData.name : ''"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Nama Berkas:</span>
                    <span class="font-mono text-slate-700 truncate max-w-[200px]" x-text="deleteKtpData ? deleteKtpData.filename : ''"></span>
                </div>
                <div class="pt-1 text-[11px] text-amber-700 font-medium">
                    ℹ️ Data transaksi, data verifikasi, dan riwayat penyewa tetap tersimpan aman di database.
                </div>
            </div>

            <form method="POST" :action="deleteKtpData ? '{{ url('admin/penyewaan') }}/' + deleteKtpData.id + '/dokumen/id_card' : '#'">
                @csrf
                @method('DELETE')

                <div>
                    <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1">
                        Alasan Penghapusan Dokumen KTP (Wajib Diisi) <span class="text-rose-500">*</span>
                    </label>
                    <textarea name="reason"
                              x-model="deleteKtpReason"
                              required
                              rows="3"
                              placeholder="Tuliskan alasan penghapusan dokumen KTP (min. 5 karakter)..."
                              class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-rose-500"></textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-4">
                    <button type="button"
                            @click="deleteKtpModalOpen = false"
                            class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-bold text-xs hover:bg-slate-50 transition">
                        Batal
                    </button>
                    <button type="submit"
                            :disabled="deleteKtpReason.trim().length < 5"
                            class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shadow-md shadow-rose-600/20 transition disabled:opacity-50">
                        Hapus Dokumen KTP
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
