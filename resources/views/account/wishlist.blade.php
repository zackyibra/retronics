<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wishlist Barang Favorit | Retronics</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Space+Grotesk:wght@700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        display: ['"Space Grotesk"', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            500: '#2563eb',
                            600: '#1d4ed8',
                            700: '#1e40af',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-slate-50 text-slate-800 antialiased min-h-screen flex flex-col justify-between">

    <!-- Navbar -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <a href="{{ route('home') }}" class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-brand-600 text-white flex items-center justify-center font-bold text-xl shadow-md">
                        📷
                    </div>
                    <div>
                        <span class="font-display text-2xl font-bold tracking-tight text-slate-900 block">Retronics</span>
                        <span class="text-[10px] uppercase tracking-widest font-semibold text-slate-500 -mt-1 block">Wishlist Favorit</span>
                    </div>
                </a>

                <div class="flex items-center gap-4 text-xs font-semibold">
                    <a href="{{ route('products.index') }}" class="text-slate-600 hover:text-brand-600">
                        ← Jelajahi Katalog
                    </a>
                    <a href="{{ route('account.index') }}" class="px-3.5 py-2 rounded-xl bg-blue-50 text-brand-600 font-bold">
                        Akun Saya
                    </a>
                </div>
            </div>
        </div>
    </header>

    <main class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10 w-full flex-grow">

        <div class="mb-8">
            <span class="text-xs font-bold uppercase tracking-widest text-rose-600 bg-rose-50 px-3 py-1 rounded-full">
                Koleksi Favorit
            </span>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-display mt-2">
                Wishlist Perlengkapan Fotografi Anda
            </h1>
            <p class="text-slate-500 text-xs sm:text-sm mt-1">
                Daftar peralatan kamera yang Anda simpan untuk proyek fotografi atau video mendatang.
            </p>
        </div>

        @if (session('success'))
            <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm flex items-center justify-between">
                <span>✓ {{ session('success') }}</span>
                <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900">✕</button>
            </div>
        @endif

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @forelse ($wishlistItems as $item)
                @php $product = $item->product; @endphp
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition flex flex-col justify-between overflow-hidden">
                    <div>
                        <div class="relative bg-slate-50 p-6 h-44 flex items-center justify-center">
                            @if ($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}"
                                     alt="{{ $product->name }}"
                                     class="max-h-36 max-w-full object-contain"
                                     onerror="this.onerror=null; this.src='{{ asset('images/products/' . $product->image) }}';">
                            @else
                                <span class="text-5xl">📷</span>
                            @endif

                            <form method="POST" action="{{ route('wishlist.toggle', $product->id) }}" class="absolute top-3 right-3">
                                @csrf
                                <button type="submit" title="Hapus dari Wishlist" class="w-8 h-8 rounded-full bg-white shadow-sm flex items-center justify-center text-rose-500 hover:bg-rose-50 transition">
                                    ✕
                                </button>
                            </form>
                        </div>

                        <div class="p-4">
                            <span class="text-[10px] font-bold text-brand-600 bg-blue-50 px-2 py-0.5 rounded-full">
                                {{ $product->category->name }}
                            </span>
                            <a href="{{ route('products.show', $product->slug) }}">
                                <h3 class="font-bold text-slate-900 text-sm mt-1.5 hover:text-brand-600 transition line-clamp-1">
                                    {{ $product->name }}
                                </h3>
                            </a>
                            <div class="text-base font-black text-brand-600 font-display mt-2">
                                Rp{{ number_format($product->price_per_day, 0, ',', '.') }}
                                <span class="text-[10px] text-slate-400 font-normal">/ hari</span>
                            </div>
                        </div>
                    </div>

                    <div class="p-4 pt-0 grid grid-cols-2 gap-2">
                        <a href="{{ route('products.show', $product->slug) }}" class="py-2 text-center text-xs font-bold text-slate-700 bg-slate-100 rounded-xl hover:bg-slate-200 transition">
                            Detail
                        </a>
                        <a href="{{ route('rentals.create', ['product_id' => $product->id]) }}" class="py-2 text-center text-xs font-bold text-white bg-brand-600 rounded-xl hover:bg-brand-700 transition">
                            Sewa
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-16 text-center bg-white rounded-3xl border border-dashed border-slate-300">
                    <span class="text-5xl block mb-3">❤️</span>
                    <h3 class="text-base font-bold text-slate-800">Wishlist Anda Masih Kosong</h3>
                    <p class="text-slate-500 text-xs mt-1 max-w-sm mx-auto">
                        Simpan kamera atau perlengkapan favorit Anda dari katalog untuk memudahkan pemesanan nanti.
                    </p>
                    <a href="{{ route('products.index') }}" class="inline-block mt-4 px-5 py-2.5 bg-brand-600 text-white rounded-xl text-xs font-bold hover:bg-brand-700 transition">
                        Buka Katalog Kamera
                    </a>
                </div>
            @endforelse
        </div>

    </main>

    <footer class="bg-white border-t border-slate-200 py-6 text-center text-xs text-slate-500">
        &copy; {{ date('Y') }} Retronics. Rental Perlengkapan Fotografi & Kamera Profesional.
    </footer>

</body>
</html>
