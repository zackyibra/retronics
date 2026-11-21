@extends('layouts.app')

@section('title', 'Masuk ke Retronics | Rental Fotografi')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-14">

    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-xs text-slate-500 mb-8">
        <a href="{{ route('home') }}" class="hover:text-brand-600 transition">Beranda</a>
        <span class="text-slate-300">›</span>
        <span class="text-slate-800 font-semibold">Masuk</span>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">

        <!-- LEFT COLUMN: Photography Gear Showcase & Tagline (lg:col-span-6) -->
        <div class="lg:col-span-6 space-y-6">
            <div class="relative rounded-3xl overflow-hidden bg-slate-100 border border-slate-200/80 shadow-md aspect-[4/3]">
                <img src="https://images.unsplash.com/photo-1516035069371-29a1b244cc32?auto=format&fit=crop&w=1200&q=80"
                     alt="Kamera Retronics"
                     class="w-full h-full object-cover">
            </div>

            <div>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 font-display tracking-tight">
                    Kembali ke ide berikutnya.
                </h2>
                <p class="text-slate-500 text-sm sm:text-base mt-2 leading-relaxed">
                    Masuk untuk booking gear, memakai promo, dan memantau setiap tahap sewamu.
                </p>
            </div>
        </div>

        <!-- RIGHT COLUMN: Login Form (lg:col-span-6) -->
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
                    Selamat datang kembali
                </span>
                <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 font-display tracking-tight mt-3">
                    Masuk ke Retronics
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-2">
                    Belum punya akun?
                    <a href="{{ route('register') }}" class="font-bold text-brand-600 hover:text-brand-700 underline underline-offset-2">
                        Daftar sekarang ↗
                    </a>
                </p>
            </div>

            @if (session('error'))
                <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-xs sm:text-sm">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-xs sm:text-sm">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf

                <!-- Email Input -->
                <div class="p-3.5 rounded-2xl border border-slate-200 bg-slate-50/50 focus-within:border-brand-500 focus-within:bg-white focus-within:ring-2 focus-within:ring-brand-100 transition">
                    <label for="email" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">
                        Email
                    </label>
                    <input type="email"
                           id="email"
                           name="email"
                           value="{{ old('email') }}"
                           placeholder="nama@email.com"
                           required
                           autofocus
                           class="w-full bg-transparent text-sm font-semibold text-slate-900 placeholder:text-slate-400 focus:outline-none">
                </div>

                <!-- Password Input -->
                <div class="p-3.5 rounded-2xl border border-slate-200 bg-slate-50/50 focus-within:border-brand-500 focus-within:bg-white focus-within:ring-2 focus-within:ring-brand-100 transition"
                     x-data="{ show: false }">
                    <div class="flex items-center justify-between">
                        <label for="password" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">
                            Password
                        </label>
                        <button type="button"
                                @click="show = !show"
                                class="text-[11px] font-bold text-slate-400 hover:text-slate-600 transition">
                            <span x-text="show ? 'Sembunyikan' : 'Lihat'"></span>
                        </button>
                    </div>
                    <input :type="show ? 'text' : 'password'"
                           id="password"
                           name="password"
                           placeholder="Masukkan password"
                           required
                           class="w-full bg-transparent text-sm font-semibold text-slate-900 placeholder:text-slate-400 focus:outline-none">
                </div>

                <!-- Remember -->
                <div class="flex items-center justify-between text-xs pt-1">
                    <label class="flex items-center gap-2 text-slate-600 cursor-pointer">
                        <input type="checkbox"
                               name="remember"
                               class="w-4 h-4 text-brand-600 rounded border-slate-300 focus:ring-brand-500">
                        <span>Ingat saya</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit"
                        class="w-full py-4 px-6 rounded-2xl font-bold text-sm text-white bg-brand-600 hover:bg-brand-700 shadow-lg shadow-brand-500/25 transition">
                    Masuk
                </button>

                <p class="text-[11px] text-slate-400 text-center leading-relaxed pt-2">
                    Gunakan akun penyewa untuk menyimpan wishlist dan mengelola riwayat sewa. Akses admin hanya tersedia sesuai peran akun.
                </p>
            </form>

        </div>

    </div>

</div>
@endsection
