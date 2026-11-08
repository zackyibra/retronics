@extends('layouts.app')

@section('title', 'Verifikasi Kode OTP | Retronics – Rental Fotografi')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-14">

    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-xs text-slate-500 mb-8">
        <a href="{{ route('home') }}" class="hover:text-brand-600 transition">Beranda</a>
        <span class="text-slate-300">›</span>
        <a href="{{ route('login') }}" class="hover:text-brand-600 transition">Masuk</a>
        <span class="text-slate-300">›</span>
        <a href="{{ route('otp.request') }}" class="hover:text-brand-600 transition">Lupa Password</a>
        <span class="text-slate-300">›</span>
        <span class="text-slate-800 font-semibold">Verifikasi OTP</span>
    </nav>

    <div class="max-w-md mx-auto w-full space-y-6">

        <div class="text-center space-y-2">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-emerald-100 text-emerald-600 mb-2">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
            </div>
            <h1 class="text-3xl font-extrabold text-slate-900 font-display tracking-tight">
                Verifikasi Kode OTP
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 leading-relaxed">
                Kami telah mengirimkan 6 digit kode OTP rahasia ke nomor WhatsApp Anda:
                <span class="font-bold text-slate-800 block mt-1 text-base">{{ $maskedPhone }}</span>
            </p>
        </div>

        @if (session('demo_otp_hint'))
            <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 text-xs sm:text-sm">
                <div class="flex items-start gap-2.5">
                    <span class="text-base">💡</span>
                    <div>
                        <strong class="font-bold block">Mode Pengujian / Simulasi OTP:</strong>
                        <span>Kode OTP Anda saat ini adalah:</span>
                        <code class="px-2 py-0.5 ml-1 bg-amber-200/80 font-mono text-base font-bold rounded tracking-wider text-amber-900">
                            {{ session('demo_otp_hint') }}
                        </code>
                    </div>
                </div>
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
                <ul class="space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm space-y-6">
            <form action="{{ route('otp.verify.submit') }}" method="POST" class="space-y-5">
                @csrf

                <div class="space-y-2">
                    <label for="otp" class="block text-xs font-bold text-slate-700 uppercase tracking-wider text-center">
                        Masukkan 6 Digit Kode OTP
                    </label>
                    <input type="text"
                           id="otp"
                           name="otp"
                           inputmode="numeric"
                           pattern="[0-9]*"
                           maxlength="6"
                           autocomplete="one-time-code"
                           placeholder="••••••"
                           required
                           autofocus
                           class="w-full py-4 text-center text-3xl font-mono font-bold tracking-[0.5em] rounded-2xl border border-slate-200 text-slate-900 placeholder:text-slate-300 focus:outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 transition">
                    <p class="text-[11px] text-slate-400 text-center">
                        Kode berlaku selama 5 menit. Jangan bagikan kode ini kepada siapapun!
                    </p>
                </div>

                <button type="submit"
                        class="w-full py-4 px-6 rounded-2xl font-bold text-sm text-white bg-emerald-600 hover:bg-emerald-700 shadow-lg shadow-emerald-600/25 transition">
                    Verifikasi & Lanjutkan ↗
                </button>
            </form>

            <div class="pt-4 border-t border-slate-100 flex flex-col items-center gap-3 text-xs"
                 x-data="{ countdown: 60, timer: null }"
                 x-init="timer = setInterval(() => { if (countdown > 0) countdown--; else clearInterval(timer); }, 1000)">
                <div class="text-slate-500 text-center">
                    Belum menerima pesan WhatsApp?
                    <template x-if="countdown > 0">
                        <span class="font-semibold text-slate-700 block mt-1">
                            Kirim ulang kode dalam <span x-text="countdown" class="text-brand-600 font-bold font-mono"></span> detik
                        </span>
                    </template>
                    <template x-if="countdown === 0">
                        <a href="{{ route('otp.request') }}" class="font-bold text-brand-600 hover:text-brand-700 underline block mt-1">
                            Kirim Ulang Kode OTP Sekarang
                        </a>
                    </template>
                </div>

                <a href="{{ route('otp.request') }}" class="text-slate-400 hover:text-slate-600 transition font-medium">
                    ← Ganti nomor WhatsApp
                </a>
            </div>
        </div>

    </div>

</div>
@endsection
