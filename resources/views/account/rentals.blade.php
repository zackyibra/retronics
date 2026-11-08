@extends('layouts.app')

@section('title', 'Riwayat Sewa | Retronics – Rental Fotografi')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12"
     x-data="{ activeTab: 'semua' }">

    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-xs text-slate-500 mb-6">
        <a href="{{ route('home') }}" class="hover:text-brand-600 transition">Beranda</a>
        <span class="text-slate-300">›</span>
        <a href="{{ route('account.index') }}" class="hover:text-brand-600 transition">Akun Saya</a>
        <span class="text-slate-300">›</span>
        <span class="text-slate-800 font-semibold">Riwayat Sewa</span>
    </nav>

    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 font-display tracking-tight">
            Riwayat sewa
        </h1>
        <p class="text-slate-500 text-sm mt-1">
            Pantau pembayaran, jadwal pengembalian, dan semua karya yang sudah kamu mulai.
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
                       class="flex items-center justify-between px-3.5 py-2.5 rounded-xl bg-brand-50 text-brand-600 font-bold transition">
                        <span>Riwayat Sewa</span>
                        <span class="text-[11px] font-bold px-2 py-0.5 rounded-full bg-brand-100 text-brand-700">
                            {{ $rentals->total() }}
                        </span>
                    </a>

                    <a href="{{ route('account.wishlist') }}"
                       class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-slate-600 hover:text-brand-600 hover:bg-slate-50 transition">
                        <span>Wishlist</span>
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

        <!-- RIGHT CONTENT: Tabs & Rental Cards (lg:col-span-9) -->
        <div class="lg:col-span-9 space-y-6">

            <!-- Filter Tabs (Matching Figma Screenshot) -->
            <div class="flex items-center gap-2 overflow-x-auto pb-2">
                <button type="button"
                        @click="activeTab = 'semua'"
                        class="px-4 py-2 rounded-xl text-xs font-bold transition flex-shrink-0"
                        :class="activeTab === 'semua' ? 'bg-brand-600 text-white shadow-sm' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50'">
                    Semua
                </button>
                <button type="button"
                        @click="activeTab = 'pending_payment'"
                        class="px-4 py-2 rounded-xl text-xs font-bold transition flex-shrink-0"
                        :class="activeTab === 'pending_payment' ? 'bg-brand-600 text-white shadow-sm' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50'">
                    Menunggu Bayar
                </button>
                <button type="button"
                        @click="activeTab = 'berjalan'"
                        class="px-4 py-2 rounded-xl text-xs font-bold transition flex-shrink-0"
                        :class="activeTab === 'berjalan' ? 'bg-brand-600 text-white shadow-sm' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50'">
                    Sedang Berjalan
                </button>
                <button type="button"
                        @click="activeTab = 'completed'"
                        class="px-4 py-2 rounded-xl text-xs font-bold transition flex-shrink-0"
                        :class="activeTab === 'completed' ? 'bg-brand-600 text-white shadow-sm' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50'">
                    Selesai
                </button>
                <button type="button"
                        @click="activeTab = 'cancelled'"
                        class="px-4 py-2 rounded-xl text-xs font-bold transition flex-shrink-0"
                        :class="activeTab === 'cancelled' ? 'bg-brand-600 text-white shadow-sm' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50'">
                    Dibatalkan
                </button>
            </div>

            <!-- List of Rentals -->
            @forelse($rentals as $rental)
                @php
                    $isPending = ($rental->status === 'pending_payment');
                    $isCompleted = ($rental->status === 'completed');
                    $isCancelled = ($rental->status === 'cancelled');
                    $isOngoing = in_array($rental->status, ['waiting_verification', 'confirmed', 'preparing', 'ready_pickup', 'in_use', 'returned', 'needs_maintenance']);

                    $firstItem = $rental->items->first();
                    $otherItemsCount = $rental->items->count() - 1;
                    $gearTitle = $firstItem ? $firstItem->product->name : 'Gear Rental';
                    if ($otherItemsCount > 0) {
                        $gearTitle .= ' + ' . $rental->items->slice(1)->pluck('product.name')->join(', ');
                    }

                    $primaryImage = $firstItem && $firstItem->product->image
                        ? (str_starts_with($firstItem->product->image, 'http') ? $firstItem->product->image : asset('storage/' . $firstItem->product->image))
                        : 'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?auto=format&fit=crop&w=400&q=80';
                @endphp

                <div class="bg-white p-6 sm:p-7 rounded-3xl border border-slate-200 shadow-sm space-y-5 transition hover:border-slate-300"
                     x-show="activeTab === 'semua'
                             || (activeTab === 'pending_payment' && '{{ $rental->status }}' === 'pending_payment')
                             || (activeTab === 'berjalan' && {{ $isOngoing ? 'true' : 'false' }})
                             || (activeTab === 'completed' && '{{ $rental->status }}' === 'completed')
                             || (activeTab === 'cancelled' && '{{ $rental->status }}' === 'cancelled')">

                    <!-- Top Row: Order Code, Dates, and Status Pill -->
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <div class="text-base sm:text-lg font-extrabold text-slate-900 font-display">
                                {{ $rental->rental_code }}
                            </div>
                            <div class="text-xs text-slate-500 mt-0.5">
                                {{ $rental->start_date->translatedFormat('d') }} – {{ $rental->end_date->translatedFormat('d M Y') }} · {{ $rental->duration_days }} hari
                            </div>
                        </div>

                        <div>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border {{ $rental->status_badge_class }}">
                                {{ $rental->status_label }}
                            </span>
                        </div>
                    </div>

                    <!-- Middle Row: Thumbnail, Gear Names, Subtitle, and Price -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pt-4 border-t border-slate-100">
                        <div class="flex items-center gap-4">
                            <img src="{{ $primaryImage }}"
                                 alt="{{ $gearTitle }}"
                                 class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl object-cover bg-slate-100 border border-slate-100 flex-shrink-0">

                            <div>
                                <h3 class="text-sm sm:text-base font-extrabold text-slate-900">
                                    {{ $gearTitle }}
                                </h3>
                                <p class="text-xs text-slate-500 mt-1">
                                    {{ $rental->items->sum('quantity') }} unit · {{ $rental->duration_days }} hari
                                    @if($isPending)
                                        · <span class="text-amber-600 font-semibold">bukti belum diunggah</span>
                                    @elseif($rental->status === 'waiting_verification')
                                        · <span class="text-blue-600 font-semibold">menunggu verifikasi admin</span>
                                    @elseif($isCompleted)
                                        · <span class="text-emerald-600 font-semibold">kembali tepat waktu · transaksi ditutup</span>
                                    @elseif($isCancelled)
                                        · <span class="text-rose-600 font-semibold">dibatalkan</span>
                                    @endif
                                </p>
                            </div>
                        </div>

                        <!-- Price display -->
                        <div class="sm:text-right">
                            <div class="text-xl sm:text-2xl font-extrabold text-slate-900 font-display tracking-tight">
                                Rp{{ number_format($rental->total_price, 0, ',', '.') }}
                            </div>
                        </div>
                    </div>

                    <!-- Actions Bottom Row -->
                    <div class="pt-4 border-t border-slate-100 flex flex-wrap items-center justify-end gap-3">
                        <a href="{{ route('rentals.show', $rental->id) }}"
                           class="px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs transition shadow-sm">
                            Lihat Detail
                        </a>

                        @if($isPending)
                            <a href="{{ route('rentals.show', $rental->id) }}"
                               class="px-5 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs transition shadow-md shadow-brand-500/20">
                                Bayar Sekarang
                            </a>
                        @elseif($isCompleted)
                            @if($firstItem)
                                <a href="{{ route('products.show', $firstItem->product->slug) }}#review-section"
                                   class="px-5 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs transition shadow-md shadow-brand-500/20">
                                    Beri Ulasan Bintang
                                </a>
                            @endif
                        @elseif($isCancelled)
                            <a href="{{ route('products.index') }}"
                               class="px-5 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs transition shadow-md shadow-brand-500/20">
                                Sewa Lagi
                            </a>
                        @endif
                    </div>

                    @if($isCompleted)
                        <div class="pt-2 text-[11px] text-slate-400">
                            ★★★★★ Pilih bintang dan ceritakan pengalaman sewamu. Ulasan membantu penyewa lain memilih gear terbaik.
                        </div>
                    @endif
                </div>
            @empty
                <div class="p-12 text-center bg-white rounded-3xl border border-slate-200 space-y-3">
                    <div class="w-16 h-16 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto text-2xl">
                        📷
                    </div>
                    <h3 class="text-base font-bold text-slate-800">Belum ada riwayat penyewaan</h3>
                    <p class="text-xs text-slate-500 max-w-sm mx-auto">
                        Kamu belum pernah membuat pesanan sewa kamera atau lensa. Jelajahi katalog gear siap pakai kami.
                    </p>
                    <a href="{{ route('products.index') }}" class="inline-block mt-2 px-5 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs transition">
                        Jelajahi Katalog
                    </a>
                </div>
            @endforelse

            <div class="mt-6">
                {{ $rentals->links() }}
            </div>

        </div>

    </div>

</div>
@endsection
