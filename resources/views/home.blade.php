@extends('layouts.app')

@section('title', 'Sewa Kamera & Peralatan Fotografi Profesional di Gunung Putri, Bogor')
@section('meta_description', 'Sewa kamera DSLR, mirrorless, lensa premium, lighting studio, dan stabilizer di Retronics Gunung Putri, Kabupaten Bogor. Unit siap pakai, sensor dan optik terkalibrasi bersih, tanpa birokrasi berbelit.')

@section('content')

    <!-- ========================================================================= -->
    <!-- 1. HERO SECTION (MATCHING DESIGN SCREENSHOT)                              -->
    <!-- ========================================================================= -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-10 pb-16">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-center">
            
            <!-- Left Headline & CTA -->
            <div class="lg:col-span-7 space-y-6">
                
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-blue-50 text-brand-700 border border-blue-200">
                    <span>RENTAL GEAR PROFESIONAL · GUNUNG PUTRI, BOGOR</span>
                </div>

                <h1 class="text-3xl sm:text-5xl lg:text-5xl font-black text-slate-900 tracking-tight font-display leading-[1.15]">
                    Sewa Kamera &<br>
                    Peralatan Fotografi<br>
                    Profesional di<br>
                    Gunung Putri, Bogor
                </h1>

                <p class="text-sm sm:text-base text-slate-600 leading-relaxed max-w-xl">
                    Ide besar butuh gear yang tepat. Unit siap pakai, sensor dan optik terkalibrasi bersih, tanpa birokrasi berbelit.
                </p>

                <!-- Action Buttons -->
                <div class="flex flex-wrap items-center gap-3 pt-2">
                    <a href="{{ route('products.index') }}"
                       class="px-6 py-3.5 rounded-xl font-bold text-xs sm:text-sm text-white bg-brand-600 hover:bg-brand-700 shadow-md shadow-brand-500/20 transition flex items-center gap-2">
                        <span>Jelajahi Katalog ↗</span>
                    </a>

                    <a href="{{ route('promo') }}"
                       class="px-6 py-3.5 rounded-xl font-bold text-xs sm:text-sm text-slate-800 bg-white hover:bg-slate-50 border border-slate-300 shadow-xs transition">
                        <span>Cek Promo Aktif</span>
                    </a>

                    <a href="#cara-sewa"
                       class="px-5 py-3.5 rounded-xl font-bold text-xs sm:text-sm text-slate-700 bg-slate-100 hover:bg-slate-200 border border-slate-200 transition flex items-center gap-1.5">
                        <span>📋 Lihat Alur Penyewaan</span>
                    </a>

                    <a href="#kalkulator"
                       class="px-5 py-3.5 rounded-xl font-bold text-xs sm:text-sm text-brand-700 bg-blue-50 hover:bg-blue-100 border border-blue-200 transition flex items-center gap-1.5">
                        <span>🧮 Hitung Estimasi Sewa</span>
                    </a>
                </div>

                <div class="flex items-center gap-2 text-xs text-slate-500 font-medium pt-1">
                    <span class="text-emerald-600 font-bold">✓</span>
                    <span>Diperiksa sebelum berangkat. Siap saat kamu berkarya.</span>
                </div>

            </div>

            <!-- Right Hero Card (Camera Photo with Floating Badge) -->
            <div class="lg:col-span-5 relative">
                <div class="relative bg-gradient-to-b from-slate-100 to-slate-200/80 rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-sm overflow-hidden flex items-center justify-center min-h-[360px]">
                    <img src="{{ asset('images/products/canon-eos-r50.png') }}"
                         alt="Canon EOS R50"
                         class="max-h-64 sm:max-h-72 w-auto object-contain drop-shadow-2xl transition duration-500 hover:scale-105"
                         onerror="this.onerror=null; this.src='{{ asset('images/hero-bg.jpg') }}';">

                    <!-- Floating Badge matching design -->
                    <div class="absolute bottom-5 inset-x-5 bg-white/95 backdrop-blur-md rounded-2xl p-4 border border-slate-200/80 shadow-lg flex items-center justify-between">
                        <div>
                            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest block">PILIHAN UNTUK IDE PERTAMAMU</span>
                            <span class="font-bold text-slate-900 text-sm block">Canon EOS R50</span>
                        </div>
                        <div class="text-right">
                            <span class="font-black text-brand-600 text-base font-display">Rp150.000</span>
                            <span class="text-[10px] text-slate-400 block -mt-0.5">per hari</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- ========================================================================= -->
        <!-- STATS HIGHLIGHT STRIP (MATCHING DESIGN)                                   -->
        <!-- ========================================================================= -->
        <div class="mt-14 pt-8 border-t border-slate-200 grid grid-cols-2 md:grid-cols-4 gap-6 items-center">
            <div>
                <p class="text-xs sm:text-sm font-semibold text-slate-800 leading-snug">
                    Standar profesional.<br>
                    Akses yang lebih sederhana.
                </p>
            </div>
            <div>
                <span class="block font-display font-black text-2xl sm:text-3xl text-slate-900">15+</span>
                <span class="text-xs text-slate-500 font-medium">Gear Siap Sewa</span>
            </div>
            <div>
                <span class="block font-display font-black text-2xl sm:text-3xl text-slate-900">100%</span>
                <span class="text-xs text-slate-500 font-medium">Sensor Bersih</span>
            </div>
            <div>
                <span class="block font-display font-black text-2xl sm:text-3xl text-slate-900">4.9★</span>
                <span class="text-xs text-slate-500 font-medium">Rating · contoh tampilan</span>
            </div>
        </div>
    </section>

    <!-- ========================================================================= -->
    <!-- 2. "APA YANG INGIN KAMU CIPTAKAN?" (CATEGORY SELECTOR)                     -->
    <!-- ========================================================================= -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex items-center justify-between mb-5">
            <h2 class="text-lg sm:text-xl font-bold text-slate-900 font-display">
                Apa yang ingin kamu ciptakan?
            </h2>
            <a href="{{ route('products.index') }}" class="text-xs font-semibold text-slate-600 hover:text-brand-600 flex items-center gap-1 transition">
                <span>Lihat semua kategori ↗</span>
            </a>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <a href="{{ route('products.index', ['category' => 'Kamera']) }}"
               class="p-4 rounded-2xl bg-white border border-slate-200/90 shadow-xs hover:border-brand-500 hover:shadow-md transition flex items-center justify-between group">
                <div class="flex items-center gap-2.5">
                    <span class="text-lg">📷</span>
                    <span class="font-bold text-xs sm:text-sm text-slate-800 group-hover:text-brand-600 transition">Kamera</span>
                </div>
                <span class="text-slate-400 group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition-transform text-xs">↗</span>
            </a>

            <a href="{{ route('products.index', ['category' => 'Lensa']) }}"
               class="p-4 rounded-2xl bg-white border border-slate-200/90 shadow-xs hover:border-brand-500 hover:shadow-md transition flex items-center justify-between group">
                <div class="flex items-center gap-2.5">
                    <span class="text-lg">🔍</span>
                    <span class="font-bold text-xs sm:text-sm text-slate-800 group-hover:text-brand-600 transition">Lensa</span>
                </div>
                <span class="text-slate-400 group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition-transform text-xs">↗</span>
            </a>

            <a href="{{ route('products.index', ['category' => 'Pencahayaan']) }}"
               class="p-4 rounded-2xl bg-white border border-slate-200/90 shadow-xs hover:border-brand-500 hover:shadow-md transition flex items-center justify-between group">
                <div class="flex items-center gap-2.5">
                    <span class="text-lg">💡</span>
                    <span class="font-bold text-xs sm:text-sm text-slate-800 group-hover:text-brand-600 transition">Lighting</span>
                </div>
                <span class="text-slate-400 group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition-transform text-xs">↗</span>
            </a>

            <a href="{{ route('products.index', ['category' => 'Aksesori']) }}"
               class="p-4 rounded-2xl bg-white border border-slate-200/90 shadow-xs hover:border-brand-500 hover:shadow-md transition flex items-center justify-between group">
                <div class="flex items-center gap-2.5">
                    <span class="text-lg">🧰</span>
                    <span class="font-bold text-xs sm:text-sm text-slate-800 group-hover:text-brand-600 transition">Aksesori</span>
                </div>
                <span class="text-slate-400 group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition-transform text-xs">↗</span>
            </a>
        </div>
    </section>

    <!-- ========================================================================= -->
    <!-- 3. PROMO BANNER (MATCHING DESIGN BLUE CONTAINER)                          -->
    <!-- ========================================================================= -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="rounded-3xl bg-brand-600 text-white p-6 sm:p-8 flex flex-col md:flex-row items-center justify-between gap-6 shadow-md">
            
            <div class="space-y-2 text-center md:text-left">
                <div class="inline-block px-2.5 py-0.5 rounded-full bg-white/20 text-white text-[10px] font-bold tracking-wider uppercase">
                    PROMO CONTOH · 01 / 03
                </div>
                <h2 class="text-xl sm:text-3xl font-black font-display text-white">
                    Lebih banyak karya. Lebih hemat 10%.
                </h2>
                <p class="text-xs sm:text-sm text-blue-100 font-medium">
                    Min. Rp200.000 · s/d 31 Des 2026 · 14 kuota tersisa
                </p>
            </div>

            <!-- Promo Code Box with Copy Action -->
            <div class="p-3.5 rounded-2xl bg-white/10 border border-dashed border-white/40 flex items-center gap-4">
                <span class="font-mono font-black text-lg sm:text-xl tracking-wider text-white px-2">RETRONICS10</span>
                <button type="button"
                        onclick="copyToClipboard('RETRONICS10', 'Kode RETRONICS10 berhasil disalin!')"
                        class="px-4 py-2 rounded-xl bg-white text-brand-600 hover:bg-blue-50 font-bold text-xs transition shadow-xs">
                    Salin Kode
                </button>
            </div>

        </div>
    </section>

    <!-- ========================================================================= -->
    <!-- 4. ALUR SEWA ("SEDERHANA DARI PILIH SAMPAI BERKARYA")                      -->
    <!-- ========================================================================= -->
    <span id="alur-sewa" class="scroll-mt-24 block -mt-24 pt-24 pointer-events-none"></span>
    <section id="cara-sewa" class="scroll-mt-24 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="flex flex-col sm:flex-row sm:items-baseline justify-between gap-2 mb-10 pb-4 border-b border-slate-200">
            <h2 class="text-2xl sm:text-3xl font-black text-slate-900 font-display">
                Sederhana dari pilih sampai berkarya.
            </h2>
            <span class="text-xs text-slate-500">
                Tidak harus lewat keranjang. Langsung sewa juga bisa.
            </span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            <!-- 01 -->
            <div>
                <span class="font-display font-black text-3xl sm:text-4xl text-brand-600 block mb-2">01</span>
                <h3 class="font-bold text-slate-900 text-sm mb-1.5">Pilih Gear & Unit</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Temukan kamera, lensa, atau lighting sesuai kebutuhan produksi Anda.
                </p>
            </div>

            <!-- 02 -->
            <div>
                <span class="font-display font-black text-3xl sm:text-4xl text-brand-600 block mb-2">02</span>
                <h3 class="font-bold text-slate-900 text-sm mb-1.5">Tentukan Jadwal Sewa</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Atur tanggal mulai dan durasi penggunaan secara fleksibel.
                </p>
            </div>

            <!-- 03 -->
            <div>
                <span class="font-display font-black text-3xl sm:text-4xl text-brand-600 block mb-2">03</span>
                <h3 class="font-bold text-slate-900 text-sm mb-1.5">Bayar & Verifikasi Admin</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Transfer manual, unggah bukti, dan tunggu konfirmasi cepat admin.
                </p>
            </div>

            <!-- 04 -->
            <div>
                <span class="font-display font-black text-3xl sm:text-4xl text-brand-600 block mb-2">04</span>
                <h3 class="font-bold text-slate-900 text-sm mb-1.5">Ambil di Store & Berkarya</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Cek bersama, bawa gear, kembalikan tepat waktu di store kami.
                </p>
            </div>
        </div>
    </section>

    <!-- ========================================================================= -->
    <!-- 5. FEATURED GEAR ("GEAR ANDALAN, SIAP UNTUK IDEMU")                        -->
    <!-- ========================================================================= -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="flex items-center justify-between mb-8">
            <div>
                <span class="text-[10px] font-bold uppercase tracking-wider text-brand-600 block mb-1">PILIHAN KREATOR</span>
                <h2 class="text-2xl sm:text-3xl font-black text-slate-900 font-display">
                    Gear andalan, siap untuk idemu.
                </h2>
            </div>
            <a href="{{ route('products.index') }}" class="text-xs font-bold text-slate-700 hover:text-brand-600 transition flex items-center gap-1">
                <span>Semua Gear ↗</span>
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach ($featuredProducts as $index => $prod)
                <x-product-card :product="$prod" :index="$index" />
            @endforeach
        </div>
    </section>

    <!-- ========================================================================= -->
    <!-- 6. KALKULATOR SEWA ("HITUNG DULU. BERKARYA DENGAN TENANG.")               -->
    <!-- ========================================================================= -->
    <section id="kalkulator" class="scroll-mt-24 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-center">
            
            <!-- Left Info -->
            <div class="lg:col-span-5 space-y-4">
                <span class="text-[10px] font-bold uppercase tracking-wider text-brand-600 block">RENCANAKAN BUDGET</span>
                <h2 class="text-3xl sm:text-4xl font-black text-slate-900 font-display leading-tight">
                    Hitung dulu.<br>
                    Berkarya dengan tenang.
                </h2>
                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                    Transparan sejak awal. Pilih gear dan durasi untuk melihat estimasi sebelum mengajukan pesanan.
                </p>
                <p class="text-[11px] text-slate-400 leading-relaxed pt-2">
                    *Tarif diskon durasi berikut hanya simulasi, belum menjadi ketentuan resmi store.
                </p>
            </div>

            <!-- Right Calculator Card -->
            <div class="lg:col-span-7">
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-sm space-y-5">
                    <h3 class="font-display font-bold text-lg text-slate-900 pb-3 border-b border-slate-100">
                        Kalkulator sewa
                    </h3>

                    <div>
                        <label for="home-calc-gear" class="block text-xs font-bold text-slate-700 uppercase mb-1.5">
                            Gear pilihan
                        </label>
                        <select id="home-calc-gear" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs sm:text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-brand-500">
                            @foreach ($products as $p)
                                <option value="{{ $p->id }}" data-price="{{ (int) $p->price_per_day }}">
                                    {{ $p->name }} · Rp{{ number_format($p->price_per_day, 0, ',', '.') }}/hari
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="home-calc-days" class="text-xs font-bold text-slate-700 uppercase">Durasi sewa</label>
                            <span id="home-calc-days-label" class="px-2.5 py-0.5 rounded-full bg-blue-50 text-brand-700 text-xs font-bold">
                                3 hari
                            </span>
                        </div>
                        <input type="range" id="home-calc-days" min="1" max="14" value="3" class="w-full accent-brand-600 cursor-pointer">
                        <div class="flex justify-between text-[10px] text-slate-400 mt-1">
                            <span>1 hari</span>
                            <span>14 hari</span>
                        </div>
                    </div>

                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 text-xs space-y-2">
                        <div class="flex justify-between text-slate-600">
                            <span id="home-calc-gross-label">Harga kotor · 3 hari:</span>
                            <span id="home-calc-gross-val" class="font-bold text-slate-800">Rp450.000</span>
                        </div>
                        <div class="flex justify-between text-emerald-600 font-semibold" id="home-calc-disc-row">
                            <span id="home-calc-disc-rate">Diskon simulasi 5%:</span>
                            <span id="home-calc-disc-val">-Rp22.500</span>
                        </div>
                        <div class="pt-2 border-t border-slate-200 flex justify-between items-baseline">
                            <span class="font-bold text-slate-700">Estimasi sewa:</span>
                            <span id="home-calc-total-val" class="text-2xl font-black text-slate-900 font-display">Rp427.500</span>
                        </div>
                    </div>

                    @if(auth()->check() && auth()->user()->isAdmin())
                        <div class="p-3 bg-amber-50 border border-amber-200 rounded-xl text-center text-xs font-bold text-amber-800">
                            Simulasi estimasi sewa aktif (Akun Admin hanya digunakan untuk pemantauan/administrasi).
                        </div>
                    @else
                        <button type="button"
                                id="home-calc-btn-sewa"
                                class="w-full py-3.5 px-4 rounded-xl font-bold text-xs sm:text-sm text-white bg-brand-600 hover:bg-brand-700 shadow-md shadow-brand-500/25 transition flex items-center justify-center gap-2">
                            <span>⚡ Sewa Sekarang</span>
                        </button>
                    @endif
                </div>
            </div>

        </div>
    </section>

    <!-- Script Kalkulator Home -->
    <script>
        (function() {
            const gearSelect = document.getElementById('home-calc-gear');
            const daysSlider = document.getElementById('home-calc-days');
            const daysLabel = document.getElementById('home-calc-days-label');
            const grossLabel = document.getElementById('home-calc-gross-label');
            const grossVal = document.getElementById('home-calc-gross-val');
            const discRate = document.getElementById('home-calc-disc-rate');
            const discVal = document.getElementById('home-calc-disc-val');
            const totalVal = document.getElementById('home-calc-total-val');
            const btnSewa = document.getElementById('home-calc-btn-sewa');

            function updateCalc() {
                if (!gearSelect || !daysSlider) return;
                const opt = gearSelect.options[gearSelect.selectedIndex];
                const price = parseInt(opt.getAttribute('data-price') || 0);
                const days = parseInt(daysSlider.value || 1);

                daysLabel.innerText = days + ' hari';
                grossLabel.innerText = 'Harga kotor · ' + days + ' hari';

                let discountPercent = 0;
                if (days >= 7) discountPercent = 0.25;
                else if (days >= 3) discountPercent = 0.15;
                else if (days >= 2) discountPercent = 0.05;

                const gross = price * days;
                const disc = Math.round(gross * discountPercent);
                const net = gross - disc;

                grossVal.innerText = formatIDR(gross);
                discRate.innerText = discountPercent > 0 ? 'Diskon simulasi ' + (discountPercent * 100) + '%:' : 'Diskon durasi:';
                discVal.innerText = disc > 0 ? '-' + formatIDR(disc) : 'Rp0';
                totalVal.innerText = formatIDR(net);
            }

            if (gearSelect && daysSlider) {
                gearSelect.addEventListener('change', updateCalc);
                daysSlider.addEventListener('input', updateCalc);
                updateCalc();
            }

            if (btnSewa && gearSelect && daysSlider) {
                btnSewa.addEventListener('click', function() {
                    const prodId = gearSelect.value;
                    const days = daysSlider.value;
                    window.location.href = "{{ route('rentals.create') }}?product_id=" + prodId + "&days=" + days;
                });
            }
        })();
    </script>

    <!-- ========================================================================= -->
    <!-- 7. STANDAR KUALITAS ("FOKUS PADA KARYA. SISANYA, KAMI SIAPKAN.")           -->
    <!-- ========================================================================= -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <h2 class="text-2xl sm:text-3xl font-black text-slate-900 font-display mb-8">
            Fokus pada karya. Sisanya, kami siapkan.
        </h2>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
            <!-- Card 1 -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xs space-y-3">
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-brand-600 flex items-center justify-center text-xl">
                    🎯
                </div>
                <h3 class="font-bold text-slate-900 text-base">Sensor Bersih</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Sensor dan optik terkalibrasi bersih. Unit diuji sebelum diserahkan kepada penyewa.
                </p>
            </div>

            <!-- Card 2 -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xs space-y-3">
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-brand-600 flex items-center justify-center text-xl">
                    📋
                </div>
                <h3 class="font-bold text-slate-900 text-base">Syarat Transparan</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Harga, kelengkapan, dan estimasi biaya terlihat sejak awal tanpa biaya siluman.
                </p>
            </div>

            <!-- Card 3 -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xs space-y-3">
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-brand-600 flex items-center justify-center text-xl">
                    🕒
                </div>
                <h3 class="font-bold text-slate-900 text-base">Tepat Waktu</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Status persiapan jelas agar jadwal produksimu tetap terjaga dengan lancar.
                </p>
            </div>
        </div>
    </section>

    <!-- ========================================================================= -->
    <!-- 8. STORE / PICKUP SECTION ("AMBIL GEAR. KENALI UNITMU.")                  -->
    <!-- ========================================================================= -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="bg-white rounded-3xl p-6 sm:p-10 border border-slate-200/90 shadow-sm grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
            
            <div class="lg:col-span-5 rounded-2xl overflow-hidden bg-slate-100 border border-slate-200 flex items-center justify-center min-h-[260px]">
                <img src="{{ asset('images/products/hard-case-kamera.png') }}"
                     alt="Store Retronics"
                     class="max-h-56 w-auto object-contain drop-shadow"
                     onerror="this.onerror=null; this.src='{{ asset('images/hero-bg.jpg') }}';">
            </div>

            <div class="lg:col-span-7 space-y-4">
                <span class="text-[10px] font-bold uppercase tracking-wider text-brand-600 block">BERBASIS DI GUNUNG PUTRI, BOGOR</span>
                <h2 class="text-2xl sm:text-3xl font-black text-slate-900 font-display">
                    Ambil gear. Kenali unitmu. Mulai berkarya.
                </h2>
                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                    Pengambilan dan pengembalian dilakukan di store. Tim kami membantu pengecekan unit dan kelengkapan bersama sebelum kamu berangkat.
                </p>
                <p class="text-xs text-slate-500">
                    Alamat store: Gunung Putri, Kabupaten Bogor · Jam buka: 08.00 - 21.00 WIB · WhatsApp: +62 819-2951-3780
                </p>
                <div class="pt-2">
                    <a href="https://wa.me/6281929513780?text=Halo%20Admin%20Retronics%2C%20saya%20ingin%20tanya%20lokasi%20store"
                       target="_blank"
                       class="inline-flex px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold transition">
                        Panduan Pengambilan ↗
                    </a>
                </div>
            </div>

        </div>
    </section>

    <!-- ========================================================================= -->
    <!-- 9. FAQ ACCORDION ("HAL KECIL YANG PENTING SEBELUM SEWA.")                  -->
    <!-- ========================================================================= -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <h2 class="text-2xl sm:text-3xl font-black text-slate-900 font-display mb-8">
            Hal kecil yang penting sebelum sewa.
        </h2>

        <div class="space-y-4 max-w-4xl">
            <div class="p-5 rounded-2xl bg-white border border-slate-200/90 shadow-xs">
                <h3 class="font-bold text-slate-900 text-sm mb-1.5">Bisa langsung sewa tanpa keranjang?</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Bisa. Pilih tombol <strong>Sewa Sekarang</strong> dari kartu katalog atau halaman detail gear untuk langsung mengatur jadwal booking tanpa harus membuka keranjang.
                </p>
            </div>

            <div class="p-5 rounded-2xl bg-white border border-slate-200/90 shadow-xs">
                <h3 class="font-bold text-slate-900 text-sm mb-1.5">Bagaimana cara pembayaran?</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Transfer manual melalui BCA, Mandiri, atau QRIS. Unggah bukti pembayaran, lalu admin kami segera memverifikasi pesanan Anda.
                </p>
            </div>

            <div class="p-5 rounded-2xl bg-white border border-slate-200/90 shadow-xs">
                <h3 class="font-bold text-slate-900 text-sm mb-1.5">Bagaimana jika terlambat mengembalikan?</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Denda keterlambatan dihitung sebesar <strong>Rp50.000 per hari per unit</strong>. Pelunasan denda dilakukan secara transparan saat pengembalian barang ke store.
                </p>
            </div>
        </div>
    </section>

    <!-- ========================================================================= -->
    <!-- 10. PRE-FOOTER BANNER                                                     -->
    <!-- ========================================================================= -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="rounded-3xl bg-blue-50/70 border border-blue-200 p-8 flex flex-col sm:flex-row items-center justify-between gap-6">
            <div>
                <h2 class="text-xl sm:text-2xl font-black text-slate-900 font-display">
                    Ide berikutnya layak mendapatkan gear terbaik.
                </h2>
                <p class="text-xs sm:text-sm text-slate-600 mt-1">
                    Mulai dari kamera favoritmu. Atur jadwalnya hari ini.
                </p>
            </div>
            <a href="{{ route('products.index') }}"
               class="px-6 py-3.5 rounded-xl font-bold text-xs sm:text-sm text-white bg-brand-600 hover:bg-brand-700 shadow-md shadow-brand-500/20 transition flex items-center gap-1.5 shrink-0">
                <span>Jelajahi Katalog ↗</span>
            </a>
        </div>
    </section>

@endsection
