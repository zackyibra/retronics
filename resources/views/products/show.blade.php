@extends('layouts.app')

@section('title', $product->name . ' | Rental Kamera Retronics')
@section('meta_description', 'Sewa ' . $product->name . ' di Retronics Gunung Putri, Bogor. Kondisi ' . $product->condition . ', harga Rp' . number_format($product->price_per_day, 0, ',', '.') . '/hari. Sensor bersih, terkalibrasi, dan siap pakai.')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full flex-grow pb-24 lg:pb-12">

    <!-- Breadcrumb -->
    <x-breadcrumb :items="[
        ['label' => 'Katalog Gear', 'url' => route('products.index')],
        ['label' => $product->category->name ?? 'Kategori', 'url' => route('products.index', ['category' => $product->category->name ?? ''])],
        ['label' => $product->name, 'url' => '']
    ]" />

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

        <!-- ================================================================= -->
        <!-- LEFT COLUMN: Product Gallery & Specs (7 Cols)                     -->
        <!-- ================================================================= -->
        <div class="lg:col-span-7 space-y-6">
            
            <!-- Main Image Card -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-sm relative overflow-hidden">
                
                <!-- Category & Stock Badges -->
                <div class="flex items-center justify-between gap-2 mb-4">
                    <span class="text-xs font-bold px-3 py-1 rounded-full bg-blue-50 text-brand-700 border border-blue-200/80">
                        {{ $product->category->name ?? 'Gear' }}
                    </span>

                    @if ($product->stock > 0)
                        <span class="text-xs font-bold px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span>Unit Siap Sewa ({{ $product->stock }} Unit)</span>
                        </span>
                    @else
                        <span class="text-xs font-bold px-3 py-1 rounded-full bg-rose-50 text-rose-700 border border-rose-200">
                            Unit Sedang Disewa
                        </span>
                    @endif
                </div>

                <!-- Product Image Container -->
                <div class="bg-slate-50 rounded-2xl p-8 h-80 sm:h-96 flex items-center justify-center relative overflow-hidden border border-slate-100">
                    @if ($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}"
                             alt="{{ $product->name }}"
                             class="max-h-72 max-w-full object-contain drop-shadow-xl transition-transform duration-300 hover:scale-105"
                             onerror="this.onerror=null; this.src='{{ asset('images/products/' . $product->image) }}';">
                    @else
                        <div class="text-8xl">📷</div>
                    @endif
                </div>

                <!-- Trust Guarantee Bar -->
                <div class="grid grid-cols-3 gap-3 mt-6 pt-6 border-t border-slate-100 text-center">
                    <div class="p-3 bg-slate-50 rounded-2xl border border-slate-100">
                        <span class="text-base block mb-0.5">✨</span>
                        <span class="text-[10px] text-slate-400 block font-bold uppercase tracking-wider">Kondisi Fisik</span>
                        <span class="text-xs font-bold text-slate-800 capitalize">{{ $product->condition }}</span>
                    </div>
                    <div class="p-3 bg-slate-50 rounded-2xl border border-slate-100">
                        <span class="text-base block mb-0.5">🔍</span>
                        <span class="text-[10px] text-slate-400 block font-bold uppercase tracking-wider">Optik & Sensor</span>
                        <span class="text-xs font-bold text-emerald-600">100% Bersih</span>
                    </div>
                    <div class="p-3 bg-slate-50 rounded-2xl border border-slate-100">
                        <span class="text-base block mb-0.5">🛡️</span>
                        <span class="text-[10px] text-slate-400 block font-bold uppercase tracking-wider">Jaminan Sewa</span>
                        <span class="text-xs font-bold text-slate-800">Garansi Unit</span>
                    </div>
                </div>

            </div>

            <!-- Description & Included Equipment Box -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-sm space-y-6">
                <div>
                    <h2 class="text-lg font-bold text-slate-900 font-display mb-2">Deskripsi Produk</h2>
                    <p class="text-slate-600 text-xs sm:text-sm leading-relaxed">
                        {{ $product->description ?? 'Peralatan fotografi dan produksi original dengan standar perawatan tinggi di Retronics Gunung Putri, Bogor. Sensor terkalibrasi bersih, fungsi autofokus akurat, dan baterai terisi penuh siap pakai untuk pemotretan komersial maupun personal.' }}
                    </p>
                </div>

                <div class="pt-6 border-t border-slate-100">
                    <h3 class="text-sm font-bold text-slate-900 mb-3 flex items-center gap-2">
                        <span>📦</span>
                        <span>Kelengkapan Bawaan Sewa (Include):</span>
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs text-slate-600">
                        <div class="flex items-center gap-2 p-2.5 rounded-xl bg-slate-50">
                            <span class="text-emerald-600 font-bold">✓</span>
                            <span>1x Bodi/Unit Utama Kamera</span>
                        </div>
                        <div class="flex items-center gap-2 p-2.5 rounded-xl bg-slate-50">
                            <span class="text-emerald-600 font-bold">✓</span>
                            <span>2x Baterai Original Full Charge</span>
                        </div>
                        <div class="flex items-center gap-2 p-2.5 rounded-xl bg-slate-50">
                            <span class="text-emerald-600 font-bold">✓</span>
                            <span>1x Charger Unit & Kabel Daya</span>
                        </div>
                        <div class="flex items-center gap-2 p-2.5 rounded-xl bg-slate-50">
                            <span class="text-emerald-600 font-bold">✓</span>
                            <span>1x Tas Kamera & Strap Original</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Customer Reviews Section -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-sm min-w-0 overflow-hidden" id="review-section">
                <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-100">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900 font-display flex items-center gap-2">
                            <span>⭐</span>
                            <span>Ulasan Penyewa</span>
                        </h2>
                        <span class="text-xs text-slate-500">
                            {{ $reviews->count() }} ulasan terverifikasi
                        </span>
                    </div>

                    @if ($reviews->count() > 0)
                        <div class="text-right">
                            <span class="text-2xl font-black text-amber-500 font-display">
                                {{ number_format($averageRating, 1) }}
                            </span>
                            <span class="text-xs text-slate-400">/ 5.0</span>
                        </div>
                    @endif
                </div>

                <!-- Review Submission Form (if eligible) -->
                @auth
                    @if ($canReview)
                        <div class="bg-blue-50/70 border border-blue-200/80 rounded-2xl p-5 mb-6"
                             x-data="{
                                 rating: {{ old('rating', 5) }},
                                 hoverRating: 0,
                                 labels: {
                                     1: '★ 1/5 - Sangat Kurang',
                                     2: '★ 2/5 - Kurang Memuaskan',
                                     3: '★ 3/5 - Cukup Baik',
                                     4: '★ 4/5 - Sangat Bagus',
                                     5: '★ 5/5 - Luar Biasa (Bintang 5)!'
                                 }
                             }">
                            <h3 class="font-bold text-slate-900 text-xs uppercase tracking-wider mb-3">✍️ Tulis Pengalaman Sewa Anda</h3>
                            <form method="POST" action="{{ route('reviews.store', $product->id) }}">
                                @csrf
                                <div class="mb-3">
                                    <label class="block text-xs font-bold text-slate-700 mb-1.5">Rating Bintang: <span class="text-rose-500">*</span></label>
                                    <div class="flex items-center gap-1.5 flex-wrap" @mouseleave="hoverRating = 0">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <label class="cursor-pointer transition-transform hover:scale-125 active:scale-95"
                                                   @mouseenter="hoverRating = {{ $i }}"
                                                   @click="rating = {{ $i }}">
                                                <input type="radio"
                                                       name="rating"
                                                       value="{{ $i }}"
                                                       x-model.number="rating"
                                                       class="sr-only"
                                                       required>
                                                <span class="text-3xl transition-colors select-none"
                                                      :class="(hoverRating || rating) >= {{ $i }} ? 'text-amber-400' : 'text-slate-300'">
                                                    ★
                                                </span>
                                            </label>
                                        @endfor
                                        <span class="text-xs font-semibold px-2.5 py-1 rounded-lg bg-amber-50 text-amber-800 border border-amber-200/60 ml-2"
                                              x-text="(hoverRating || rating) ? labels[hoverRating || rating] : 'Pilih bintang penilaian'"></span>
                                    </div>
                                    @error('rating')
                                        <p class="text-rose-600 text-[11px] mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <textarea name="comment"
                                              rows="3"
                                              placeholder="Bagaimana kondisi gear dan layanan selama penyewaan?..."
                                              required
                                              class="w-full px-3.5 py-2.5 rounded-xl bg-white border border-slate-200 text-xs focus:ring-2 focus:ring-brand-500 focus:outline-none"></textarea>
                                    @error('comment')
                                        <p class="text-rose-600 text-[11px] mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <button type="submit" class="px-5 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs transition shadow-sm">
                                    Kirim Ulasan
                                </button>
                            </form>
                        </div>
                    @elseif ($hasReviewed)
                        <div class="p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold mb-6 flex items-center gap-2">
                            <span>✓</span>
                            <span>Terima kasih! Anda telah memberikan ulasan untuk peralatan ini.</span>
                        </div>
                    @endif
                @endauth

                <!-- Reviews List -->
                @if ($reviews->isEmpty())
                    <div class="text-center py-8 text-slate-400">
                        <div class="text-3xl mb-2">💬</div>
                        <p class="text-xs">Belum ada ulasan untuk perlengkapan ini. Jadilah yang pertama menyewa!</p>
                    </div>
                @else
                    <div class="space-y-3.5">
                        @foreach ($reviews as $rev)
                            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 min-w-0 overflow-hidden">
                                <div class="flex items-center justify-between mb-1.5 gap-2">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <div class="w-6 h-6 shrink-0 rounded-full bg-brand-100 text-brand-700 font-bold text-[10px] flex items-center justify-center">
                                            {{ substr($rev->user->name ?? 'U', 0, 1) }}
                                        </div>
                                        <span class="text-xs font-bold text-slate-800 truncate">{{ $rev->user->name ?? 'Penyewa' }}</span>
                                    </div>
                                    <div class="flex items-center text-amber-400 text-xs shrink-0">
                                        @for ($s = 1; $s <= 5; $s++)
                                            <span>{{ $s <= $rev->rating ? '★' : '☆' }}</span>
                                        @endfor
                                    </div>
                                </div>
                                <p class="text-xs text-slate-600 leading-relaxed break-words break-all whitespace-pre-line overflow-hidden">{{ $rev->comment }}</p>
                                <span class="text-[10px] text-slate-400 block mt-1">{{ $rev->created_at->diffForHumans() }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>

        <!-- ================================================================= -->
        <!-- RIGHT COLUMN: Booking & Rental Calculator Card (5 Cols)           -->
        <!-- ================================================================= -->
        <div class="lg:col-span-5 space-y-6">
            
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-sm lg:sticky lg:top-28">
                
                <!-- Wishlist & Category Header -->
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
                    <span class="text-[10px] uppercase font-bold tracking-wider text-slate-400">
                        {{ $product->category->name }}
                    </span>

                    @auth
                        <form method="POST" action="{{ route('wishlist.toggle', $product->id) }}">
                            @csrf
                            <button type="submit" class="flex items-center gap-1.5 text-xs font-semibold transition {{ $isWishlisted ? 'text-rose-600' : 'text-slate-400 hover:text-rose-600' }}">
                                <svg class="w-4 h-4 {{ $isWishlisted ? 'fill-current' : 'fill-none' }}" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                </svg>
                                <span>{{ $isWishlisted ? 'Favorit' : 'Simpan' }}</span>
                            </button>
                        </form>
                    @endauth
                </div>

                <!-- Title & Price per Day -->
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-display mb-2 leading-tight">
                    {{ $product->name }}
                </h1>

                <div class="mb-6 flex items-baseline gap-2">
                    <span class="text-3xl font-black text-brand-600 font-display">
                        Rp{{ number_format($product->price_per_day, 0, ',', '.') }}
                    </span>
                    <span class="text-xs text-slate-400 font-medium">/ 24 Jam</span>
                </div>

                <!-- Booking Options Form -->
                <div class="space-y-4">
                    
                    <!-- Date Picker -->
                    <div>
                        <label for="detail_start_date" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Tanggal Mulai Sewa
                        </label>
                        <input type="date"
                               id="detail_start_date"
                               value="{{ date('Y-m-d') }}"
                               min="{{ date('Y-m-d') }}"
                               class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 font-medium">
                    </div>

                    <!-- Duration Selector -->
                    <div>
                        <label for="detail_days" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Pilih Durasi Sewa
                        </label>
                        <select id="detail_days"
                                class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 font-medium">
                            <option value="1">1 Hari (Tarif Standar)</option>
                            <option value="2">2 Hari (Hemat 5%)</option>
                            <option value="3">3 Hari (Hemat 15% - Paling Diminati)</option>
                            <option value="7">7 Hari (Hemat 25% - Paket Mingguan)</option>
                        </select>
                    </div>

                    <!-- Live Cost Simulation Box -->
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/90 text-xs space-y-2">
                        <div class="flex justify-between text-slate-500">
                            <span>Subtotal Durasi:</span>
                            <span class="font-bold text-slate-800" id="detail_subtotal_calc">Rp0</span>
                        </div>
                        <div class="flex justify-between text-emerald-600 font-semibold" id="detail_discount_row">
                            <span>Diskon Durasi:</span>
                            <span id="detail_discount_calc">-Rp0</span>
                        </div>
                        <div class="pt-2 border-t border-slate-200 flex justify-between items-baseline">
                            <span class="font-bold text-slate-700">Estimasi Total:</span>
                            <span class="text-xl font-black text-brand-600 font-display" id="detail_total_calc">Rp0</span>
                        </div>
                    </div>

                    @if (auth()->check() && auth()->user()->isAdmin())
                        <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 space-y-2 mt-2">
                            <div class="flex items-center gap-2 font-bold text-xs">
                                <span>👑 Akun Administrator</span>
                            </div>
                            <p class="text-[11px] leading-relaxed text-amber-800">
                                Anda sedang membuka halaman ini sebagai Administrator. Akun administrator hanya digunakan untuk pengelolaan operasional, bukan untuk melakukan penyewaan barang.
                            </p>
                            <div class="pt-1">
                                <a href="{{ route('admin.products.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs transition shadow-xs">
                                    <span>Kelola Inventaris Barang di Panel Admin ↗</span>
                                </a>
                            </div>
                        </div>
                    @elseif ($product->stock > 0)
                        <!-- DUAL ACTIONS: DIRECT BOOKING & ADD TO CART -->
                        <div class="space-y-2.5 pt-2">
                            
                            <!-- 1. Direct Booking Button (⚡ Sewa Sekarang) -->
                            <button type="button"
                                    id="btn-direct-booking"
                                    class="w-full py-3.5 px-4 rounded-xl font-bold text-sm text-white bg-brand-600 hover:bg-brand-700 shadow-lg shadow-brand-500/25 transition flex items-center justify-center gap-2">
                                <span>⚡ Sewa Sekarang (Direct Checkout)</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </button>

                            <!-- 2. Add To Cart Button -->
                            <form method="POST" action="{{ route('cart.add') }}" id="form-add-to-cart">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <input type="hidden" name="quantity" value="1">
                                <input type="hidden" name="duration_days" id="cart_duration_input" value="1">
                                <button type="submit"
                                        class="w-full py-3 px-4 rounded-xl font-bold text-xs sm:text-sm text-slate-800 bg-slate-100 hover:bg-slate-200 border border-slate-200 transition flex items-center justify-center gap-2">
                                    <span>🛒 Masukkan ke Keranjang Sewa</span>
                                </button>
                            </form>

                        </div>
                    @else
                        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-center text-xs font-bold">
                            Unit ini sedang tersewa. Silakan hubungi CS untuk cek jadwal ketersediaan.
                        </div>
                    @endif

                    <!-- WhatsApp Hotline Option -->
                    <div class="pt-4 border-t border-slate-100 text-center">
                        <a href="https://wa.me/6281929513780?text=Halo%20Admin%20Retronics%2C%20saya%20ingin%20tanya%20seputar%20sewa%20{{ urlencode($product->name) }}"
                           target="_blank"
                           class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 flex items-center justify-center gap-1.5 transition">
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.711 2.598 2.669-.699c.969.586 1.838.88 2.791.88h.005c3.18 0 5.767-2.586 5.768-5.766 0-3.18-2.587-5.766-5.773-5.766zm8.814 5.765c0 4.87-3.953 8.823-8.82 8.823-1.503 0-2.915-.382-4.15-1.047l-4.575 1.199 1.226-4.464c-.75-1.309-1.196-2.825-1.196-4.511 0-4.87 3.953-8.824 8.82-8.824 4.869 0 8.82 3.954 8.82 8.824z"/></svg>
                            <span>Tanya CS WhatsApp Retronics</span>
                        </a>
                    </div>

                </div>

            </div>

        </div>

    </div>

    <!-- Related Products Showcase -->
    @if ($relatedProducts->isNotEmpty())
        <div class="mt-16 pt-12 border-t border-slate-200">
            <h2 class="text-xl sm:text-2xl font-black text-slate-900 font-display mb-6">
                Rekomendasi Gear Terkait
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach ($relatedProducts as $relIndex => $relProd)
                    <x-product-card :product="$relProd" :index="$relIndex" />
                @endforeach
            </div>
        </div>
    @endif

</div>

<!-- ========================================================================= -->
<!-- STICKY BOTTOM ACTION BAR (MOBILE ONLY — MATCHING DESIGN SCREEN ROW 6)      -->
<!-- ========================================================================= -->
@if ($product->stock > 0 && !(auth()->check() && auth()->user()->isAdmin()))
    <div class="fixed bottom-0 inset-x-0 z-40 bg-white/95 backdrop-blur-md border-t border-slate-200 p-3 sm:hidden shadow-2xl flex items-center justify-between gap-3">
        <div>
            <span class="text-[10px] text-slate-400 block font-medium">Tarif Sewa Harian</span>
            <span class="text-base font-black text-brand-600 font-display">
                Rp{{ number_format($product->price_per_day, 0, ',', '.') }}
            </span>
        </div>

        <button type="button"
                onclick="document.getElementById('btn-direct-booking')?.click()"
                class="px-5 py-2.5 rounded-xl font-bold text-xs text-white bg-brand-600 hover:bg-brand-700 shadow-md shadow-brand-500/30 transition flex items-center gap-1.5">
            <span>⚡ Sewa Sekarang</span>
        </button>
    </div>
@endif

<!-- Client-side Calculation Script -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const pricePerDay = {{ (int) $product->price_per_day }};
        const daysSelect = document.getElementById('detail_days');
        const startDateInput = document.getElementById('detail_start_date');
        const subtotalCalc = document.getElementById('detail_subtotal_calc');
        const discountCalc = document.getElementById('detail_discount_calc');
        const totalCalc = document.getElementById('detail_total_calc');
        const cartDurationInput = document.getElementById('cart_duration_input');
        const btnDirectBooking = document.getElementById('btn-direct-booking');

        function recalculate() {
            if (!daysSelect) return;
            const days = parseInt(daysSelect.value) || 1;
            if (cartDurationInput) cartDurationInput.value = days;

            let discountRate = 0;
            if (days >= 7) discountRate = 0.25;
            else if (days >= 3) discountRate = 0.15;
            else if (days >= 2) discountRate = 0.05;

            const gross = pricePerDay * days;
            const discount = Math.round(gross * discountRate);
            const net = gross - discount;

            if (subtotalCalc) subtotalCalc.innerText = formatIDR(gross);
            if (discountCalc) discountCalc.innerText = discount > 0 ? `-${formatIDR(discount)} (${discountRate * 100}%)` : 'Rp0';
            if (totalCalc) totalCalc.innerText = formatIDR(net);
        }

        if (daysSelect) {
            daysSelect.addEventListener('change', recalculate);
            recalculate();
        }

        if (btnDirectBooking && startDateInput && daysSelect) {
            btnDirectBooking.addEventListener('click', function () {
                const sDate = startDateInput.value;
                const days = daysSelect.value;
                const targetUrl = "{{ route('rentals.create') }}?product_id={{ $product->id }}&start_date=" + sDate + "&days=" + days;
                window.location.href = targetUrl;
            });
        }
    });
</script>

@endsection
