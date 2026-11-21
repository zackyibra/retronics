@extends('layouts.app')

@section('title', 'Pesanan ' . $rental->rental_code . ' | Retronics – Rental Fotografi')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12"
     x-data="{
        selectedMethod: '{{ old('payment_method', $rental->payment_method ?? 'transfer_bca') }}',
        previewUrl: null,
        copied: false,
        qrisModalOpen: false,
        copyAccount(number) {
            navigator.clipboard.writeText(number);
            this.copied = true;
            setTimeout(() => this.copied = false, 2500);
        },
        fileChosen(event) {
            const file = event.target.files[0];
            if (file) {
                this.previewUrl = URL.createObjectURL(file);
            }
        }
     }">

    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-xs text-slate-500 mb-6">
        <a href="{{ route('home') }}" class="hover:text-brand-600 transition">Beranda</a>
        <span class="text-slate-300">›</span>
        <a href="{{ route('account.index') }}" class="hover:text-brand-600 transition">Akun Saya</a>
        <span class="text-slate-300">›</span>
        <a href="{{ route('account.rentals') }}" class="hover:text-brand-600 transition">Riwayat Sewa</a>
        <span class="text-slate-300">›</span>
        <span class="text-slate-800 font-semibold">{{ $rental->rental_code }}</span>
    </nav>

    <!-- Header & Action Button -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 font-display tracking-tight">
                Pesanan {{ $rental->rental_code }}
            </h1>
            <p class="text-slate-500 text-xs sm:text-sm mt-1">
                Dibuat {{ $rental->created_at->translatedFormat('d F Y · H.i') }} WIB · {{ $rental->rental_code }}
            </p>
            <div class="mt-2.5 flex items-center gap-2 flex-wrap">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border {{ $rental->status_badge_class }}">
                    Status Pesanan: {{ $rental->status_label }}
                </span>
                @if($rental->verification)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border {{ $rental->verification->status_badge_class }}">
                        Verifikasi Dokumen: {{ $rental->verification->status_label }}
                    </span>
                @endif
            </div>
        </div>

        <div class="flex items-center gap-3 self-start md:self-auto flex-wrap">
            @if($rental->verification)
                <a href="{{ route('rentals.agreement', $rental->id) }}"
                   target="_blank"
                   class="px-4 py-2.5 rounded-xl border border-brand-200 bg-brand-50 hover:bg-brand-100 text-brand-700 font-bold text-xs transition shadow-sm flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    <span>Surat Perjanjian Sewa</span>
                </a>
            @endif
            <a href="{{ route('account.rentals') }}"
               class="px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs transition shadow-sm">
                Lihat Riwayat Sewa
            </a>
            <a href="https://wa.me/6281929513780?text=Halo%20Admin%20Retronics%2C%20saya%20ingin%20konfirmasi%20pesanan%20sewa%20nomor%20{{ $rental->rental_code }}"
               target="_blank"
               class="px-4 py-2.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 font-bold text-xs transition flex items-center gap-1.5 shadow-sm">
                <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.711 2.598 2.669-.699c.969.586 1.838.88 2.791.88h.005c3.18 0 5.767-2.586 5.768-5.766 0-3.18-2.587-5.766-5.773-5.766zm8.814 5.765c0 4.87-3.953 8.823-8.82 8.823-1.503 0-2.915-.382-4.15-1.047l-4.575 1.199 1.226-4.464c-.75-1.309-1.196-2.825-1.196-4.511 0-4.87 3.953-8.824 8.82-8.824 4.869 0 8.82 3.954 8.82 8.824z"/></svg>
                <span>WhatsApp Admin</span>
            </a>
        </div>
    </div>

    @php
        $steps = [
            1 => ['key' => 'pending_payment', 'label' => 'Menunggu Bayar'],
            2 => ['key' => 'waiting_verification', 'label' => 'Menunggu Verifikasi'],
            3 => ['key' => 'preparing', 'label' => 'Disiapkan'],
            4 => ['key' => 'ready_pickup', 'label' => 'Siap Diambil'],
            5 => ['key' => 'in_use', 'label' => 'Sedang Disewa'],
            6 => ['key' => 'returning', 'label' => 'Pengembalian & Cek Fisik'],
            7 => ['key' => 'completed', 'label' => 'Selesai'],
        ];

        $currentStep = match($rental->status) {
            'pending_payment' => 1,
            'waiting_verification' => 2,
            'confirmed', 'preparing' => 3,
            'ready_pickup' => 4,
            'in_use' => 5,
            'returned', 'needs_maintenance' => 6,
            'completed' => 7,
            'cancelled' => 0,
            default => 1,
        };
    @endphp

    <!-- 7-Stage Stepper Component (Matching Figma Screenshot) -->
    <div class="bg-white p-6 sm:p-7 rounded-3xl border border-slate-200 shadow-sm mb-6 overflow-x-auto">
        <div class="min-w-[680px] flex items-center justify-between relative">
            @foreach($steps as $num => $step)
                @php
                    $isDone = ($currentStep > $num && $currentStep !== 0);
                    $isActive = ($currentStep === $num);
                @endphp
                <div class="flex flex-col items-center relative z-10 flex-1">
                    <div class="w-9 h-9 rounded-full flex items-center justify-center text-xs font-bold transition {{ $isActive ? 'bg-brand-600 text-white ring-4 ring-brand-100 shadow-sm' : ($isDone ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-400') }}">
                        @if($isDone)
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                        @else
                            {{ $num }}
                        @endif
                    </div>
                    <span class="text-[11px] font-bold mt-2 text-center {{ $isActive ? 'text-brand-600' : ($isDone ? 'text-slate-800' : 'text-slate-400') }}">
                        {{ $step['label'] }}
                    </span>
                </div>

                @if(!$loop->last)
                    <div class="h-0.5 flex-1 -mt-6 transition {{ $currentStep > $num && $currentStep !== 0 ? 'bg-emerald-500' : 'bg-slate-200' }}"></div>
                @endif
            @endforeach
        </div>
    </div>

    <!-- Status Alert Banner (Matching Figma Yellow Banner) -->
    @if($rental->status === 'pending_payment')
        @php
            $latestRejected = $rental->payments->where('status', 'rejected')->sortByDesc('id')->first();
        @endphp
        <div class="mb-6 p-4 rounded-2xl bg-amber-50/80 border border-amber-200 text-xs sm:text-sm text-amber-900 leading-relaxed flex items-center gap-3">
            <span class="text-base">⚠️</span>
            <div>
                @if($latestRejected)
                    <strong>Pembayaran sebelumnya ditolak:</strong> {{ $latestRejected->notes ?: 'Bukti transfer tidak sesuai atau kurang lengkap.' }}
                    <span class="block mt-0.5 text-xs text-amber-800">Silakan pilih metode pembayaran dan unggah kembali bukti pembayaran yang telah diperbaiki.</span>
                @else
                    <span>Pesanan belum dibayar. Silakan transfer sesuai total, lalu unggah bukti. Status berubah ke Menunggu Verifikasi hanya setelah bukti dikirim.</span>
                @endif
            </div>
        </div>
    @elseif($rental->status === 'waiting_verification')
        <div class="mb-6 p-4 rounded-2xl bg-blue-50/80 border border-blue-200 text-xs sm:text-sm text-blue-900 leading-relaxed flex items-center gap-3">
            <span class="text-base">⏳</span>
            <span>Bukti pembayaran sudah terkirim! Admin store sedang mencocokkan mutasi rekening. Pesanan Anda akan segera disiapkan.</span>
        </div>
    @elseif(in_array($rental->status, ['confirmed', 'preparing']))
        <div class="mb-6 p-4 rounded-2xl bg-indigo-50/80 border border-indigo-200 text-xs sm:text-sm text-indigo-900 leading-relaxed flex items-center gap-3">
            <span class="text-base">📷</span>
            <span>Pembayaran terverifikasi! Tim operasional store sedang melakukan pembersihan sensor optik dan pengisian daya baterai unit.</span>
        </div>
    @elseif($rental->status === 'ready_pickup')
        <div class="mb-6 p-4 rounded-2xl bg-emerald-50/80 border border-emerald-200 text-xs sm:text-sm text-emerald-900 leading-relaxed flex items-center gap-3">
            <span class="text-base">🎉</span>
            <span>Gear sudah siap diambil di Store Retronics Gunung Putri, Bogor! Tunjukkan KTP/identitas penyewa saat serah terima barang.</span>
        </div>
    @elseif($rental->status === 'in_use')
        <div class="mb-6 p-4 rounded-2xl bg-cyan-50/80 border border-cyan-200 text-xs sm:text-sm text-cyan-900 leading-relaxed flex items-center gap-3">
            <span class="text-base">🎬</span>
            <span>Gear sedang Anda gunakan untuk produksi. Selamat berkarya! Pastikan pengembalian tepat waktu sebelum tanggal {{ $rental->end_date->translatedFormat('d F Y') }}.</span>
        </div>
    @elseif($rental->status === 'completed')
        <div class="mb-6 p-4 rounded-2xl bg-emerald-50/80 border border-emerald-200 text-xs sm:text-sm text-emerald-900 leading-relaxed flex items-center gap-3">
            <span class="text-base">✓</span>
            <span>Penyewaan ini telah selesai. Seluruh gear telah diperiksa dan kembali ke inventaris store. Terima kasih!</span>
        </div>
    @elseif($rental->status === 'cancelled')
        <div class="mb-6 p-4 rounded-2xl bg-rose-50/80 border border-rose-200 text-xs sm:text-sm text-rose-900 leading-relaxed flex items-center gap-3">
            <span class="text-base">✕</span>
            <span>Pesanan ini telah dibatalkan. {{ $rental->admin_notes }}</span>
        </div>
    @endif

    <!-- VERIFICATION ALERT BANNER -->
    @if($rental->verification)
        @if($rental->verification->needsRevision())
            <div class="mb-6 p-5 sm:p-6 rounded-3xl bg-purple-50 border-2 border-purple-300 text-purple-950 shadow-sm" x-data="{ openUpload: false }">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex items-start gap-3">
                        <span class="text-2xl mt-0.5">⚠️</span>
                        <div>
                            <strong class="text-sm sm:text-base font-bold text-purple-900 block">Dokumen Verifikasi Membutuhkan Revisi</strong>
                            <p class="text-xs text-purple-800 mt-1">
                                Catatan Admin: <strong class="text-slate-900 underline">{{ $rental->verification->admin_notes }}</strong>
                            </p>
                            <span class="text-[11px] text-purple-600 block mt-1">Silakan unggah kembali dokumen identitas / surat izin yang telah diperbaiki agar pesanan dapat disetujui.</span>
                        </div>
                    </div>

                    <button type="button"
                            @click="openUpload = !openUpload"
                            class="px-4 py-2 rounded-xl bg-purple-600 hover:bg-purple-700 text-white font-bold text-xs transition flex-shrink-0 shadow-xs">
                        <span x-text="openUpload ? 'Tutup Formulir' : 'Perbaiki Dokumen'"></span>
                    </button>
                </div>

                <!-- Form Upload Ulang Dokumen Revisi -->
                <div x-show="openUpload" x-cloak class="mt-4 pt-4 border-t border-purple-200">
                    <form method="POST" action="{{ route('rentals.verification.resubmit', $rental->id) }}" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-purple-900 uppercase tracking-wider mb-1">
                                    Unggah Ulang Foto KTP Asli *
                                </label>
                                <input type="file"
                                       name="id_card_file"
                                       required
                                       accept="image/jpeg,image/png,application/pdf"
                                       class="w-full px-3 py-2 text-xs rounded-xl border border-purple-300 bg-white text-slate-700 file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-purple-100 file:text-purple-800">
                                <span class="block text-[10px] text-purple-700 mt-1">Pastikan foto KTP terlihat jelas, tidak buram, dan NIK terbaca dengan baik. Format: JPG, PNG, PDF (Maks. 4MB).</span>
                            </div>
                        </div>

                        <div class="flex justify-end pt-2">
                            <button type="submit" class="px-5 py-2.5 rounded-xl bg-purple-700 hover:bg-purple-800 text-white font-bold text-xs shadow-sm transition">
                                Kirim Dokumen Perbaikan ke Admin
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @elseif($rental->verification->isRejected())
            <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-xs sm:text-sm text-rose-900 leading-relaxed flex items-center gap-3">
                <span class="text-base">⛔</span>
                <div>
                    <strong>Dokumen verifikasi ditolak oleh admin store:</strong> {{ $rental->verification->admin_notes ?: 'Dokumen tidak memenuhi persyaratan verifikasi identitas Retronics.' }}
                </div>
            </div>
        @elseif($rental->verification->isApproved())
            <div class="mb-6 p-4 rounded-2xl bg-emerald-50/80 border border-emerald-200 text-xs sm:text-sm text-emerald-900 leading-relaxed flex items-center justify-between gap-3">
                <div class="flex items-center gap-2.5">
                    <span class="text-base">✓</span>
                    <span>Dokumen identitas dan Surat Perjanjian Sewa telah <strong>diverifikasi & disetujui sah</strong> oleh admin store.</span>
                </div>
                <a href="{{ route('rentals.agreement', $rental->id) }}" target="_blank" class="text-xs font-bold text-emerald-700 hover:text-emerald-900 underline whitespace-nowrap">
                    Buka Dokumen Kontrak →
                </a>
            </div>
        @endif
    @endif

    <!-- Overdue & Damage Fine Box (If fines exist) -->
    @if($rental->fines->isNotEmpty())
        <div class="mb-6 p-5 sm:p-6 rounded-3xl bg-rose-50 border border-rose-200 text-rose-900 space-y-3 shadow-xs">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2 font-extrabold text-sm sm:text-base font-display text-rose-950">
                    <span>⚠️</span>
                    <span>Rincian Denda Transaksi</span>
                </div>
                <div class="text-right">
                    <span class="text-xs text-rose-700 block font-medium">Total Denda:</span>
                    <span class="text-base sm:text-lg font-black font-display text-rose-700">
                        Rp{{ number_format($rental->total_fines, 0, ',', '.') }}
                    </span>
                </div>
            </div>

            <div class="divide-y divide-rose-100 pt-1">
                @foreach($rental->fines as $fine)
                    <div class="py-2.5 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs">
                        <div>
                            <span class="font-bold text-rose-900">
                                {{ $fine->type === \App\Models\Fine::TYPE_LATE ? 'Denda Keterlambatan' : 'Denda Kerusakan' }}
                            </span>
                            @if($fine->rentalItem && $fine->rentalItem->product)
                                <span class="text-slate-600">({{ $fine->rentalItem->product->name }})</span>
                            @endif
                            <p class="text-[11px] text-rose-700 mt-0.5">
                                {{ $fine->reason ?: ($fine->isLate() ? "Terlambat {$fine->days_late} hari dari jadwal batas sewa." : 'Biaya perbaikan kerusakan unit.') }}
                            </p>
                        </div>
                        <div class="font-extrabold text-sm text-rose-700 text-left sm:text-right shrink-0">
                            Rp{{ number_format($fine->amount, 0, ',', '.') }}
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="pt-2 border-t border-rose-200/80 text-[11px] text-rose-800 leading-relaxed">
                *Pelunasan denda dilakukan langsung di counter store Retronics saat serah terima barang kembali.
            </div>
        </div>
    @endif

    <!-- Main Grid Layout (Left 7 cols, Right 5 cols) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start mb-8">

        <!-- LEFT COLUMN: Payment Method & Proof Upload -->
        <div class="lg:col-span-7 space-y-6 min-w-0">

            <!-- 01 / Pilih metode pembayaran -->
            <div class="bg-white p-6 sm:p-7 rounded-3xl border border-slate-200 shadow-sm space-y-5 min-w-0">
                <h2 class="text-lg font-bold text-slate-900 font-display">
                    01 / Pilih metode pembayaran
                </h2>

                <!-- Method Radio Pills (BCA, Mandiri, QRIS) -->
                <div class="flex flex-wrap items-center gap-3">
                    <button type="button"
                            @click="selectedMethod = 'transfer_bca'"
                            class="px-4 py-2.5 rounded-xl border text-xs font-bold transition flex items-center gap-2"
                            :class="selectedMethod === 'transfer_bca' ? 'border-brand-600 bg-brand-50/50 text-brand-700 ring-2 ring-brand-100' : 'border-slate-200 text-slate-700 hover:bg-slate-50'">
                        <span class="w-2.5 h-2.5 rounded-full" :class="selectedMethod === 'transfer_bca' ? 'bg-brand-600' : 'border border-slate-400'"></span>
                        <span>● BCA</span>
                    </button>

                    <button type="button"
                            @click="selectedMethod = 'transfer_mandiri'"
                            class="px-4 py-2.5 rounded-xl border text-xs font-bold transition flex items-center gap-2"
                            :class="selectedMethod === 'transfer_mandiri' ? 'border-brand-600 bg-brand-50/50 text-brand-700 ring-2 ring-brand-100' : 'border-slate-200 text-slate-700 hover:bg-slate-50'">
                        <span class="w-2.5 h-2.5 rounded-full" :class="selectedMethod === 'transfer_mandiri' ? 'bg-brand-600' : 'border border-slate-400'"></span>
                        <span>○ Mandiri</span>
                    </button>

                    <button type="button"
                            @click="selectedMethod = 'qris'"
                            class="px-4 py-2.5 rounded-xl border text-xs font-bold transition flex items-center gap-2"
                            :class="selectedMethod === 'qris' ? 'border-brand-600 bg-brand-50/50 text-brand-700 ring-2 ring-brand-100' : 'border-slate-200 text-slate-700 hover:bg-slate-50'">
                        <span class="w-2.5 h-2.5 rounded-full" :class="selectedMethod === 'qris' ? 'bg-brand-600' : 'border border-slate-400'"></span>
                        <span>○ QRIS</span>
                    </button>
                </div>

                <!-- Account Detail Card Container -->
                <div class="p-5 rounded-2xl bg-slate-50/70 border border-slate-200 space-y-4">
                    <!-- If BCA -->
                    <div x-show="selectedMethod === 'transfer_bca'" class="space-y-3">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">
                            TRANSFER MANUAL · BCA
                        </span>
                        <div class="text-xl sm:text-2xl font-extrabold text-slate-900 font-display tracking-tight flex items-center justify-between">
                            <span>829-0192-881</span>
                            <button type="button"
                                    @click="copyAccount('8290192881')"
                                    class="px-3.5 py-1.5 rounded-xl bg-white border border-slate-200 text-xs font-bold text-slate-700 hover:bg-slate-50 transition shadow-sm">
                                <span x-show="!copied">Salin Rekening</span>
                                <span x-show="copied" class="text-emerald-600">✓ Disalin!</span>
                            </button>
                        </div>
                        <p class="text-xs text-slate-600 font-medium">
                            Atas nama: <strong>Retronics Rental Kamera PT</strong>
                        </p>
                    </div>

                    <!-- If Mandiri -->
                    <div x-show="selectedMethod === 'transfer_mandiri'" class="space-y-3">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">
                            TRANSFER MANUAL · MANDIRI
                        </span>
                        <div class="text-xl sm:text-2xl font-extrabold text-slate-900 font-display tracking-tight flex items-center justify-between">
                            <span>141-00-9921-882</span>
                            <button type="button"
                                    @click="copyAccount('141009921882')"
                                    class="px-3.5 py-1.5 rounded-xl bg-white border border-slate-200 text-xs font-bold text-slate-700 hover:bg-slate-50 transition shadow-sm">
                                <span x-show="!copied">Salin Rekening</span>
                                <span x-show="copied" class="text-emerald-600">✓ Disalin!</span>
                            </button>
                        </div>
                        <p class="text-xs text-slate-600 font-medium">
                            Atas nama: <strong>Retronics Rental Kamera PT</strong>
                        </p>
                    </div>

                    <!-- If QRIS -->
                    @php
                        $activeQris = \App\Models\QrisSetting::getActive();
                    @endphp
                    <div x-show="selectedMethod === 'qris'" class="space-y-4">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">
                            PEMBAYARAN QRIS NASIONAL (DANA / GOPAY / OVO / SHOPEEPAY / M-BANKING)
                        </span>

                        @if($activeQris)
                            <div class="p-4 bg-white border border-slate-200 rounded-2xl shadow-sm space-y-4">
                                <div class="flex flex-col sm:flex-row items-center sm:items-start gap-4">
                                    <!-- QRIS Image with Zoom Trigger -->
                                    <div class="relative group cursor-pointer bg-slate-50 p-2.5 rounded-2xl border border-slate-200 shadow-xs flex-shrink-0 text-center"
                                         @click="qrisModalOpen = true">
                                        <img src="{{ asset('storage/' . $activeQris->image_path) }}"
                                             alt="QRIS {{ $activeQris->merchant_name }}"
                                             class="w-44 sm:w-48 h-auto object-contain rounded-xl border border-slate-200 bg-white">
                                        <span class="inline-flex items-center gap-1 mt-2 px-2.5 py-1 rounded-lg bg-slate-200/70 text-slate-700 text-[10px] font-bold group-hover:bg-brand-600 group-hover:text-white transition">
                                            🔍 Perbesar / Scan
                                        </span>
                                    </div>

                                    <!-- QRIS Details & Instructions -->
                                    <div class="space-y-2.5 text-xs w-full">
                                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 space-y-1.5">
                                            <div class="flex items-center justify-between">
                                                <span class="text-slate-500 text-[11px]">Nama Merchant:</span>
                                                <span class="font-extrabold text-slate-900">{{ $activeQris->merchant_name }}</span>
                                            </div>
                                            <div class="flex items-center justify-between">
                                                <span class="text-slate-500 text-[11px]">NMID:</span>
                                                <span class="font-mono font-bold text-slate-800">{{ $activeQris->nmid }}</span>
                                            </div>
                                            @if($activeQris->account_notes)
                                                <div class="text-[11px] text-slate-500 pt-1 border-t border-slate-200/50">
                                                    {{ $activeQris->account_notes }}
                                                </div>
                                            @endif
                                        </div>

                                        <ol class="list-decimal list-inside space-y-1 text-[11px] text-slate-600 leading-relaxed">
                                            <li>Buka aplikasi <strong>DANA, GoPay, OVO, ShopeePay</strong>, atau <strong>BCA/Mandiri Mobile</strong>.</li>
                                            <li>Pindai (scan) kode QRIS di samping atau klik gambar untuk mode layar penuh.</li>
                                            <li>Pastikan nama merchant tertera <strong>{{ $activeQris->merchant_name }}</strong>.</li>
                                            <li>Masukkan nominal tepat <strong>Rp{{ number_format($rental->total_price, 0, ',', '.') }}</strong>.</li>
                                            <li>Simpan tangkapan layar (screenshot) bukti transaksi berhasil, lalu unggah pada form di bawah.</li>
                                        </ol>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-800 text-xs space-y-1.5">
                                <div class="font-bold flex items-center gap-1.5">
                                    <span>⚠️ Layanan QRIS Sedang Dalam Pemeliharaan</span>
                                </div>
                                <p class="text-[11px] leading-relaxed">
                                    Metode pembayaran QRIS sementara tidak dapat digunakan. Silakan pilih opsi transfer manual <strong>BCA</strong> atau <strong>Mandiri</strong> di atas untuk menyelesaikan pembayaran sewa Anda.
                                </p>
                            </div>
                        @endif
                    </div>

                    <!-- Subtext notice -->
                    <div class="pt-3 border-t border-slate-200 text-[11px] text-slate-500 flex items-center justify-between">
                        <span>Total yang ditransfer:</span>
                        <span class="text-lg font-extrabold text-brand-600 font-display">
                            Rp{{ number_format($rental->total_price, 0, ',', '.') }}
                        </span>
                    </div>
                </div>

                <!-- Light Blue Alert Notice (Matching Screenshot) -->
                <div class="p-4 rounded-2xl bg-blue-50/70 border border-blue-100 text-xs text-brand-800 leading-relaxed">
                    Masukkan nominal persis <strong>Rp{{ number_format($rental->total_price, 0, ',', '.') }}</strong>. Pembayaran tidak otomatis terverifikasi; admin memeriksa bukti fisik mutasi rekening terlebih dahulu.
                </div>
            </div>

            <!-- 02 / Unggah bukti transfer -->
            <div class="bg-white p-6 sm:p-7 rounded-3xl border border-slate-200 shadow-sm space-y-5 min-w-0">
                <h2 class="text-lg font-bold text-slate-900 font-display">
                    02 / Unggah bukti transfer
                </h2>

                @if($rental->payment_proof)
                    <div class="p-4 rounded-2xl border border-slate-200 bg-slate-50/50 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-700">Bukti Transfer Terkirim</span>
                            <span class="text-[11px] text-emerald-600 font-bold">✓ Tersimpan di sistem</span>
                        </div>
                        <div class="aspect-video max-w-sm rounded-xl overflow-hidden border border-slate-200 bg-black">
                            <img src="{{ asset('storage/' . $rental->payment_proof) }}"
                                 alt="Bukti Transfer {{ $rental->rental_code }}"
                                 class="w-full h-full object-contain">
                        </div>
                        <p class="text-[11px] text-slate-500">
                            Metode: <strong class="uppercase">{{ str_replace('_', ' ', $rental->payment_method) }}</strong>. Diunggah pada {{ $rental->updated_at->translatedFormat('d M Y, H.i') }} WIB.
                        </p>
                    </div>
                @endif

                @if($rental->status === 'pending_payment')
                    <form method="POST" action="{{ route('rentals.pay', $rental->id) }}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="payment_method" :value="selectedMethod">

                        <!-- Drag and Drop Box (Matching Figma Screenshot) -->
                        <div class="relative border-2 border-dashed border-slate-200 hover:border-brand-500 rounded-3xl p-8 text-center bg-slate-50/50 transition">
                            <!-- Cloud Icon -->
                            <div class="w-12 h-12 rounded-2xl bg-brand-50 text-brand-600 flex items-center justify-center mx-auto mb-3">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                            </div>

                            <p class="text-sm font-bold text-slate-800">
                                Seret bukti transfer ke sini
                            </p>
                            <p class="text-xs text-slate-400 mt-1 mb-4">
                                JPG, PNG, atau WEBP · Maks. 3 MB
                            </p>

                            <!-- Custom File Input Button -->
                            <label for="payment_proof" class="inline-flex items-center px-4 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs shadow-sm cursor-pointer transition">
                                <span>Pilih Gambar</span>
                                <input type="file"
                                       id="payment_proof"
                                       name="payment_proof"
                                       accept="image/*"
                                       required
                                       @change="fileChosen($event)"
                                       class="sr-only">
                            </label>

                            <!-- Live Image Preview -->
                            <template x-if="previewUrl">
                                <div class="mt-4 max-w-xs mx-auto p-2 bg-white rounded-xl border border-slate-200 shadow-sm">
                                    <img :src="previewUrl" alt="Preview Bukti" class="w-full h-36 object-contain rounded-lg">
                                    <span class="block text-[11px] text-emerald-600 font-bold mt-1">✓ Berkas siap dikirim</span>
                                </div>
                            </template>
                        </div>

                        <!-- Submit Button -->
                        <div class="mt-4">
                            <button type="submit"
                                    class="w-full py-4 px-6 rounded-2xl font-bold text-sm text-white bg-brand-600 hover:bg-brand-700 shadow-lg shadow-brand-500/25 transition flex items-center justify-center gap-2">
                                <span>{{ $rental->payment_proof ? 'Perbarui Bukti Pembayaran' : 'Kirim Bukti Pembayaran' }}</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </button>
                            <p class="text-[11px] text-slate-400 text-center mt-2">
                                Tombol aktif setelah gambar dipilih. Admin akan mencocokkan bukti dengan mutasi rekening store.
                            </p>
                        </div>
                    </form>
                @endif
            </div>

            <!-- 03 / Riwayat Pembayaran (Payments Entity) -->
            @if($rental->payments->isNotEmpty())
                <div class="bg-white p-6 sm:p-7 rounded-3xl border border-slate-200 shadow-sm space-y-4 min-w-0">
                    <h2 class="text-lg font-bold text-slate-900 font-display flex items-center justify-between">
                        <span>Riwayat Pembayaran</span>
                        <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-600">
                            {{ $rental->payments->count() }} catatan
                        </span>
                    </h2>

                    <div class="divide-y divide-slate-100">
                        @foreach($rental->payments->sortByDesc('id') as $paymentItem)
                            <div class="py-3 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs">
                                <div class="space-y-0.5 min-w-0">
                                    <div class="flex items-center gap-2">
                                        <span class="font-mono font-bold text-slate-900">{{ $paymentItem->payment_code }}</span>
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $paymentItem->status_badge_class }}">
                                            {{ $paymentItem->status_label }}
                                        </span>
                                    </div>
                                    <div class="text-[11px] text-slate-500">
                                        {{ $paymentItem->payment_method_label }} · {{ $paymentItem->payment_date ? $paymentItem->payment_date->translatedFormat('d M Y, H.i') : $paymentItem->created_at->translatedFormat('d M Y, H.i') }} WIB
                                    </div>
                                    @if($paymentItem->notes)
                                        <div class="text-[11px] text-slate-600 italic">
                                            Catatan: "{{ $paymentItem->notes }}"
                                        </div>
                                    @endif
                                </div>
                                <div class="text-left sm:text-right shrink-0">
                                    <span class="font-extrabold text-slate-900 block">
                                        Rp{{ number_format($paymentItem->amount, 0, ',', '.') }}
                                    </span>
                                    @if($paymentItem->payment_proof)
                                        <a href="{{ asset('storage/' . $paymentItem->payment_proof) }}" target="_blank" class="text-[11px] font-bold text-brand-600 hover:underline">
                                            Lihat Bukti ↗
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>

        <!-- RIGHT COLUMN: Gear dalam Pesanan, Rincian Sewa, Penyewa, & Actions -->
        <div class="lg:col-span-5 space-y-6 min-w-0">

            <!-- Card 1: Gear dalam pesanan -->
            <div class="bg-white p-6 sm:p-7 rounded-3xl border border-slate-200 shadow-sm space-y-4 min-w-0">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-bold text-slate-900 font-display">
                        Gear dalam pesanan
                    </h2>
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-brand-50 text-brand-600 border border-brand-100">
                        {{ $rental->items->sum('quantity') }} unit
                    </span>
                </div>

                <div class="divide-y divide-slate-100">
                    @foreach($rental->items as $item)
                        <div class="py-3.5 first:pt-0 last:pb-0 flex items-center gap-3.5 min-w-0">
                            <div class="w-16 h-16 rounded-2xl overflow-hidden bg-slate-100 border border-slate-200 shrink-0">
                                <img src="{{ $item->product->image ? (str_starts_with($item->product->image, 'http') ? $item->product->image : asset('storage/' . $item->product->image)) : 'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?auto=format&fit=crop&w=300&q=80' }}"
                                     alt="{{ $item->product->name }}"
                                     class="w-full h-full object-cover">
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="font-bold text-slate-900 text-xs sm:text-sm truncate" title="{{ $item->product->name }}">
                                    {{ $item->product->name }}
                                </h3>
                                <div class="text-[11px] text-slate-400 mt-0.5">
                                    {{ $item->product->category->name ?? 'Gear' }}
                                </div>
                                <div class="text-[11px] text-slate-500 mt-1 font-medium">
                                    {{ $item->quantity }} unit · Rp{{ number_format($item->price_per_day, 0, ',', '.') }} / hari
                                </div>
                            </div>
                            <div class="text-right shrink-0">
                                <span class="font-extrabold text-xs sm:text-sm text-slate-900 block">
                                    Rp{{ number_format($item->subtotal, 0, ',', '.') }}
                                </span>
                                <span class="text-[10px] text-slate-400">
                                    {{ $rental->duration_days }} hari
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Card 2: Rincian sewa -->
            <div class="bg-white p-6 sm:p-7 rounded-3xl border border-slate-200 shadow-sm space-y-4 min-w-0">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-bold text-slate-900 font-display">
                        Rincian sewa
                    </h2>
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700">
                        {{ $rental->duration_days }} hari sewa
                    </span>
                </div>

                <!-- Schedule & Store Details -->
                <div class="space-y-1.5 text-xs text-slate-600 bg-slate-50/70 p-4 rounded-2xl border border-slate-100">
                    <div class="font-extrabold text-sm text-slate-900">
                        {{ $rental->start_date->translatedFormat('d M') }} – {{ $rental->end_date->translatedFormat('d M Y') }}
                    </div>
                    <div>Pengambilan: <strong>{{ $rental->start_date->translatedFormat('d M Y') }}</strong> (mulai 10.00 WIB)</div>
                    <div>Pengembalian: <strong>{{ $rental->end_date->translatedFormat('d M Y') }}</strong> (jam disepakati)</div>
                    <div class="text-slate-500 pt-1 border-t border-slate-200/60 mt-1.5">
                        Lokasi Store: Retronics Gunung Putri, Kabupaten Bogor
                    </div>
                </div>

                <!-- Itemized Breakdown -->
                <div class="pt-2 space-y-2.5 text-xs sm:text-sm">
                    @php
                        $grossSum = $rental->items->sum('subtotal');
                        $diff = $grossSum - $rental->total_price;
                    @endphp

                    <div class="space-y-2 text-xs">
                        <div class="flex items-center justify-between text-slate-600">
                            <span>Subtotal sewa ({{ $rental->items->sum('quantity') }} unit)</span>
                            <span class="font-bold text-slate-900">Rp{{ number_format($grossSum, 0, ',', '.') }}</span>
                        </div>

                        @if($diff > 0)
                            <div class="flex items-center justify-between text-emerald-600 font-medium">
                                <span>Potongan promo / durasi</span>
                                <span class="font-bold">-Rp{{ number_format($diff, 0, ',', '.') }}</span>
                            </div>
                        @endif
                    </div>

                    <!-- Total Akhir -->
                    <div class="pt-4 border-t border-slate-200 flex items-baseline justify-between">
                        <span class="text-sm font-bold text-slate-900">Total tagihan</span>
                        <span class="text-2xl sm:text-3xl font-extrabold text-brand-600 font-display tracking-tight">
                            Rp{{ number_format($rental->total_price, 0, ',', '.') }}
                        </span>
                    </div>
                </div>

                <p class="text-[11px] text-slate-400 leading-relaxed">
                    Tarif durasi dan diskon dihitung otomatis sesuai kebijakan rental Retronics.
                </p>
            </div>

            <!-- Card 3: Penyewa -->
            <div class="bg-white p-6 sm:p-7 rounded-3xl border border-slate-200 shadow-sm space-y-3 text-xs sm:text-sm min-w-0">
                <h2 class="text-base font-bold text-slate-900 font-display">
                    Identitas Penyewa
                </h2>

                <div class="space-y-1.5 text-slate-700">
                    <div class="font-bold text-slate-900 text-sm">{{ $rental->user->name }}</div>
                    <div>WhatsApp: <strong>{{ $rental->user->phone ?? $rental->user->email }}</strong></div>
                    <div>Domisili: {{ $rental->user->city ?? $rental->user->address ?? 'Gunung Putri, Bogor' }}</div>
                    @if($rental->notes)
                        <div class="pt-2 text-xs text-slate-500 italic border-t border-slate-100 mt-2">
                            Catatan: "{{ $rental->notes }}"
                        </div>
                    @endif
                </div>
            </div>

            <!-- Cancel Button if applicable -->
            @if(in_array($rental->status, ['pending_payment', 'waiting_verification']))
                <form method="POST"
                      action="{{ route('rentals.cancel', $rental->id) }}"
                      onsubmit="return confirm('Apakah Anda yakin ingin membatalkan permintaan penyewaan ini?')">
                    @csrf
                    <button type="submit"
                            class="w-full py-3 px-4 rounded-2xl border border-slate-200 text-slate-500 hover:text-rose-600 hover:bg-rose-50 hover:border-rose-200 font-bold text-xs transition shadow-xs">
                        Batalkan Permintaan Sewa
                    </button>
                </form>
            @endif

        </div>

    </div>

    <!-- Modal Lightbox Preview QRIS Penuh -->
    @if($activeQris)
        <div x-show="qrisModalOpen"
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/80 backdrop-blur-xs"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             style="display: none;">
            <div class="bg-white rounded-3xl p-6 max-w-md w-full shadow-2xl space-y-4 relative"
                 @click.outside="qrisModalOpen = false">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div>
                        <h3 class="font-bold text-slate-900 font-display">QRIS {{ $activeQris->merchant_name }}</h3>
                        <p class="text-[11px] font-mono text-slate-500">{{ $activeQris->nmid }}</p>
                    </div>
                    <button type="button"
                            @click="qrisModalOpen = false"
                            class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold flex items-center justify-center transition">
                        ✕
                    </button>
                </div>

                <div class="text-center p-3 bg-slate-50 rounded-2xl border border-slate-200">
                    <img src="{{ asset('storage/' . $activeQris->image_path) }}"
                         alt="QRIS {{ $activeQris->merchant_name }}"
                         class="max-h-[60vh] w-auto mx-auto object-contain rounded-xl">
                </div>

                <div class="text-center text-xs text-slate-600 space-y-1">
                    <p>Total Tagihan: <strong class="text-brand-600 font-display text-base">Rp{{ number_format($rental->total_price, 0, ',', '.') }}</strong></p>
                    <p class="text-[11px] text-slate-400">Pindai langsung menggunakan kamera/scanner aplikasi Anda.</p>
                </div>
            </div>
        </div>
    @endif

</div>
@endsection
