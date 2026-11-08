@extends('layouts.app')

@section('title', 'Lupa Password | Retronics – Rental Fotografi')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-14">

    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-xs text-slate-500 mb-8">
        <a href="{{ route('home') }}" class="hover:text-brand-600 transition">Beranda</a>
        <span class="text-slate-300">›</span>
        <a href="{{ route('login') }}" class="hover:text-brand-600 transition">Masuk</a>
        <span class="text-slate-300">›</span>
        <span class="text-slate-800 font-semibold">Lupa Password</span>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">

        <!-- LEFT COLUMN: Security & Recovery Info -->
        <div class="lg:col-span-6 space-y-6">
            <div class="relative rounded-3xl border border-slate-200/80 bg-slate-100 overflow-hidden shadow-md aspect-[4/3]">
                <img src="https://images.unsplash.com/photo-1516035069371-29a1b244cc32?auto=format&fit=crop&w=1200&q=80"
                     alt="Keamanan Akun Retronics"
                     class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 via-slate-900/20 to-transparent flex flex-col justify-end p-6 sm:p-8">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold tracking-wider uppercase text-emerald-300 bg-emerald-950/70 border border-emerald-500/30 backdrop-blur w-fit mb-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        WhatsApp OTP Protection
                    </span>
                    <h3 class="text-xl sm:text-2xl font-bold text-white font-display">
                        Aman, Cepat, dan Otomatis
                    </h3>
                    <p class="text-xs sm:text-sm text-slate-200 mt-1 max-w-md">
                        Kode verifikasi satu kali (One-Time Password) langsung diteruskan ke WhatsApp pribadi yang tertaut pada akun Anda.
                    </p>
                </div>
            </div>

            <div class="space-y-3">
                <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-display tracking-tight">
                    Panduan Pemulihan Akun
                </h2>
                <ul class="space-y-2.5 text-xs sm:text-sm font-semibold text-slate-700">
                    <li class="flex items-center gap-2.5">
                        <span class="w-5 h-5 rounded-full bg-brand-50 text-brand-600 flex items-center justify-center font-bold text-xs flex-shrink-0">1</span>
                        <span>Ketik nomor WhatsApp aktif atau email yang terdaftar.</span>
                    </li>
                    <li class="flex items-center gap-2.5">
                        <span class="w-5 h-5 rounded-full bg-brand-50 text-brand-600 flex items-center justify-center font-bold text-xs flex-shrink-0">2</span>
                        <span>Terima 6 digit kode OTP rahasia melalui chat WhatsApp Retronics.</span>
                    </li>
                    <li class="flex items-center gap-2.5">
                        <span class="w-5 h-5 rounded-full bg-brand-50 text-brand-600 flex items-center justify-center font-bold text-xs flex-shrink-0">3</span>
                        <span>Verifikasi kode dan tetapkan kata sandi baru untuk melanjutkan booking gear.</span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- RIGHT COLUMN: Request OTP Form -->
        <div class="lg:col-span-6 max-w-lg mx-auto w-full space-y-6">

            <div>
                <span class="inline-block px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider text-brand-600 bg-brand-50 border border-brand-100">
                    Pemulihan Akun
                </span>
                <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 font-display tracking-tight mt-3">
                    Lupa Kata Sandi?
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-2 leading-relaxed">
                    Jangan khawatir! Masukkan nomor WhatsApp atau alamat email akun Anda. Kami akan mengirimkan kode OTP untuk mereset kata sandi.
                </p>
            </div>

            @if (session('error'))
                <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-xs sm:text-sm flex items-start gap-3">
                    <svg class="w-5 h-5 text-rose-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if (session('success'))
                <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm flex items-start gap-3">
                    <svg class="w-5 h-5 text-emerald-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-xs sm:text-sm">
                    <p class="font-bold mb-1">Periksa kembali data Anda:</p>
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('otp.send') }}" method="POST" class="space-y-4">
                @csrf

                <!-- Account Identifier Input -->
                <div class="space-y-1.5">
                    <label for="account_identifier" class="text-xs font-bold text-slate-700 uppercase tracking-wider">
                        Nomor WhatsApp atau Email Terdaftar
                    </label>
                    <div class="relative">
                        <input type="text"
                               id="account_identifier"
                               name="account_identifier"
                               value="{{ old('account_identifier') }}"
                               placeholder="Contoh: 081929513780 atau nama@email.com"
                               required
                               autofocus
                               class="w-full px-4 py-3.5 pl-11 rounded-2xl border border-slate-200 text-sm font-semibold text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 transition">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                        </div>
                    </div>
                    <p class="text-[11px] text-slate-400">
                        Format nomor WhatsApp boleh diawali <code class="text-slate-600 bg-slate-100 px-1 py-0.5 rounded">08xx</code>, <code class="text-slate-600 bg-slate-100 px-1 py-0.5 rounded">628xx</code>, atau <code class="text-slate-600 bg-slate-100 px-1 py-0.5 rounded">+628xx</code>.
                    </p>
                </div>

                <!-- Submit Button -->
                <button type="submit"
                        class="w-full py-4 px-6 rounded-2xl font-bold text-sm text-white bg-emerald-600 hover:bg-emerald-700 shadow-lg shadow-emerald-600/25 transition flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                    <span>Kirim Kode OTP ke WhatsApp</span>
                </button>

                <div class="pt-4 border-t border-slate-200/80 flex items-center justify-between text-xs">
                    <a href="{{ route('login') }}" class="font-semibold text-slate-600 hover:text-brand-600 transition flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        <span>Kembali ke Halaman Masuk</span>
                    </a>
                    <a href="https://wa.me/6281929513780" target="_blank" rel="noopener" class="text-emerald-600 hover:text-emerald-700 font-semibold flex items-center gap-1">
                        <span>Bantuan CS</span>
                        <span>↗</span>
                    </a>
                </div>
            </form>

        </div>

    </div>

</div>
@endsection
