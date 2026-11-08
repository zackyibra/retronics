@extends('layouts.app')

@section('title', 'Konfirmasi Pemesanan & Verifikasi Sewa | Retronics')

@section('content')
@php
    $promosData = ($activePromotions ?? collect())->map(function ($p) {
        return [
            'code' => $p->code,
            'type' => $p->type,
            'value' => (float) $p->discount_value,
            'min' => (float) $p->min_transaction,
            'max' => $p->max_discount ? (float) $p->max_discount : null,
            'label' => $p->discount_label,
        ];
    })->values();
@endphp

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12"
     x-data="checkoutPage({
        initialDays: {{ (int) $initialDays }},
        initialStartDate: '{{ $initialStartDate }}',
        promotions: {{ $promosData->toJson() }},
        items: [
            @foreach($initialItems as $idx => $initItem)
            {
                id: {{ $initItem['product']->id }},
                name: '{{ addslashes($initItem['product']->name) }}',
                category: '{{ addslashes($initItem['product']->category->name ?? 'Gear') }}',
                pricePerDay: {{ (float) $initItem['product']->price_per_day }},
                maxStock: {{ (int) $initItem['product']->stock }},
                image: '{{ $initItem['product']->image ? (str_starts_with($initItem['product']->image, 'http') ? $initItem['product']->image : asset('storage/' . $initItem['product']->image)) : 'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?auto=format&fit=crop&w=500&q=80' }}',
                qty: {{ (int) $initItem['quantity'] }},
                selected: true
            },
            @endforeach
        ]
     })">

    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-xs text-slate-500 mb-6">
        <a href="{{ route('home') }}" class="hover:text-brand-600 transition">Beranda</a>
        <span class="text-slate-300">›</span>
        <a href="{{ route('cart.index') }}" class="hover:text-brand-600 transition">Keranjang</a>
        <span class="text-slate-300">›</span>
        <span class="text-slate-800 font-semibold">Konfirmasi & Verifikasi Dokumen</span>
    </nav>

    <!-- Page Title & Step Pill -->
    <div class="mb-8">
        <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 font-display tracking-tight">
            Formulir Sewa & Verifikasi Identitas
        </h1>
        <p class="text-slate-500 text-xs sm:text-sm mt-1">
            Lengkapi data identitas, tujuan pemakaian, dan tanda tangani surat perjanjian sewa resmi sebelum melanjutkan pembayaran.
        </p>

        <!-- Stepper Pill -->
        <div class="flex items-center gap-3 mt-4 text-xs font-semibold">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                <span>Keranjang</span>
            </span>
            <span class="text-slate-300">—</span>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-brand-50 text-brand-600 font-bold border border-brand-200">
                <span>2 · Formulir & Verifikasi Legal</span>
            </span>
            <span class="text-slate-300">—</span>
            <span class="text-slate-400">3 · Pembayaran & Verifikasi Admin</span>
        </div>
    </div>

    @if ($errors->any())
        <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-xs sm:text-sm">
            <div class="font-bold mb-1">Mohon perbaiki data berikut:</div>
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('rentals.store') }}" id="checkout-form" enctype="multipart/form-data" @submit="handleSubmit($event)">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

            <!-- LEFT COLUMN: Stepped Sections (lg:col-span-7) -->
            <div class="lg:col-span-7 space-y-6">

                <!-- 01 / Jadwal sewa -->
                <div class="bg-white p-6 sm:p-7 rounded-3xl border border-slate-200 shadow-sm space-y-5">
                    <h2 class="text-lg font-bold text-slate-900 font-display flex items-center gap-2">
                        <span>01 / Jadwal sewa</span>
                    </h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Tanggal Mulai -->
                        <div class="p-3.5 rounded-2xl border border-slate-200 bg-slate-50/50 hover:border-slate-300 transition">
                            <label for="start_date" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">
                                Tanggal mulai *
                            </label>
                            <input type="date"
                                   id="start_date"
                                   name="start_date"
                                   x-model="startDate"
                                   min="{{ date('Y-m-d') }}"
                                   required
                                   class="w-full bg-transparent text-sm font-bold text-slate-800 focus:outline-none">
                            <span class="block text-[11px] text-slate-400 mt-1">
                                Minimal hari ini: {{ \Carbon\Carbon::today()->translatedFormat('d M Y') }}.
                            </span>
                        </div>

                        <!-- Durasi -->
                        <div class="p-3.5 rounded-2xl border border-slate-200 bg-slate-50/50 hover:border-slate-300 transition">
                            <label for="duration_days" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">
                                Durasi *
                            </label>
                            <select id="duration_days"
                                    name="duration_days"
                                    x-model.number="durationDays"
                                    required
                                    class="w-full bg-transparent text-sm font-bold text-slate-800 focus:outline-none cursor-pointer">
                                <option value="1">1 hari</option>
                                <option value="2">2 hari (Hemat 5%)</option>
                                <option value="3">3 hari (Hemat 15%)</option>
                                <option value="4">4 hari</option>
                                <option value="5">5 hari</option>
                                <option value="6">6 hari</option>
                                <option value="7">7 hari (Hemat 25%)</option>
                                <option value="14">14 hari (Hemat 25%)</option>
                            </select>
                            <span class="block text-[11px] text-slate-400 mt-1" x-text="'Kembali ' + calculateReturnDate(startDate, durationDays) + '.'">
                                Kembali --.
                            </span>
                        </div>
                    </div>

                    <!-- Blue Info Box -->
                    <div class="p-4 rounded-2xl bg-blue-50/70 border border-blue-100 text-xs text-brand-800 leading-relaxed flex items-start gap-2.5">
                        <svg class="w-4 h-4 text-brand-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span>Semua gear mengikuti jadwal yang sama. Jam kembali maksimal pukul 21.00 WIB pada tanggal pengembalian.</span>
                    </div>
                </div>

                <!-- 02 / Gear & jumlah unit -->
                <div class="bg-white p-6 sm:p-7 rounded-3xl border border-slate-200 shadow-sm space-y-5">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-bold text-slate-900 font-display">
                            02 / Gear & jumlah unit
                        </h2>
                        <a href="{{ route('products.index') }}" class="text-xs font-bold text-brand-600 hover:text-brand-700">
                            + Tambah gear lain
                        </a>
                    </div>

                    <!-- Items list -->
                    <div class="space-y-3.5">
                        <template x-for="(item, index) in items" :key="item.id">
                            <div class="p-4 rounded-2xl border transition flex items-center justify-between gap-4"
                                 :class="item.selected ? 'border-slate-200 bg-white' : 'border-slate-100 bg-slate-50 opacity-60'">
                                <div class="flex items-center gap-3.5 min-w-0">
                                    <input type="checkbox"
                                           :checked="item.selected"
                                           @change="item.selected = !item.selected"
                                           class="w-4 h-4 text-brand-600 rounded border-slate-300 focus:ring-brand-500 cursor-pointer">

                                    <img :src="item.image"
                                         :alt="item.name"
                                         class="w-16 h-16 rounded-xl object-cover bg-slate-100 border border-slate-100 flex-shrink-0">

                                    <div class="min-w-0">
                                        <h3 class="text-sm font-bold text-slate-900 truncate" x-text="item.name"></h3>
                                        <p class="text-xs text-slate-500 mt-0.5" x-text="formatIDR(item.pricePerDay) + ' /hari · ' + durationDays + ' hari'"></p>
                                    </div>
                                </div>

                                <!-- Quantity Controls -->
                                <div class="flex items-center border border-slate-200 rounded-xl bg-slate-50 p-1 flex-shrink-0" x-show="item.selected">
                                    <button type="button"
                                            @click="if(item.qty > 1) item.qty--"
                                            class="w-7 h-7 flex items-center justify-center text-xs font-bold text-slate-600 hover:bg-white rounded-lg transition">
                                        -
                                    </button>
                                    <span class="w-8 text-center text-xs font-bold text-slate-900" x-text="item.qty"></span>
                                    <button type="button"
                                            @click="if(item.qty < item.maxStock) item.qty++"
                                            class="w-7 h-7 flex items-center justify-center text-xs font-bold text-slate-600 hover:bg-white rounded-lg transition">
                                        +
                                    </button>
                                </div>

                                <!-- Hidden Inputs for Submission -->
                                <template x-if="item.selected">
                                    <div>
                                        <input type="hidden" :name="'products[' + index + '][id]'" :value="item.id">
                                        <input type="hidden" :name="'products[' + index + '][quantity]'" :value="item.qty">
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>

                    <!-- Summary footer note in card 02 -->
                    <div class="pt-3 border-t border-slate-100 text-xs text-slate-500 flex items-center justify-between">
                        <span x-text="'Total ' + selectedUnitsCount() + ' unit · Subtotal harian ' + formatIDR(dailySubtotal())"></span>
                        <span class="text-[11px] text-slate-400">Stok otomatis direservasi setelah verifikasi disetujui</span>
                    </div>
                </div>

                <!-- 03 / Formulir Penyewaan & Identitas Pelanggan -->
                <div class="bg-white p-6 sm:p-7 rounded-3xl border border-slate-200 shadow-sm space-y-6">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900 font-display">
                            03 / Formulir Penyewaan & Identitas Pelanggan (Wajib KTP)
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Kebijakan Retronics: Penyewa wajib berusia minimal 17 tahun dan melampirkan identitas KTP asli yang sah. Data identitas disimpan terenkripsi dan aman.
                        </p>
                    </div>


                    <!-- DATA AKUN PENYEWA -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="p-3.5 rounded-2xl border border-slate-200 bg-slate-50/50">
                            <span class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">
                                Nama Penyewa
                            </span>
                            <div class="text-sm font-bold text-slate-900">
                                {{ auth()->user()->name }}
                            </div>
                        </div>

                        <div class="p-3.5 rounded-2xl border border-slate-200 bg-slate-50/50">
                            <span class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">
                                Nomor WhatsApp Aktif
                            </span>
                            <div class="text-sm font-semibold text-slate-800">
                                {{ auth()->user()->phone ?? 'Belum diatur' }}
                            </div>
                        </div>
                    </div>

                    <!-- BLOK VERIFIKASI IDENTITAS KTP & USIA MINIMAL 17 TAHUN -->
                    <div class="space-y-4 p-5 rounded-2xl bg-slate-50 border border-slate-200">
                        <div class="flex items-center gap-2 text-xs font-bold text-brand-700">
                            <span>📄 Dokumen Wajib: KTP Asli (Minimal Usia 17 Tahun)</span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label for="id_card_number" class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1">
                                    Nomor NIK KTP (16 Digit) *
                                </label>
                                <input type="text"
                                       id="id_card_number"
                                       name="id_card_number"
                                       value="{{ old('id_card_number', auth()->user()->id_card_number) }}"
                                       placeholder="3578xxxxxxxxxxxx"
                                       required
                                       maxlength="20"
                                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm font-semibold text-slate-800 bg-white focus:outline-none focus:ring-2 focus:ring-brand-500">
                            </div>

                            <div>
                                <label for="birth_date" class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1">
                                    Tanggal Lahir *
                                </label>
                                <input type="date"
                                       id="birth_date"
                                       name="birth_date"
                                       value="{{ old('birth_date') }}"
                                       max="{{ now()->subYears(17)->format('Y-m-d') }}"
                                       required
                                       class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm font-semibold text-slate-800 bg-white focus:outline-none focus:ring-2 focus:ring-brand-500">
                                <span class="block text-[10px] text-slate-400 mt-1">Batas usia min. 17 tahun.</span>
                            </div>

                            <div>
                                <label for="id_card_file" class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1">
                                    Unggah Foto KTP Asli *
                                </label>
                                <input type="file"
                                       id="id_card_file"
                                       name="id_card_file"
                                       accept="image/jpeg,image/png,application/pdf"
                                       required
                                       class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 bg-white text-slate-700 file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100">
                                <span class="block text-[10px] text-slate-400 mt-1">JPG, PNG, atau PDF (Maks. 4MB).</span>
                            </div>
                        </div>
                    </div>

                    <!-- FORMULIR PENYEWAAN: KEPERLUAN & KONTAK DARURAT -->
                    <div class="space-y-4 pt-2 border-t border-slate-100">
                        <div>
                            <label for="rental_purpose" class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1">
                                Keperluan / Tujuan Sewa *
                            </label>
                            <input type="text"
                                   id="rental_purpose"
                                   name="rental_purpose"
                                   value="{{ old('rental_purpose') }}"
                                   placeholder="Contoh: Photoshoot Wedding di Bogor, Pembuatan Film Pendek, Hobi Hunting Foto"
                                   required
                                   class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-brand-500">
                        </div>

                        <div>
                            <span class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2">
                                Kontak Darurat (Penjamin / Keluarga / Kerabat) *
                            </span>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <div>
                                    <input type="text"
                                           name="emergency_contact_name"
                                           value="{{ old('emergency_contact_name') }}"
                                           placeholder="Nama kontak darurat *"
                                           required
                                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                                </div>
                                <div>
                                    <input type="text"
                                           name="emergency_contact_relation"
                                           value="{{ old('emergency_contact_relation') }}"
                                           placeholder="Hubungan (cth: Saudara/Teman) *"
                                           required
                                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                                </div>
                                <div>
                                    <input type="text"
                                           name="emergency_contact_phone"
                                           value="{{ old('emergency_contact_phone') }}"
                                           placeholder="Nomor HP/WA darurat *"
                                           required
                                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                                </div>
                            </div>
                        </div>

                        <!-- Catatan Pengambilan (Opsional) -->
                        <div>
                            <label for="notes" class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                                Catatan Pengambilan (Opsional)
                            </label>
                            <textarea id="notes"
                                      name="notes"
                                      rows="2"
                                      placeholder="Contoh: Rencana ambil pukul 10.00 WIB. Mohon disiapkan tas kamera & baterai cadangan."
                                      class="w-full px-4 py-3 rounded-2xl border border-slate-200 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 resize-none">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- 04 / Surat Perjanjian, Bea Meterai & Tanda Tangan Digital -->
                <div class="bg-white p-6 sm:p-7 rounded-3xl border border-slate-200 shadow-sm space-y-6">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900 font-display">
                            04 / Surat Perjanjian, Bea Meterai & Tanda Tangan Digital
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Setiap transaksi sewa dilindungi Surat Perjanjian Sewa resmi Retronics dengan kekuatan hukum perdata.
                        </p>
                    </div>

                    <!-- BANNER BEA METERAI OTOMATIS (REAKTIF SESUAI NILAI SEWA >= 5 JUTA) -->
                    <div x-show="finalTotal() >= 5000000" x-cloak class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs leading-relaxed flex items-start gap-3">
                        <div class="w-8 h-8 rounded-xl bg-emerald-600 text-white flex-shrink-0 flex items-center justify-center font-bold font-display text-xs">
                            10K
                        </div>
                        <div>
                            <strong class="text-emerald-950 font-bold block mb-0.5">Ketentuan Bea Meterai (UU No. 10 Tahun 2020)</strong>
                            <span>Total sewa transaksi Anda bernilai <strong>≥ Rp5.000.000,-</strong>. Sesuai ketentuan perpajakan, surat perjanjian ini dibubuhi Bea Meterai Elektronik resmi senilai Rp10.000,- (difasilitasi oleh Retronics Store).</span>
                        </div>
                    </div>

                    <!-- RINGKASAN PASAL PERJANJIAN (SCROLLABLE BOX) -->
                    <div class="border border-slate-200 rounded-2xl p-4 bg-slate-50/70 max-h-48 overflow-y-auto text-xs text-slate-600 space-y-3 leading-relaxed">
                        <div class="font-bold text-slate-800 border-b border-slate-200 pb-1 uppercase tracking-wider text-[11px]">
                            Ringkasan Klausul Surat Perjanjian Sewa Retronics
                        </div>
                        <p><strong>1. Kewajiban Pemeliharaan:</strong> Penyewa wajib memperlakukan seluruh gear fotografi dengan hati-hati dan tidak memindahtangankan barang kepada pihak ketiga.</p>
                        <p><strong>2. Batas Waktu & Denda Terlambat:</strong> Waktu pengembalian maksimal pukul 21.00 WIB pada tanggal kembali. Keterlambatan dikenakan denda resmi Retronics sebesar <strong>Rp50.000,- per hari per unit</strong>.</p>
                        <p><strong>3. Kerusakan & Kehilangan:</strong> Kerusakan sensor optik, benturan fisik, atau kehilangan unit menjadi tanggung jawab penuh penyewa untuk penggantian unit sesuai spesifikasi resmi.</p>
                        <p><strong>4. Kekuatan Hukum:</strong> Surat perjanjian ini ditandatangani secara elektronik dan sah mengikat kedua belah pihak di mata hukum Negara Republik Indonesia.</p>
                    </div>

                    <!-- AREA TANDA TANGAN DIGITAL (CANVAS SIGNATURE PAD) -->
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <label class="block text-xs font-bold text-slate-800 uppercase tracking-wider">
                                Tanda Tangan Digital Penyewa *
                            </label>
                            <button type="button"
                                    @click="clearCanvas()"
                                    class="text-xs font-bold text-rose-600 hover:text-rose-700 transition">
                                ↺ Hapus & Tanda Tangani Ulang
                            </button>
                        </div>

                        <!-- Canvas Box -->
                        <div class="border-2 border-dashed border-slate-300 rounded-2xl bg-white p-2 relative overflow-hidden focus-within:border-brand-500">
                            <canvas id="signatureCanvas"
                                    width="600"
                                    height="180"
                                    class="w-full h-44 cursor-crosshair rounded-xl touch-none bg-slate-50/30"
                                    @mousedown="startDrawing($event)"
                                    @mousemove="draw($event)"
                                    @mouseup="stopDrawing()"
                                    @mouseleave="stopDrawing()"
                                    @touchstart.passive="startDrawingTouch($event)"
                                    @touchmove.prevent="drawTouch($event)"
                                    @touchend="stopDrawing()">
                            </canvas>

                            <div class="absolute bottom-3 left-4 pointer-events-none text-[11px] text-slate-400 font-medium" x-show="!hasDrawn">
                                ✍️ Goreskan tanda tangan Anda di sini menggunakan mouse atau jari layar sentuh
                            </div>
                        </div>

                        <!-- Hidden Input untuk Base64 Data Signature -->
                        <input type="hidden" name="signature_data" id="signature_data" x-model="signatureData" required>
                    </div>

                    <!-- Checkbox Persetujuan Hukum -->
                    <div class="pt-2">
                        <label class="flex items-start gap-3 cursor-pointer">
                            <input type="checkbox"
                                   id="agree_terms"
                                   name="agree_terms"
                                   value="1"
                                   x-model="agreedToTerms"
                                   required
                                   class="w-4 h-4 mt-0.5 text-brand-600 rounded border-slate-300 focus:ring-brand-500">
                            <span class="text-xs text-slate-700 leading-relaxed font-medium">
                                Saya menyatakan bahwa seluruh identitas yang diunggah adalah <strong>benar, asli, dan sah</strong>, serta menyetujui seluruh klausul <strong>Surat Perjanjian Sewa Retronics</strong> beserta sanksi denda keterlambatan dan ganti rugi kerusakan.
                            </span>
                        </label>
                    </div>
                </div>

            </div>

            <!-- RIGHT COLUMN: Sticky Summary Card (lg:col-span-5) -->
            <div class="lg:col-span-5 sticky top-24 space-y-4">

                <div class="bg-white p-6 sm:p-7 rounded-3xl border border-slate-200 shadow-sm space-y-5">
                    <h2 class="text-lg font-bold text-slate-900 font-display">
                        Ringkasan pesanan
                    </h2>

                    <!-- Kode Promo Input & Apply Button -->
                    <div>
                        <div class="p-3.5 rounded-2xl border border-slate-200 bg-slate-50/50 focus-within:border-brand-500 focus-within:ring-2 focus-within:ring-brand-100 transition">
                            <label for="promo_code_input" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">
                                Kode promo
                            </label>
                            <input type="text"
                                   id="promo_code_input"
                                   x-model="promoInput"
                                   placeholder="Masukkan voucher (cth: RETRONICS10)"
                                   class="w-full bg-transparent text-xs sm:text-sm font-bold text-slate-900 placeholder:font-normal placeholder:text-slate-400 uppercase tracking-wider focus:outline-none">
                        </div>

                        <div class="flex items-center justify-between mt-2.5">
                            <div class="text-xs">
                                <template x-if="appliedPromo">
                                    <span class="inline-flex items-center gap-1 text-emerald-600 font-bold text-xs">
                                        ✓ Promo digunakan
                                    </span>
                                </template>
                            </div>
                            <button type="button"
                                    @click="applyPromo()"
                                    class="px-4 py-2 rounded-xl text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 border border-slate-200 transition">
                                Gunakan
                            </button>
                        </div>

                        <!-- Active Promo Banner -->
                        <template x-if="appliedPromo">
                            <div class="mt-3 p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-xs text-emerald-800 flex items-center justify-between">
                                <span x-text="appliedPromoText"></span>
                                <button type="button" @click="removePromo()" class="text-emerald-700 hover:text-emerald-900 font-bold text-xs ml-2">
                                    ✕
                                </button>
                            </div>
                        </template>

                        <!-- Error Message if invalid promo -->
                        <template x-if="promoError">
                            <div class="mt-2.5 text-xs text-rose-600 font-semibold" x-text="promoError"></div>
                        </template>

                        <!-- Hidden input for promo code -->
                        <input type="hidden" name="promo_code" :value="appliedPromo ? appliedPromo.code : ''">
                    </div>

                    <!-- Itemized Subtotals List -->
                    <div class="pt-4 border-t border-slate-100 space-y-2.5">
                        <template x-for="item in activeItems()" :key="item.id">
                            <div class="flex items-center justify-between text-xs sm:text-sm text-slate-700">
                                <span class="truncate mr-2" x-text="item.name + ' · ' + item.qty + ' × ' + durationDays + ' hari'"></span>
                                <span class="font-semibold text-slate-900 flex-shrink-0" x-text="formatIDR(item.pricePerDay * item.qty * durationDays)"></span>
                            </div>
                        </template>

                        <div class="pt-3 border-t border-slate-100 space-y-2 text-xs sm:text-sm">
                            <div class="flex items-center justify-between text-slate-600">
                                <span>Subtotal sewa</span>
                                <span class="font-bold text-slate-900" x-text="formatIDR(grossRentalTotal())"></span>
                            </div>

                            <div class="flex items-center justify-between text-emerald-600 font-medium" x-show="durationDiscountAmount() > 0">
                                <span x-text="'Diskon durasi (simulasi ' + (durationDiscountRate() * 100) + '%)'"></span>
                                <span class="font-bold" x-text="'-' + formatIDR(durationDiscountAmount())"></span>
                            </div>

                            <div class="flex items-center justify-between text-emerald-600 font-medium" x-show="promoDiscountAmount() > 0">
                                <span x-text="'Promo ' + (appliedPromo ? appliedPromo.code : '') + ' · ' + (appliedPromo ? appliedPromo.label : '')"></span>
                                <span class="font-bold" x-text="'-' + formatIDR(promoDiscountAmount())"></span>
                            </div>

                            <!-- Ketentuan Bea Meterai Info -->
                            <div class="flex items-center justify-between text-emerald-700 font-medium" x-show="finalTotal() >= 5000000">
                                <span>Bea Meterai UU 10/2020</span>
                                <span class="font-bold">Termasuk (Rp10.000)</span>
                            </div>
                        </div>

                        <!-- Total Akhir -->
                        <div class="pt-4 border-t border-slate-200 flex items-baseline justify-between">
                            <span class="text-sm font-bold text-slate-900">Total akhir</span>
                            <span class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-display tracking-tight"
                                  x-text="formatIDR(finalTotal())">
                            </span>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit"
                            :disabled="!agreedToTerms || selectedUnitsCount() === 0 || !hasDrawn"
                            class="w-full py-4 px-6 rounded-2xl font-bold text-sm text-white bg-brand-600 hover:bg-brand-700 disabled:opacity-50 disabled:cursor-not-allowed shadow-lg shadow-brand-500/25 transition flex items-center justify-center gap-2">
                        <span>Kirim Permintaan & Verifikasi Sewa</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </button>

                    <p class="text-[11px] text-slate-400 text-center">
                        Langkah berikutnya: pilih transfer bank atau QRIS, lalu pantau verifikasi dokumen oleh tim admin.
                    </p>
                </div>

            </div>

        </div>
    </form>
