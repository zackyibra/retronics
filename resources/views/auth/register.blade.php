@extends('layouts.app')

@section('title', 'Daftar Akun Penyewa | Retronics – Rental Fotografi')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-14">

    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-xs text-slate-500 mb-8">
        <a href="{{ route('home') }}" class="hover:text-brand-600 transition">Beranda</a>
        <span class="text-slate-300">›</span>
        <span class="text-slate-800 font-semibold">Daftar</span>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">

        <!-- LEFT COLUMN: Gear Setup Photo & Value Props (lg:col-span-6) -->
        <div class="lg:col-span-6 space-y-6">
            <div class="relative rounded-3xl overflow-hidden bg-slate-100 border border-slate-200/80 shadow-md aspect-[4/3]">
                <img src="https://images.unsplash.com/photo-1502920917128-1aa500764cbd?auto=format&fit=crop&w=1200&q=80"
                     alt="Retronics Studio Gear"
                     class="w-full h-full object-cover">
            </div>

            <div class="space-y-4">
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 font-display tracking-tight">
                    Satu akun untuk setiap rencana produksi.
                </h2>

                <ul class="space-y-2.5 text-xs sm:text-sm font-semibold text-slate-700">
                    <li class="flex items-center gap-2.5">
                        <span class="w-5 h-5 rounded-full bg-brand-50 text-brand-600 flex items-center justify-center font-bold text-xs flex-shrink-0">✓</span>
                        <span>Booking gear dan gunakan promo</span>
                    </li>
                    <li class="flex items-center gap-2.5">
                        <span class="w-5 h-5 rounded-full bg-brand-50 text-brand-600 flex items-center justify-center font-bold text-xs flex-shrink-0">✓</span>
                        <span>Unggah bukti transfer manual</span>
                    </li>
                    <li class="flex items-center gap-2.5">
                        <span class="w-5 h-5 rounded-full bg-brand-50 text-brand-600 flex items-center justify-center font-bold text-xs flex-shrink-0">✓</span>
                        <span>Pantau jadwal dan pengembalian</span>
                    </li>
                    <li class="flex items-center gap-2.5">
                        <span class="w-5 h-5 rounded-full bg-brand-50 text-brand-600 flex items-center justify-center font-bold text-xs flex-shrink-0">✓</span>
                        <span>Simpan wishlist dan beri ulasan</span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- RIGHT COLUMN: Registration Form (lg:col-span-6) -->
        <div class="lg:col-span-6 max-w-lg mx-auto w-full space-y-6">

            <div class="flex items-center gap-3">
                <img src="{{ asset('images/logo.jpg') }}" alt="Logo Retronics" class="w-12 h-12 object-contain rounded-2xl border border-slate-200 bg-white shadow-xs">
                <div>
                    <span class="font-display font-black text-xl text-slate-900 block leading-none">retronics</span>
                    <span class="text-[9px] uppercase tracking-widest font-bold text-slate-500 block mt-1">Camera Rental Service</span>
                </div>
            </div>

            <div>
                <span class="inline-block px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider text-brand-600 bg-brand-50 border border-brand-100">
                    Mulai berkarya bersama Retronics
                </span>
                <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 font-display tracking-tight mt-3">
                    Buat akun penyewa
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-2">
                    Sudah terdaftar?
                    <a href="{{ route('login') }}" class="font-bold text-brand-600 hover:text-brand-700 underline underline-offset-2">
                        Masuk ke akunmu ↗
                    </a>
                </p>
            </div>

            @if ($errors->any())
                <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-xs sm:text-sm">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" class="space-y-4">
                @csrf

                <!-- Nama Lengkap -->
                <div class="p-3.5 rounded-2xl border border-slate-200 bg-slate-50/50 focus-within:border-brand-500 focus-within:bg-white focus-within:ring-2 focus-within:ring-brand-100 transition">
                    <label for="name" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">
                        Nama lengkap *
                    </label>
                    <input type="text"
                           id="name"
                           name="name"
                           value="{{ old('name') }}"
                           placeholder="Nama sesuai identitas"
                           required
                           autofocus
                           class="w-full bg-transparent text-sm font-semibold text-slate-900 placeholder:text-slate-400 focus:outline-none">
                </div>

                <!-- Email & WhatsApp Row -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="p-3.5 rounded-2xl border border-slate-200 bg-slate-50/50 focus-within:border-brand-500 focus-within:bg-white focus-within:ring-2 focus-within:ring-brand-100 transition">
                        <label for="email" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">
                            Email *
                        </label>
                        <input type="email"
                               id="email"
                               name="email"
                               value="{{ old('email') }}"
                               placeholder="nama@email.com"
                               required
                               class="w-full bg-transparent text-sm font-semibold text-slate-900 placeholder:text-slate-400 focus:outline-none">
                    </div>

                    <div class="p-3.5 rounded-2xl border border-slate-200 bg-slate-50/50 focus-within:border-brand-500 focus-within:bg-white focus-within:ring-2 focus-within:ring-brand-100 transition">
                        <label for="phone" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">
                            WhatsApp aktif *
                        </label>
                        <input type="text"
                               id="phone"
                               name="phone"
                               value="{{ old('phone') }}"
                               placeholder="Nomor yang dapat dihubungi"
                               required
                               class="w-full bg-transparent text-sm font-semibold text-slate-900 placeholder:text-slate-400 focus:outline-none">
                    </div>
                </div>

                <!-- Domisili -->
                <div class="p-3.5 rounded-2xl border border-slate-200 bg-slate-50/50 focus-within:border-brand-500 focus-within:bg-white focus-within:ring-2 focus-within:ring-brand-100 transition">
                    <label for="address" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">
                        Domisili *
                    </label>
                    <input type="text"
                           id="address"
                           name="address"
                           value="{{ old('address', 'Gunung Putri, Bogor') }}"
                           placeholder="Kota / wilayah tempat tinggal"
                           required
                           class="w-full bg-transparent text-sm font-semibold text-slate-900 placeholder:text-slate-400 focus:outline-none">
                </div>

                <!-- Password Row -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="p-3.5 rounded-2xl border border-slate-200 bg-slate-50/50 focus-within:border-brand-500 focus-within:bg-white focus-within:ring-2 focus-within:ring-brand-100 transition">
                        <label for="password" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">
                            Password *
                        </label>
                        <input type="password"
                               id="password"
                               name="password"
                               placeholder="Minimal 8 karakter"
                               required
                               class="w-full bg-transparent text-sm font-semibold text-slate-900 placeholder:text-slate-400 focus:outline-none">
                    </div>

                    <div class="p-3.5 rounded-2xl border border-slate-200 bg-slate-50/50 focus-within:border-brand-500 focus-within:bg-white focus-within:ring-2 focus-within:ring-brand-100 transition">
                        <label for="password_confirmation" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">
                            Konfirmasi password *
                        </label>
                        <input type="password"
                               id="password_confirmation"
                               name="password_confirmation"
                               placeholder="Ulangi password"
                               required
                               class="w-full bg-transparent text-sm font-semibold text-slate-900 placeholder:text-slate-400 focus:outline-none">
                    </div>
                </div>

                <!-- Terms Checkbox -->
                <div class="pt-1">
                    <label class="flex items-start gap-2.5 text-xs text-slate-600 cursor-pointer">
                        <input type="checkbox"
                               name="agree_terms"
                               value="1"
                               {{ old('agree_terms') ? 'checked' : '' }}
                               required
                               class="w-4 h-4 mt-0.5 text-brand-600 rounded border-slate-300 focus:ring-brand-500">
                        <span class="leading-relaxed">
                            Saya telah membaca dan menyetujui <a href="{{ route('terms') }}" target="_blank" class="font-bold text-brand-600 hover:text-brand-700 underline underline-offset-2">Syarat dan Ketentuan</a> serta <a href="{{ route('privacy') }}" target="_blank" class="font-bold text-brand-600 hover:text-brand-700 underline underline-offset-2">Kebijakan Privasi</a> Retronics.
                        </span>
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit"
                        class="w-full py-4 px-6 rounded-2xl font-bold text-sm text-white bg-brand-600 hover:bg-brand-700 shadow-lg shadow-brand-500/25 transition">
                    Daftar Akun
                </button>

                <!-- Subtext Banner (Matching Figma) -->
                <div class="p-3.5 rounded-2xl bg-blue-50/70 border border-blue-100 text-xs text-brand-800 leading-relaxed text-center">
                    Informasi kontak dipakai untuk koordinasi sewa dan pengembalian, bukan untuk pembayaran otomatis.
                </div>
            </form>

        </div>

    </div>

</div>
@endsection
