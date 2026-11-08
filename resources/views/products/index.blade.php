@extends('layouts.app')

@section('title', 'Katalog Kamera & Peralatan Fotografi')
@section('meta_description', 'Katalog sewa kamera DSLR, mirrorless, lensa premium, lighting studio, dan stabilizer di Retronics Gunung Putri, Bogor. Temukan gear terbaik untuk kebutuhan produksi Anda.')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full flex-grow">

    <!-- Breadcrumb -->
    <x-breadcrumb :items="[['label' => 'Katalog Gear', 'url' => '']]" />

    <!-- Page Header & Summary -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-blue-50 text-brand-700 border border-blue-200/80 mb-2">
                <span>📷 Inventaris Lengkap</span>
            </div>
            <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 font-display">
                Katalog Peralatan Fotografi & Kamera
            </h1>
            <p class="text-slate-500 text-xs sm:text-sm mt-1 max-w-2xl">
                Temukan bodi kamera mirrorless & DSLR, lensa prima/zoom, pencahayaan studio, dan gimbal stabilizer siap pakai untuk kebutuhan produksi Anda.
            </p>
        </div>

        <div class="text-xs font-semibold text-slate-500 bg-white px-4 py-2.5 rounded-xl border border-slate-200 shadow-xs self-start md:self-auto">
            Menampilkan <strong class="text-slate-900">{{ $products->total() }}</strong> peralatan fotografi
        </div>
    </div>

    <!-- Filter & Search Toolbar (Matching Design Image) -->
    <div class="bg-white p-5 sm:p-6 rounded-3xl border border-slate-200/90 shadow-sm mb-8">
        <form method="GET" action="{{ route('products.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3.5 items-center">
            
            <!-- Search Keyword Input -->
            <div class="sm:col-span-5 relative">
                <input type="text"
                       name="q"
                       value="{{ request('q') }}"
                       placeholder="Cari kamera, lensa, lighting, stabilizer, mic..."
                       class="w-full pl-10 pr-4 py-2.5 text-xs sm:text-sm rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500 text-slate-900">
                <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>

            <!-- Category Select Filter -->
            <div class="sm:col-span-3">
                <select name="category" class="w-full px-3.5 py-2.5 text-xs sm:text-sm rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500 text-slate-800">
                    <option value="">Semua Kategori</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->name }}" {{ request('category') == $cat->name ? 'selected' : '' }}>
                            {{ $cat->name }} ({{ $cat->products_count }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Sort By Filter -->
            <div class="sm:col-span-2">
                <select name="sort" class="w-full px-3.5 py-2.5 text-xs sm:text-sm rounded-xl bg-slate-50 border border-slate-200 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-500 text-slate-800">
                    <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Urut Nama A-Z</option>
                    <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Tarif Terendah</option>
                    <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Tarif Tertinggi</option>
                    <option value="stock" {{ request('sort') == 'stock' ? 'selected' : '' }}>Stok Terbanyak</option>
                </select>
            </div>

            <!-- Submit Button & Reset -->
            <div class="sm:col-span-2 flex items-center gap-2">
                <button type="submit" class="w-full py-2.5 px-4 bg-brand-600 hover:bg-brand-700 text-white rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5 shadow-xs">
                    <span>Filter</span>
                </button>
                @if (request()->hasAny(['q', 'category', 'sort']))
                    <a href="{{ route('products.index') }}"
                       title="Reset Filter"
                       class="p-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-bold transition">
                        ✕
                    </a>
                @endif
            </div>

        </form>

        <!-- Category Filter Quick Chips -->
        <div class="mt-4 pt-4 border-t border-slate-100 flex flex-wrap items-center gap-2">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mr-1">Kategori:</span>
            <a href="{{ route('products.index', array_merge(request()->except(['category', 'page']))) }}"
               class="px-3 py-1 rounded-lg text-xs font-semibold transition {{ !request('category') ? 'bg-brand-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                Semua
            </a>
            @foreach ($categories as $cat)
                <a href="{{ route('products.index', array_merge(request()->except(['page']), ['category' => $cat->name])) }}"
                   class="px-3 py-1 rounded-lg text-xs font-semibold transition {{ request('category') == $cat->name ? 'bg-brand-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    {{ $cat->name }} ({{ $cat->products_count }})
                </a>
            @endforeach
        </div>
    </div>

    <!-- Product Grid (4 columns desktop, matching design screen #2) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse ($products as $index => $product)
            <x-product-card :product="$product" :index="$index" />
        @empty
            <div class="col-span-full py-16 text-center bg-white rounded-3xl border border-slate-200 p-8">
                <div class="w-16 h-16 rounded-2xl bg-blue-50 text-brand-600 text-3xl flex items-center justify-center mx-auto mb-4">
                    🔍
                </div>
                <h3 class="font-display font-bold text-lg text-slate-900">Peralatan Tidak Ditemukan</h3>
                <p class="text-xs sm:text-sm text-slate-500 mt-1 max-w-md mx-auto">
                    Tidak ada gear fotografi yang cocok dengan kata kunci atau filter pencarian Anda. Coba gunakan kata kunci lain atau reset filter.
                </p>
                <div class="mt-5">
                    <a href="{{ route('products.index') }}" class="inline-flex px-5 py-2.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white text-xs font-bold transition shadow-xs">
                        Lihat Semua Peralatan
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination Links -->
    <div class="mt-10">
        {{ $products->links() }}
    </div>

</div>
@endsection
