@extends('layouts.app')

@section('title', 'Buat Kata Sandi Baru | Retronics – Rental Fotografi')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-14">

    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-xs text-slate-500 mb-8">
        <a href="{{ route('home') }}" class="hover:text-brand-600 transition">Beranda</a>
        <span class="text-slate-300">›</span>
        <a href="{{ route('login') }}" class="hover:text-brand-600 transition">Masuk</a>
        <span class="text-slate-300">›</span>
        <span class="text-slate-800 font-semibold">Buat Kata Sandi Baru</span>
    </nav>

    <div class="max-w-md mx-auto w-full space-y-6">

        <div class="text-center space-y-2">
            <div class="inline-flex items-center justify-center w-14 h-14 rounded-2xl bg-brand-100 text-brand-600 mb-2">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                </svg>
            </div>
            <h1 class="text-3xl font-extrabold text-slate-900 font-display tracking-tight">
                Buat Kata Sandi Baru
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 leading-relaxed">
                Verifikasi OTP WhatsApp berhasil. Silakan buat kata sandi baru yang kuat untuk melindungi akun Anda.
            </p>
        </div>

        @if (session('success'))
            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm flex items-start gap-3">
                <svg class="w-5 h-5 text-emerald-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-xs sm:text-sm">
                {{ session('error') }}
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

        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm">
            <form action="{{ route('otp.reset.submit') }}" method="POST" class="space-y-4" x-data="{ showNew: false, showConfirm: false }">
                @csrf

                <!-- Kata Sandi Baru -->
                <div class="space-y-1.5">
                    <label for="password" class="text-xs font-bold text-slate-700 uppercase tracking-wider">
                        Kata Sandi Baru
                    </label>
                    <div class="relative">
                        <input :type="showNew ? 'text' : 'password'"
                               id="password"
                               name="password"
                               placeholder="Minimal 8 karakter"
                               required
                               autofocus
                               class="w-full px-4 py-3.5 pr-12 rounded-2xl border border-slate-200 text-sm font-semibold text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 transition">
                        <button type="button"
                                @click="showNew = !showNew"
                                class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 transition">
                            <span class="text-xs font-semibold" x-text="showNew ? 'Sembunyikan' : 'Lihat'"></span>
                        </button>
                    </div>
                </div>

                <!-- Konfirmasi Kata Sandi Baru -->
                <div class="space-y-1.5">
                    <label for="password_confirmation" class="text-xs font-bold text-slate-700 uppercase tracking-wider">
                        Ulangi Kata Sandi Baru
                    </label>
                    <div class="relative">
                        <input :type="showConfirm ? 'text' : 'password'"
                               id="password_confirmation"
                               name="password_confirmation"
                               placeholder="Ulangi kata sandi baru"
                               required
                               class="w-full px-4 py-3.5 pr-12 rounded-2xl border border-slate-200 text-sm font-semibold text-slate-900 placeholder:text-slate-400 focus:outline-none focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 transition">
                        <button type="button"
                                @click="showConfirm = !showConfirm"
                                class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 transition">
                            <span class="text-xs font-semibold" x-text="showConfirm ? 'Sembunyikan' : 'Lihat'"></span>
                        </button>
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit"
                            class="w-full py-4 px-6 rounded-2xl font-bold text-sm text-white bg-brand-600 hover:bg-brand-700 shadow-lg shadow-brand-500/25 transition">
                        Simpan Kata Sandi Baru & Masuk
                    </button>
                </div>
            </form>
        </div>

    </div>

</div>
@endsection