</div>

<script>
    function checkoutPage(config) {
        return {
            durationDays: config.initialDays,
            startDate: config.initialStartDate,
            items: config.items,
            promotions: config.promotions || [],
            promoInput: 'RETRONICS10',
            appliedPromo: null,
            promoError: '',
            agreedToTerms: true,

            // Canvas Signature State
            canvas: null,
            ctx: null,
            isDrawing: false,
            hasDrawn: false,
            signatureData: '',

            init() {
                this.applyPromo();

                this.$nextTick(() => {
                    this.initCanvas();
                });
            },

            initCanvas() {
                this.canvas = document.getElementById('signatureCanvas');
                if (!this.canvas) return;
                this.ctx = this.canvas.getContext('2d');
                this.ctx.lineWidth = 2.5;
                this.ctx.lineCap = 'round';
                this.ctx.lineJoin = 'round';
                this.ctx.strokeStyle = '#0f172a';
            },

            getCanvasPos(clientX, clientY) {
                const rect = this.canvas.getBoundingClientRect();
                const scaleX = this.canvas.width / rect.width;
                const scaleY = this.canvas.height / rect.height;
                return {
                    x: (clientX - rect.left) * scaleX,
                    y: (clientY - rect.top) * scaleY
                };
            },

            startDrawing(e) {
                this.isDrawing = true;
                this.hasDrawn = true;
                const pos = this.getCanvasPos(e.clientX, e.clientY);
                this.ctx.beginPath();
                this.ctx.moveTo(pos.x, pos.y);
            },

            draw(e) {
                if (!this.isDrawing) return;
                const pos = this.getCanvasPos(e.clientX, e.clientY);
                this.ctx.lineTo(pos.x, pos.y);
                this.ctx.stroke();
            },

            startDrawingTouch(e) {
                if (e.touches.length === 0) return;
                this.isDrawing = true;
                this.hasDrawn = true;
                const touch = e.touches[0];
                const pos = this.getCanvasPos(touch.clientX, touch.clientY);
                this.ctx.beginPath();
                this.ctx.moveTo(pos.x, pos.y);
            },

            drawTouch(e) {
                if (!this.isDrawing || e.touches.length === 0) return;
                const touch = e.touches[0];
                const pos = this.getCanvasPos(touch.clientX, touch.clientY);
                this.ctx.lineTo(pos.x, pos.y);
                this.ctx.stroke();
            },

            stopDrawing() {
                if (this.isDrawing) {
                    this.isDrawing = false;
                    this.signatureData = this.canvas.toDataURL('image/png');
                }
            },

            clearCanvas() {
                if (!this.canvas || !this.ctx) return;
                this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
                this.hasDrawn = false;
                this.signatureData = '';
            },

            handleSubmit(e) {
                if (this.hasDrawn && this.canvas) {
                    this.signatureData = this.canvas.toDataURL('image/png');
                }
            },

            activeItems() {
                return this.items.filter(i => i.selected);
            },

            selectedUnitsCount() {
                return this.activeItems().reduce((acc, curr) => acc + curr.qty, 0);
            },

            dailySubtotal() {
                return this.activeItems().reduce((acc, curr) => acc + (curr.pricePerDay * curr.qty), 0);
            },

            grossRentalTotal() {
                return this.dailySubtotal() * this.durationDays;
            },

            durationDiscountRate() {
                if (this.durationDays >= 7) return 0.25;
                if (this.durationDays >= 3) return 0.15;
                if (this.durationDays >= 2) return 0.05;
                return 0.0;
            },

            durationDiscountAmount() {
                return Math.round(this.grossRentalTotal() * this.durationDiscountRate());
            },

            afterDurationTotal() {
                return Math.max(0, this.grossRentalTotal() - this.durationDiscountAmount());
            },

            promoDiscountAmount() {
                if (!this.appliedPromo) return 0;
                const base = this.afterDurationTotal();
                if (this.appliedPromo.type === 'percent') {
                    let disc = Math.round(base * this.appliedPromo.value);
                    if (this.appliedPromo.max && disc > this.appliedPromo.max) {
                        disc = this.appliedPromo.max;
                    }
                    return disc;
                } else if (this.appliedPromo.type === 'fixed') {
                    return Math.min(this.appliedPromo.value, base);
                }
                return 0;
            },

            finalTotal() {
                return Math.max(0, this.afterDurationTotal() - this.promoDiscountAmount());
            },

            get appliedPromoText() {
                if (!this.appliedPromo) return '';
                return '-' + formatIDR(this.promoDiscountAmount()) + ' (Promo: ' + this.appliedPromo.code + ').';
            },

            applyPromo() {
                this.promoError = '';
                const code = (this.promoInput || '').trim().toUpperCase();
                const base = this.afterDurationTotal();

                if (!code) {
                    this.appliedPromo = null;
                    return;
                }

                const found = (this.promotions || []).find(p => p.code === code);
                if (found) {
                    if (base < found.min) {
                        this.promoError = 'Minimum transaksi ' + formatIDR(found.min) + ' untuk menggunakan kode ' + found.code + '.';
                        this.appliedPromo = null;
                        return;
                    }
                    this.appliedPromo = {
                        code: found.code,
                        type: found.type === 'percentage' ? 'percent' : 'fixed',
                        value: found.type === 'percentage' ? (found.value / 100) : found.value,
                        max: found.max,
                        label: found.label,
                        min: found.min
                    };
                    return;
                }

                if (code === 'RETRONICS10') {
                    if (base < 200000) {
                        this.promoError = 'Minimum transaksi Rp200.000 untuk menggunakan kode RETRONICS10.';
                        this.appliedPromo = null;
                        return;
                    }
                    this.appliedPromo = { code: 'RETRONICS10', type: 'percent', value: 0.10, max: null, label: '10%', min: 200000 };
                } else {
                    this.promoError = 'Kode promo tidak valid atau sudah kedaluwarsa.';
                    this.appliedPromo = null;
                }
            },

            removePromo() {
                this.appliedPromo = null;
                this.promoInput = '';
                this.promoError = '';
            },

            calculateReturnDate(startDateStr, days) {
                if (!startDateStr) return '--';
                const d = new Date(startDateStr);
                d.setDate(d.getDate() + parseInt(days));
                const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
                return d.getDate() + ' ' + months[d.getMonth()] + ' ' + d.getFullYear();
            }
        }
    }

    function formatIDR(amount) {
        return 'Rp' + Math.round(amount).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }
</script>
@endsection
