@extends('layouts.app')

@section('title', 'Wishlist Gear | Retronics – Rental Fotografi')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">

    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-xs text-slate-500 mb-6">
        <a href="{{ route('home') }}" class="hover:text-brand-600 transition">Beranda</a>
        <span class="text-slate-300">›</span>
        <a href="{{ route('account.index') }}" class="hover:text-brand-600 transition">Akun Saya</a>
        <span class="text-slate-300">›</span>
        <span class="text-slate-800 font-semibold">Wishlist</span>
    </nav>

    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 font-display tracking-tight">
            Wishlist gear
        </h1>
        <p class="text-slate-500 text-sm mt-1">
            Simpan dulu idemu. Booking kapan pun jadwalmu siap.
        </p>
    </div>

    @if (session('success'))
        <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm flex items-center justify-between">
            <span class="font-medium">✓ {{ session('success') }}</span>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900 font-bold">✕</button>
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
                       class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-slate-600 hover:text-brand-600 hover:bg-slate-50 transition">
                        <span>Profil & Password</span>
                    </a>

                    <a href="{{ route('account.rentals') }}"
                       class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-slate-600 hover:text-brand-600 hover:bg-slate-50 transition">
                        <span>Riwayat Sewa</span>
                    </a>

                    <a href="{{ route('account.wishlist') }}"
                       class="flex items-center justify-between px-3.5 py-2.5 rounded-xl bg-brand-50 text-brand-600 font-bold transition">
                        <span>Wishlist</span>
                        <span class="text-[11px] font-bold px-2 py-0.5 rounded-full bg-brand-100 text-brand-700">
                            {{ $wishlistItems->count() }}
                        </span>
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

        <!-- RIGHT CONTENT: Wishlist Grid (lg:col-span-9) -->
        <div class="lg:col-span-9 space-y-6">

            <div class="text-xs font-bold text-slate-500">
                {{ $wishlistItems->count() }} gear tersimpan
            </div>

            @if($wishlistItems->isNotEmpty())
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($wishlistItems as $item)
                        @php
                            $product = $item->product;
                            $imageUrl = $product->image
                                ? (str_starts_with($product->image, 'http') ? $product->image : asset('storage/' . $product->image))
                                : 'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?auto=format&fit=crop&w=600&q=80';
                        @endphp

                        <div class="bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-sm hover:shadow-md transition flex flex-col justify-between p-4 group">
                            <!-- Image Container with badges -->
                            <div class="relative aspect-square rounded-2xl overflow-hidden bg-slate-100 mb-4">
                                <img src="{{ $imageUrl }}"
                                     alt="{{ $product->name }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition duration-300">

                                <!-- Category Badge -->
                                <div class="absolute top-3 left-3">
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-white/90 backdrop-blur-sm text-slate-800 shadow-sm border border-slate-200/50">
                                        {{ $product->category->name ?? 'Gear' }}
                                    </span>
                                </div>

                                <!-- Stock Badge -->
                                <div class="absolute top-3 right-3">
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold {{ $product->stock > 0 ? 'bg-emerald-50/90 text-emerald-700 border border-emerald-200/50' : 'bg-rose-50/90 text-rose-700 border border-rose-200/50' }} backdrop-blur-sm shadow-sm">
                                        {{ $product->stock > 0 ? $product->stock . ' tersedia' : 'Habis' }}
                                    </span>
                                </div>

                                <!-- Wishlist Active Toggle Button -->
                                <button type="button"
                                        @click="toggleWishlist({{ $product->id }})"
                                        class="absolute bottom-3 right-3 w-9 h-9 rounded-full bg-white shadow-md flex items-center justify-center text-rose-500 hover:scale-110 transition">
                                    <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                                </button>
                            </div>

                            <!-- Gear Details -->
                            <div>
                                <a href="{{ route('products.show', $product->slug) }}" class="block">
                                    <h3 class="font-extrabold text-base text-slate-900 group-hover:text-brand-600 transition truncate">
                                        {{ $product->name }}
                                    </h3>
                                </a>
                                <p class="text-xs text-slate-400 mt-0.5 truncate">
                                    {{ $product->condition ?? 'Mulus 99% · Teruji' }}
                                </p>
                                <div class="mt-2 text-lg font-extrabold text-slate-900 font-display">
                                    Rp{{ number_format($product->price_per_day, 0, ',', '.') }}
                                    <span class="text-xs font-normal text-slate-400">/hari</span>
                                </div>
                            </div>

                            <!-- Dual CTAs -->
                            <div class="mt-4 space-y-2">
                                <a href="{{ route('rentals.create', ['product_id' => $product->id, 'days' => 1]) }}"
                                   class="w-full py-2.5 px-3 rounded-xl font-bold text-xs text-white bg-brand-600 hover:bg-brand-700 shadow-md shadow-brand-500/20 transition flex items-center justify-center gap-1.5">
                                    <span>⚡ Sewa Sekarang</span>
                                </a>

                                <form method="POST" action="{{ route('cart.add') }}">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit"
                                            class="w-full py-2 px-3 rounded-xl font-bold text-xs text-slate-700 bg-white hover:bg-slate-50 border border-slate-200 transition">
                                        + Keranjang
                                    </button>
                                </form>

                                <!-- Direct Remove Link -->
                                <button type="button"
                                        @click="toggleWishlist({{ $product->id }})"
                                        class="w-full text-center text-xs font-semibold text-rose-500 hover:text-rose-700 pt-1 transition block">
                                    ♡ Hapus dari Wishlist
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Blue Info Notice at Bottom (Matching Figma Screenshot) -->
                <div class="p-4 rounded-2xl bg-blue-50/70 border border-blue-100 text-xs text-brand-800 leading-relaxed text-center">
                    Wishlist tidak mengunci stok. Pilih <strong>Sewa Sekarang</strong> untuk memeriksa jadwal dan mengajukan booking.
                </div>
            @else
                <div class="p-12 text-center bg-white rounded-3xl border border-slate-200 space-y-3">
                    <div class="w-16 h-16 rounded-full bg-rose-50 text-rose-500 flex items-center justify-center mx-auto text-2xl">
                        ♡
                    </div>
                    <h3 class="text-base font-bold text-slate-800">Wishlist Anda masih kosong</h3>
                    <p class="text-xs text-slate-500 max-w-sm mx-auto">
                        Klik ikon hati pada kamera atau lensa favorit di katalog untuk menyimpannya di sini.
                    </p>
                    <a href="{{ route('products.index') }}" class="inline-block mt-2 px-5 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs transition">
                        Lihat Katalog Gear
                    </a>
                </div>
            @endif

        </div>

    </div>

</div>
@endsection
