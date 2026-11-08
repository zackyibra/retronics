@props(['product', 'index' => 0])

<div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs hover:shadow-xl hover:border-brand-400 transition-all duration-300 flex flex-col justify-between overflow-hidden group">
    <div>
        <!-- Image & Badges Container -->
        <div class="relative bg-slate-50 p-4 h-52 flex items-center justify-center overflow-hidden border-b border-slate-100">
            <!-- Category Badge -->
            <span class="absolute top-3 left-3 text-[10px] font-bold px-2.5 py-0.5 rounded-full bg-white/95 text-slate-700 shadow-xs border border-slate-200 backdrop-blur-xs z-10">
                {{ $product->category->name ?? 'Gear' }}
            </span>

            <!-- Stock Badge -->
            @if ($product->stock > 0)
                <span class="absolute top-3 right-12 text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200/90 z-10">
                    Stok: {{ $product->stock }}
                </span>
            @else
                <span class="absolute top-3 right-12 text-[10px] font-bold px-2 py-0.5 rounded-full bg-rose-50 text-rose-700 border border-rose-200/90 z-10">
                    Habis
                </span>
            @endif

            <!-- Wishlist Button -->
            @auth
                @php
                    $isWishlisted = auth()->user()->wishlists()->where('product_id', $product->id)->exists();
                @endphp
                <form method="POST" action="{{ route('wishlist.toggle', $product->id) }}" class="absolute top-2.5 right-2.5 z-10">
                    @csrf
                    <button type="submit"
                            title="{{ $isWishlisted ? 'Hapus dari Wishlist' : 'Tambah ke Wishlist' }}"
                            class="p-1.5 rounded-xl bg-white/90 hover:bg-white border border-slate-200 shadow-xs text-xs transition {{ $isWishlisted ? 'text-rose-600' : 'text-slate-400 hover:text-rose-600' }}">
                        <svg class="w-4 h-4 {{ $isWishlisted ? 'fill-current' : 'fill-none' }}" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}"
                   title="Masuk untuk simpan ke Wishlist"
                   class="absolute top-2.5 right-2.5 z-10 p-1.5 rounded-xl bg-white/90 hover:bg-white border border-slate-200 shadow-xs text-xs text-slate-400 hover:text-rose-600 transition">
                    <svg class="w-4 h-4 fill-none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                    </svg>
                </a>
            @endauth

            <!-- Product Image -->
            <a href="{{ route('products.show', $product->slug) }}" class="w-full h-full flex items-center justify-center">
                @if ($product->image)
                    <img src="{{ asset('storage/' . $product->image) }}"
                         alt="{{ $product->name }}"
                         class="max-h-40 max-w-full object-contain drop-shadow transition duration-300 group-hover:scale-105"
                         onerror="this.onerror=null; this.src='{{ asset('images/products/' . $product->image) }}';">
                @else
                    <div class="w-16 h-16 rounded-2xl bg-white shadow-xs flex items-center justify-center text-3xl">
                        📷
                    </div>
                @endif
            </a>
        </div>

        <!-- Product Details -->
        <div class="p-5">
            <div class="flex items-center justify-between mb-1">
                <span class="text-[10px] font-semibold text-slate-400">
                    Kondisi: <strong class="text-emerald-700 capitalize">{{ $product->condition }}</strong>
                </span>
                @if ($product->reviews_count ?? false)
                    <span class="text-[10px] font-bold text-amber-500 flex items-center gap-0.5">
                        ★ {{ number_format($product->reviews_avg_rating ?? 5.0, 1) }}
                    </span>
                @endif
            </div>

            <a href="{{ route('products.show', $product->slug) }}">
                <h3 class="font-bold text-slate-900 text-sm group-hover:text-brand-600 transition line-clamp-1 leading-snug">
                    {{ $product->name }}
                </h3>
            </a>

            <p class="text-xs text-slate-500 mt-1 line-clamp-2 leading-relaxed">
                {{ $product->description }}
            </p>
        </div>
    </div>

    <!-- Pricing & Dual Action Buttons (Sewa Sekarang & Keranjang) -->
    <div class="p-5 pt-0">
        <div class="pt-3 border-t border-slate-100 flex items-baseline justify-between mb-3">
            <div>
                <span class="text-[10px] text-slate-400 block font-medium">Tarif Sewa</span>
                <span class="text-base font-bold text-brand-600 font-display">
                    Rp{{ number_format($product->price_per_day, 0, ',', '.') }}
                </span>
                <span class="text-[10px] text-slate-400">/ hari</span>
            </div>

            <a href="{{ route('products.show', $product->slug) }}"
               class="text-xs font-semibold text-slate-500 hover:text-brand-600 underline">
                Rincian Gear →
            </a>
        </div>

        @if (auth()->check() && auth()->user()->isAdmin())
            <a href="{{ route('products.show', $product->slug) }}"
               class="w-full py-2.5 px-3 rounded-xl font-bold text-xs text-amber-800 bg-amber-50 hover:bg-amber-100 border border-amber-200 transition flex items-center justify-center gap-1.5 text-center">
                <span>👁️ Lihat Detail Unit</span>
            </a>
        @elseif ($product->stock > 0)
            <div class="grid grid-cols-12 gap-2">
                <!-- Direct Booking CTA: Sewa Sekarang -->
                <a href="{{ route('rentals.create', ['product_id' => $product->id, 'days' => 1]) }}"
                   class="col-span-8 py-2.5 px-3 rounded-xl font-bold text-xs text-white bg-brand-600 hover:bg-brand-700 shadow-xs transition flex items-center justify-center gap-1.5 text-center">
                    <span>⚡ Sewa Sekarang</span>
                </a>

                <!-- Add To Cart Form -->
                <form method="POST" action="{{ route('cart.add') }}" class="col-span-4">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="quantity" value="1">
                    <input type="hidden" name="duration_days" value="1">
                    <button type="submit"
                            title="Tambah ke Keranjang Sewa"
                            class="w-full py-2.5 rounded-xl font-bold text-xs text-slate-800 bg-slate-100 hover:bg-slate-200 border border-slate-200 transition flex items-center justify-center">
                        <span class="text-sm">🛒+</span>
                    </button>
                </form>
            </div>
        @else
            <button type="button"
                    disabled
                    class="w-full py-2.5 px-3 rounded-xl font-bold text-xs text-slate-400 bg-slate-100 border border-slate-200 cursor-not-allowed">
                Unit Sedang Disewa / Habis
            </button>
        @endif
    </div>
</div>
