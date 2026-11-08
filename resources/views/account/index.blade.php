@extends('layouts.app')

@section('title', 'Profil & Password | Retronics – Rental Fotografi')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">

    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-xs text-slate-500 mb-6">
        <a href="{{ route('home') }}" class="hover:text-brand-600 transition">Beranda</a>
        <span class="text-slate-300">›</span>
        <a href="{{ route('account.index') }}" class="hover:text-brand-600 transition">Akun Saya</a>
        <span class="text-slate-300">›</span>
        <span class="text-slate-800 font-semibold">Profil & Password</span>
    </nav>

    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 font-display tracking-tight">
            Profil & Password
        </h1>
        <p class="text-slate-500 text-sm mt-1">
            Jaga informasi tetap terbaru agar koordinasi sewa lebih mudah.
        </p>
    </div>

    @if (session('success'))
        <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm flex items-center justify-between">
            <span class="font-medium">✓ {{ session('success') }}</span>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900 font-bold">✕</button>
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-xs sm:text-sm">
            <div class="font-bold mb-1">Periksa isian berikut:</div>
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

        <!-- LEFT SIDEBAR: Akun Saya Navigation (Matching Figma) -->
        <div class="lg:col-span-3">
            <div class="bg-white p-5 rounded-3xl border border-slate-200 shadow-sm space-y-2 sticky top-24">
                <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400 px-3 py-1">
                    Akun Saya
                </h2>

                <nav class="space-y-1 text-sm font-semibold">
                    <a href="{{ route('account.index') }}"
                       class="flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl bg-brand-50 text-brand-600 font-bold transition">
                        <span>Profil & Password</span>
                    </a>

                    <a href="{{ route('account.rentals') }}"
                       class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-slate-600 hover:text-brand-600 hover:bg-slate-50 transition">
                        <span>Riwayat Sewa</span>
                        @if($recentRentals->isNotEmpty())
                            <span class="text-[11px] font-bold px-2 py-0.5 rounded-full bg-slate-100 text-slate-600">
                                {{ $recentRentals->count() }}
                            </span>
                        @endif
                    </a>

                    <a href="{{ route('account.wishlist') }}"
                       class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-slate-600 hover:text-brand-600 hover:bg-slate-50 transition">
                        <span>Wishlist</span>
                        @if($wishlistsCount > 0)
                            <span class="text-[11px] font-bold px-2 py-0.5 rounded-full bg-slate-100 text-slate-600">
                                {{ $wishlistsCount }}
                            </span>
                        @endif
                    </a>

                    <form method="POST" action="{{ route('logout') }}" class="pt-2 border-t border-slate-100">
                        @csrf
                        <button type="submit"
                                class="w-full text-left px-3.5 py-2.5 rounded-xl text-slate-600 hover:text-rose-600 hover:bg-rose-50 font-semibold transition">
                            Logout
                        </button>
                    </form>
                </nav>
            </div>
        </div>

        <!-- RIGHT CONTENT: Data Pribadi & Ubah Password (lg:col-span-9) -->
        <div class="lg:col-span-9 space-y-8">

            <!-- Card: Data pribadi -->
            <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-sm space-y-6">
                <h2 class="text-xl font-bold text-slate-900 font-display">
                    Data pribadi
                </h2>

                <!-- User Avatar Header -->
                @php
                    $names = explode(' ', trim($user->name));
                    $initials = count($names) >= 2 ? strtoupper(substr($names[0], 0, 1) . substr($names[1], 0, 1)) : strtoupper(substr($names[0], 0, 2));
                @endphp
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-brand-50 text-brand-600 font-display font-extrabold text-xl flex items-center justify-center border border-brand-100">
                        {{ $initials }}
                    </div>
                    <div>
                        <div class="font-extrabold text-lg text-slate-900">{{ $user->name }}</div>
                        <span class="inline-block mt-0.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-slate-100 text-slate-600">
                            {{ $user->isAdmin() ? 'Administrator Store' : 'Pelanggan Retronics' }}
                        </span>
                    </div>
                </div>

                <form method="POST" action="{{ route('account.profile.update') }}" class="space-y-5">
                    @csrf

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Nama Lengkap -->
                        <div class="p-3.5 rounded-2xl border border-slate-200 bg-slate-50/50 focus-within:border-brand-500 focus-within:bg-white transition">
                            <label for="name" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">
                                Nama lengkap *
                            </label>
                            <input type="text"
                                   id="name"
                                   name="name"
                                   value="{{ old('name', $user->name) }}"
                                   required
                                   class="w-full bg-transparent text-sm font-semibold text-slate-800 focus:outline-none">
                        </div>

                        <!-- Email -->
                        <div class="p-3.5 rounded-2xl border border-slate-200 bg-slate-50/50 focus-within:border-brand-500 focus-within:bg-white transition">
                            <label for="email" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">
                                Email *
                            </label>
                            <input type="email"
                                   id="email"
                                   name="email"
                                   value="{{ old('email', $user->email) }}"
                                   required
                                   class="w-full bg-transparent text-sm font-semibold text-slate-800 focus:outline-none">
                        </div>

                        <!-- WhatsApp aktif -->
                        <div class="p-3.5 rounded-2xl border border-slate-200 bg-slate-50/50 focus-within:border-brand-500 focus-within:bg-white transition">
                            <label for="phone" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">
                                WhatsApp aktif *
                            </label>
                            <input type="text"
                                   id="phone"
                                   name="phone"
                                   value="{{ old('phone', $user->phone) }}"
                                   placeholder="0812-xxxx-xxxx"
                                   required
                                   class="w-full bg-transparent text-sm font-semibold text-slate-800 focus:outline-none">
                            <span class="block text-[11px] text-slate-400 mt-1">
                                Dipakai untuk koordinasi pengambilan dan pengembalian.
                            </span>
                        </div>

                        <!-- Domisili / Alamat -->
                        <div class="p-3.5 rounded-2xl border border-slate-200 bg-slate-50/50 focus-within:border-brand-500 focus-within:bg-white transition">
                            <label for="address" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">
                                Domisili *
                            </label>
                            <input type="text"
                                   id="address"
                                   name="address"
                                   value="{{ old('address', $user->address ?? 'Gunung Putri, Bogor') }}"
                                   placeholder="Kota domisili saat ini"
                                   required
                                   class="w-full bg-transparent text-sm font-semibold text-slate-800 focus:outline-none">
                            <span class="block text-[11px] text-slate-400 mt-1">
                                Kota tempat tinggal saat ini.
                            </span>
                        </div>
                    </div>

                    <!-- Blue Info Box -->
                    <div class="p-4 rounded-2xl bg-blue-50/70 border border-blue-100 text-xs text-brand-800 leading-relaxed">
                        Data kontak dan domisili Anda digunakan admin store untuk memverifikasi kesiapan unit sewa dan koordinasi serah terima di store Gunung Putri, Bogor.
                    </div>

                    <button type="submit"
                            class="px-6 py-3.5 rounded-xl font-bold text-xs sm:text-sm text-white bg-brand-600 hover:bg-brand-700 shadow-md shadow-brand-500/20 transition">
                        Simpan Perubahan
                    </button>
                </form>
            </div>

            <!-- Card: Ubah password -->
            <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-sm space-y-6">
                <h2 class="text-xl font-bold text-slate-900 font-display">
                    Ubah password
                </h2>

                <form method="POST" action="{{ route('account.security.update') }}" class="space-y-5">
                    @csrf
                    <input type="hidden" name="name" value="{{ $user->name }}">
                    <input type="hidden" name="email" value="{{ $user->email }}">

                    <!-- Password Saat Ini -->
                    <div class="p-3.5 rounded-2xl border border-slate-200 bg-slate-50/50 focus-within:border-brand-500 focus-within:bg-white transition">
                        <label for="current_password" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">
                            Password saat ini
                        </label>
                        <input type="password"
                               id="current_password"
                               name="current_password"
                               placeholder="Masukkan password akun Anda saat ini"
                               required
                               class="w-full bg-transparent text-sm font-semibold text-slate-800 focus:outline-none">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Password Baru -->
                        <div class="p-3.5 rounded-2xl border border-slate-200 bg-slate-50/50 focus-within:border-brand-500 focus-within:bg-white transition">
                            <label for="new_password" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">
                                Password baru
                            </label>
                            <input type="password"
                                   id="new_password"
                                   name="new_password"
                                   placeholder="Minimal 8 karakter"
                                   required
                                   class="w-full bg-transparent text-sm font-semibold text-slate-800 focus:outline-none">
                            <span class="block text-[11px] text-slate-400 mt-1">
                                Minimal 8 karakter kombinasi.
                            </span>
                        </div>

                        <!-- Konfirmasi Password Baru -->
                        <div class="p-3.5 rounded-2xl border border-slate-200 bg-slate-50/50 focus-within:border-brand-500 focus-within:bg-white transition">
                            <label for="new_password_confirmation" class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">
                                Konfirmasi password baru
                            </label>
                            <input type="password"
                                   id="new_password_confirmation"
                                   name="new_password_confirmation"
                                   placeholder="Ulangi password baru"
                                   required
                                   class="w-full bg-transparent text-sm font-semibold text-slate-800 focus:outline-none">
                        </div>
                    </div>

                    <p class="text-xs text-slate-400">
                        Gunakan kombinasi karakter yang tidak mudah ditebak dan jangan bagikan password kepada siapa pun.
                    </p>

                    <button type="submit"
                            class="px-6 py-3.5 rounded-xl font-bold text-xs sm:text-sm text-white bg-brand-600 hover:bg-brand-700 shadow-md shadow-brand-500/20 transition">
                        Perbarui Password
                    </button>
                </form>
            </div>

        </div>

    </div>

</div>
@endsection
