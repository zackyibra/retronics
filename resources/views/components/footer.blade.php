<footer class="bg-[#0b1320] text-slate-300 border-t border-slate-800 mt-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-8 pb-12 border-b border-slate-800/80">
            
            <!-- Col 1: Brand & Identity (2 cols on lg) -->
            <div class="lg:col-span-2 space-y-4">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('images/logo.jpg') }}" alt="Logo Retronics" class="w-10 h-10 object-contain rounded-xl border border-slate-700 bg-white shadow-md">
                    <div>
                        <span class="font-display font-black text-white text-xl block leading-none">retronics</span>
                        <span class="text-[9px] text-slate-400 font-bold uppercase tracking-widest block mt-1">Camera Rental Service</span>
                    </div>
                </div>
                <p class="text-xs text-slate-400 leading-relaxed max-w-sm">
                    Gear siap pakai untuk setiap ide besar. Rental fotografi, videografi, dan studio di Gunung Putri, Kabupaten Bogor, Jawa Barat.
                </p>
                <div class="flex items-center gap-2 pt-1">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-800 text-emerald-400 text-[11px] font-bold border border-slate-700">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        100% Sensor Bersih & Teruji
                    </span>
                </div>
            </div>

            <!-- Col 2: Jelajahi Retronics -->
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-white block mb-4 font-display">Jelajahi Retronics</span>
                <ul class="space-y-2.5 text-xs text-slate-400">
                    <li><a href="{{ route('products.index') }}" class="hover:text-brand-400 transition">Katalog Gear</a></li>
                    <li><a href="{{ url('/promo') }}" class="hover:text-brand-400 transition">Promo Aktif</a></li>
                    <li><a href="{{ route('home') }}#cara-sewa" class="hover:text-brand-400 transition">Alur Sewa</a></li>
                    <li><a href="{{ route('home') }}#kalkulator" class="hover:text-brand-400 transition">Kalkulator Sewa</a></li>
                </ul>
            </div>

            <!-- Col 3: Lokasi Store -->
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-white block mb-4 font-display">Lokasi Retronics</span>
                <div class="space-y-2 text-xs text-slate-400">
                    <p class="leading-relaxed">
                        Gunung Putri, Kabupaten Bogor, Jawa Barat
                    </p>
                    <p class="leading-relaxed">
                        Jam buka: 08.00 – 21.00 WIB
                    </p>
                    <p>
                        WhatsApp: <a href="https://wa.me/6281929513780" target="_blank" class="text-brand-400 hover:underline font-semibold">+62 819-2951-3780</a>
                    </p>
                </div>
            </div>

            <!-- Col 4: Informasi -->
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-white block mb-4 font-display">Informasi</span>
                <ul class="space-y-2.5 text-xs text-slate-400">
                    <li><a href="{{ route('admin.login') }}" class="text-slate-400 hover:text-white transition">Portal Admin</a></li>
                    <li><a href="{{ route('terms') }}" class="text-slate-400 hover:text-white transition">Syarat & Ketentuan</a></li>
                    <li><a href="{{ route('privacy') }}" class="text-slate-400 hover:text-white transition">Kebijakan Privasi</a></li>
                </ul>
            </div>

        </div>

        <!-- Bottom Bar -->
        <div class="pt-8 text-xs text-slate-500 text-center sm:text-left">
            <p>&copy; {{ date('Y') }} Retronics – Rental Fotografi. Semua harga dan transaksi pada desain ini adalah data contoh.</p>
        </div>

    </div>
</footer>
