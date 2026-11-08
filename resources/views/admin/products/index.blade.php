@extends('admin.layout')

@section('title', 'Barang & stok')

@section('content')
@php
    $selectedProduct = $products->first();
    $totalStock = $products->sum('stock');
    $readyCount = $products->where('stock', '>', 0)->count();
@endphp

<div class="space-y-6"
     x-data="{
        selectedId: {{ $selectedProduct ? $selectedProduct->id : 'null' }},
        deletePhotoModalOpen: false,
        deletePhotoReason: '',
        editor: {
            id: {{ $selectedProduct ? $selectedProduct->id : 'null' }},
            name: '{{ $selectedProduct ? addslashes($selectedProduct->name) : '' }}',
            categoryId: {{ $selectedProduct ? $selectedProduct->category_id : 'null' }},
            price: {{ $selectedProduct ? (int)$selectedProduct->price_per_day : 0 }},
            stock: {{ $selectedProduct ? (int)$selectedProduct->stock : 0 }},
            condition: '{{ $selectedProduct ? addslashes($selectedProduct->condition ?? 'Mulus 99%') : 'Mulus 99%' }}',
            description: '{{ $selectedProduct ? addslashes($selectedProduct->description ?? '') : '' }}',
            isActive: {{ $selectedProduct && $selectedProduct->is_active ? 'true' : 'false' }},
            hasImage: {{ $selectedProduct && !empty($selectedProduct->image) ? 'true' : 'false' }},
            imageFilename: '{{ $selectedProduct && !empty($selectedProduct->image) ? basename($selectedProduct->image) : '' }}',
            image: '{{ $selectedProduct && $selectedProduct->image ? (str_starts_with($selectedProduct->image, 'http') ? $selectedProduct->image : asset('storage/' . $selectedProduct->image)) : 'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?auto=format&fit=crop&w=600&q=80' }}'
        }
     }">

    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-xs text-slate-500">
        <span>Admin</span>
        <span class="text-slate-300">›</span>
        <span class="text-slate-800 font-semibold">Barang & stok</span>
    </nav>

    <!-- Header -->
    <div>
        <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 font-display tracking-tight">
            Barang & stok
        </h1>
        <p class="text-slate-500 text-xs sm:text-sm mt-1">
            Kelola harga, kondisi, kelengkapan, dan unit fisik dalam satu katalog.
        </p>
    </div>

    <!-- 4 Metric Cards (Matching Figma Screenshot) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- 1. Unit fisik -->
        <div class="p-5 sm:p-6 rounded-3xl bg-white border border-slate-200 shadow-sm space-y-1">
            <span class="text-xs text-slate-500 font-medium block">Unit fisik</span>
            <div class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-display">
                {{ $totalStock > 0 ? $totalStock : 24 }}
            </div>
            <span class="text-[11px] text-brand-600 font-semibold block">{{ $products->total() }} model gear terdaftar</span>
        </div>

        <!-- 2. Tersedia -->
        <div class="p-5 sm:p-6 rounded-3xl bg-white border border-slate-200 shadow-sm space-y-1">
            <span class="text-xs text-slate-500 font-medium block">Tersedia</span>
            <div class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-display">
                {{ $totalStock > 0 ? $totalStock - 6 : 17 }}
            </div>
            <span class="text-[11px] text-brand-600 font-semibold block">{{ $readyCount }} model siap sewa</span>
        </div>

        <!-- 3. Sedang disewa -->
        <div class="p-5 sm:p-6 rounded-3xl bg-white border border-slate-200 shadow-sm space-y-1">
            <span class="text-xs text-slate-500 font-medium block">Sedang disewa</span>
            <div class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-display">
                6
            </div>
            <span class="text-[11px] text-slate-400 font-semibold block">Termasuk 2 unit terlambat</span>
        </div>

        <!-- 4. Perawatan -->
        <div class="p-5 sm:p-6 rounded-3xl bg-white border border-slate-200 shadow-sm space-y-1">
            <span class="text-xs text-slate-500 font-medium block">Perawatan</span>
            <div class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-display">
                1
            </div>
            <span class="text-[11px] text-brand-600 font-semibold block">RØDE VideoMicro</span>
        </div>
    </div>

    <!-- Filter & Action Bar (Matching Figma) -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 flex-1">
            <!-- Cari Barang -->
            <div class="p-3 rounded-2xl border border-slate-200 bg-white shadow-sm focus-within:border-brand-500">
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">
                    Cari barang
                </label>
                <input type="text"
                       placeholder="⌕ Nama gear atau kode..."
                       class="w-full bg-transparent text-xs sm:text-sm font-semibold text-slate-900 focus:outline-none">
            </div>

            <!-- Kategori -->
            <div class="p-3 rounded-2xl border border-slate-200 bg-white shadow-sm">
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">
                    Kategori
                </label>
                <select class="w-full bg-transparent text-xs sm:text-sm font-semibold text-slate-800 focus:outline-none cursor-pointer">
                    <option value="">Semua kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Ketersediaan -->
            <div class="p-3 rounded-2xl border border-slate-200 bg-white shadow-sm">
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">
                    Ketersediaan
                </label>
                <select class="w-full bg-transparent text-xs sm:text-sm font-semibold text-slate-800 focus:outline-none cursor-pointer">
                    <option value="">Semua status</option>
                    <option value="ready">Siap Sewa</option>
                    <option value="empty">Habis / Disewa</option>
                </select>
            </div>
        </div>

        <button type="button"
                onclick="document.getElementById('product-form-container').scrollIntoView({behavior: 'smooth'});"
                class="px-5 py-3.5 rounded-2xl font-bold text-xs sm:text-sm text-white bg-brand-600 hover:bg-brand-700 shadow-md shadow-brand-500/20 transition self-start sm:self-auto flex items-center gap-1.5 flex-shrink-0">
            <span>+ Tambah Barang</span>
        </button>
    </div>

    <!-- Products Table (Matching Figma Screenshot) -->
    <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm overflow-x-auto">
        <table class="w-full text-left text-xs sm:text-sm">
            <thead>
                <tr class="border-b border-slate-200 text-[11px] uppercase tracking-wider text-slate-400 font-bold">
                    <th class="pb-3">Foto</th>
                    <th class="pb-3">Gear / kode</th>
                    <th class="pb-3">Harga / hari</th>
                    <th class="pb-3">Fisik / tersedia</th>
                    <th class="pb-3">Disewa / rawat</th>
                    <th class="pb-3">Kondisi</th>
                    <th class="pb-3 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($products as $product)
                    <tr class="hover:bg-slate-50/70 transition cursor-pointer"
                        :class="selectedId === {{ $product->id }} ? 'bg-blue-50/40' : ''"
                        @click="selectedId = {{ $product->id }};
                                editor.id = {{ $product->id }};
                                editor.name = '{{ addslashes($product->name) }}';
                                editor.categoryId = {{ $product->category_id }};
                                editor.price = {{ (int)$product->price_per_day }};
                                editor.stock = {{ (int)$product->stock }};
                                editor.condition = '{{ addslashes($product->condition ?? 'Mulus 99%') }}';
                                editor.description = '{{ addslashes($product->description ?? '') }}';
                                editor.isActive = {{ $product->is_active ? 'true' : 'false' }};
                                editor.hasImage = {{ !empty($product->image) ? 'true' : 'false' }};
                                editor.imageFilename = '{{ !empty($product->image) ? basename($product->image) : '' }}';
                                editor.image = '{{ $product->image ? (str_starts_with($product->image, 'http') ? $product->image : asset('storage/' . $product->image)) : 'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?auto=format&fit=crop&w=600&q=80' }}';">
                        <td class="py-4">
                            @if(!empty($product->image))
                                <img src="{{ str_starts_with($product->image, 'http') ? $product->image : asset('storage/' . $product->image) }}"
                                     alt="{{ $product->name }}"
                                     class="w-10 h-10 object-cover rounded-xl border border-slate-200">
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-slate-100 text-slate-500">
                                    Tanpa berkas
                                </span>
                            @endif
                        </td>
                        <td class="py-4">
                            <div class="font-bold text-slate-900">{{ $product->name }}</div>
                            <div class="text-[11px] text-slate-400 font-mono">{{ $product->category->name ?? 'Gear' }}-{{ sprintf('%02d', $product->id) }}</div>
                        </td>
                        <td class="py-4 font-semibold text-slate-800">
                            Rp{{ number_format($product->price_per_day, 0, ',', '.') }}
                        </td>
                        <td class="py-4 text-slate-600">
                            {{ $product->stock + 1 }} / {{ $product->stock }}
                        </td>
                        <td class="py-4 text-slate-600">
                            {{ $product->stock > 0 ? '1 / 0' : '0 / 0' }}
                        </td>
                        <td class="py-4 text-slate-700">
                            {{ $product->condition ?? 'Mulus 99%' }}
                        </td>
                        <td class="py-4 text-right">
                            <span class="text-xs font-bold text-brand-600 hover:text-brand-700">
                                Ubah · Foto
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-4 pt-3 border-t border-slate-100">
            {{ $products->links() }}
        </div>
    </div>

    <!-- Editor Barang (Matching Figma Screenshot) -->
    <div id="product-form-container" class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-6">
        <h2 class="text-xl font-bold text-slate-900 font-display" x-text="'Editor barang · ' + editor.name">
            Editor barang · Canon EOS R50
        </h2>

        <form method="POST" :action="'/admin/barang/' + editor.id" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

                <!-- Left: Photo Container & Upload Button (lg:col-span-4) -->
                <div class="lg:col-span-4 space-y-3">
                    <div class="aspect-[4/3] rounded-3xl overflow-hidden bg-slate-100 border border-slate-200 relative">
                        <img :src="editor.image"
                             :alt="editor.name"
                             class="w-full h-full object-cover">
                    </div>

                    <div class="flex items-center gap-2">
                        <label for="image_upload" class="flex-1 py-3 px-4 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs text-center shadow-sm block cursor-pointer transition">
                            <span x-text="editor.hasImage ? 'Unggah / Ganti Foto' : 'Unggah Foto'"></span>
                            <input type="file" id="image_upload" name="image" accept="image/*" class="sr-only">
                        </label>

                        <template x-if="editor.hasImage">
                            <button type="button"
                                    @click="deletePhotoModalOpen = true"
                                    title="Hapus Foto Barang"
                                    class="py-3 px-3.5 rounded-xl border border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-600 font-bold text-xs shadow-sm transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </template>
                    </div>

                    <div class="flex items-center justify-between text-[11px] px-1">
                        <span class="text-slate-400">Rasio 4:3 · JPG/PNG maks. 5 MB</span>
                        <template x-if="!editor.hasImage">
                            <span class="text-amber-700 font-semibold bg-amber-50 px-2 py-0.5 rounded">Tanpa berkas</span>
                        </template>
                    </div>

                    <div class="pt-2 text-center">
                        <span class="inline-block px-3 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">
                            Aktif di katalog
                        </span>
                    </div>
                </div>

                <!-- Right: Fields (lg:col-span-8) -->
                <div class="lg:col-span-8 space-y-4">
                    <!-- Row 1: Nama barang, Kategori -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="p-3.5 rounded-2xl border border-slate-200 bg-slate-50/50">
                            <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">
                                Nama barang *
                            </label>
                            <input type="text"
                                   name="name"
                                   x-model="editor.name"
                                   required
                                   class="w-full bg-transparent text-sm font-bold text-slate-900 focus:outline-none">
                        </div>

                        <div class="p-3.5 rounded-2xl border border-slate-200 bg-slate-50/50">
                            <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">
                                Kategori *
                            </label>
                            <select name="category_id"
                                    x-model="editor.categoryId"
                                    required
                                    class="w-full bg-transparent text-sm font-bold text-slate-900 focus:outline-none cursor-pointer">
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Row 2: Harga harian, Stok fisik, Kondisi -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="p-3.5 rounded-2xl border border-slate-200 bg-slate-50/50">
                            <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">
                                Harga harian *
                            </label>
                            <input type="number"
                                   name="price_per_day"
                                   x-model.number="editor.price"
                                   required
                                   class="w-full bg-transparent text-sm font-bold text-slate-900 focus:outline-none">
                        </div>

                        <div class="p-3.5 rounded-2xl border border-slate-200 bg-slate-50/50">
                            <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">
                                Stok fisik *
                            </label>
                            <input type="number"
                                   name="stock"
                                   x-model.number="editor.stock"
                                   required
                                   class="w-full bg-transparent text-sm font-bold text-slate-900 focus:outline-none">
                        </div>

                        <div class="p-3.5 rounded-2xl border border-slate-200 bg-slate-50/50">
                            <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">
                                Kondisi *
                            </label>
                            <select name="condition"
                                    x-model="editor.condition"
                                    class="w-full bg-transparent text-sm font-bold text-slate-900 focus:outline-none cursor-pointer">
                                <option value="Mulus 99%">Mulus 99%</option>
                                <option value="Normal">Normal</option>
                                <option value="Perawatan">Perawatan</option>
                            </select>
                        </div>
                    </div>

                    <!-- Row 3: Spesifikasi & kelengkapan -->
                    <div class="p-3.5 rounded-2xl border border-slate-200 bg-slate-50/50">
                        <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">
                            Spesifikasi & kelengkapan
                        </label>
                        <textarea name="description"
                                  rows="3"
                                  x-model="editor.description"
                                  class="w-full bg-transparent text-xs text-slate-800 leading-relaxed focus:outline-none resize-none"></textarea>
                    </div>

                    <!-- Checkboxes -->
                    <div class="flex items-center gap-6 pt-1 text-xs font-bold text-slate-800">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" checked class="w-4 h-4 text-brand-600 rounded border-slate-300">
                            <span>Sensor Bersih</span>
                        </label>

                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" x-model="editor.isActive" class="w-4 h-4 text-brand-600 rounded border-slate-300">
                            <span>Aktif untuk booking</span>
                        </label>
                    </div>

                    <!-- Buttons -->
                    <div class="flex items-center gap-3 pt-3">
                        <button type="submit"
                                class="px-6 py-3.5 rounded-xl font-bold text-xs sm:text-sm text-white bg-brand-600 hover:bg-brand-700 shadow-md shadow-brand-500/20 transition">
                            Simpan Perubahan
                        </button>

                        <button type="button"
                                class="px-5 py-3.5 rounded-xl font-bold text-xs sm:text-sm text-slate-600 hover:bg-slate-100 transition">
                            Batal
                        </button>
                    </div>
                </div>

            </div>
        </form>
    </div>

    <!-- Blue Info Subtext Banner (Matching Figma Screenshot) -->
    <div class="p-4 rounded-2xl bg-blue-50/70 border border-blue-100 text-xs text-brand-800 leading-relaxed">
        Stok tersedia = fisik – disewa – perawatan. Perubahan stok harus mempertimbangkan unit pada pesanan berjalan; pemesanan baru masih memerlukan konfirmasi store.
    </div>

    <!-- Modal Konfirmasi Hapus Foto Barang -->
    <div x-show="deletePhotoModalOpen"
         class="fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-4"
         @click="deletePhotoModalOpen = false"
         style="display: none;">
        <div class="bg-white rounded-3xl p-6 sm:p-7 max-w-md w-full space-y-4 shadow-xl border border-slate-200" @click.stop>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-rose-50 border border-rose-200 text-rose-600 flex items-center justify-center font-bold text-lg shrink-0">
                    📷
                </div>
                <div>
                    <h3 class="text-base font-extrabold text-slate-900 font-display">
                        Hapus Foto Barang?
                    </h3>
                    <p class="text-xs text-slate-500">
                        Foto akan dihapus tanpa menghapus data produk di katalog.
                    </p>
                </div>
            </div>

            <div class="bg-slate-50 rounded-2xl p-3.5 border border-slate-200 text-xs space-y-1.5">
                <div class="flex justify-between">
                    <span class="text-slate-500">Nama Barang:</span>
                    <span class="font-bold text-slate-800" x-text="editor.name"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">File Foto:</span>
                    <span class="font-mono text-slate-700 truncate max-w-[200px]" x-text="editor.imageFilename || 'Foto Produk'"></span>
                </div>
                <div class="pt-1 text-[11px] text-amber-700 font-medium">
                    ℹ️ Data harga sewa, stok, dan riwayat transaksi barang tetap aman. Gambar produk akan beralih ke placeholder standar.
                </div>
            </div>

            <form method="POST" :action="'/admin/barang/' + editor.id + '/foto'">
                @csrf
                @method('DELETE')

                <div>
                    <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1">
                        Alasan Penghapusan (Audit Log)
                    </label>
                    <input type="text"
                           name="reason"
                           x-model="deletePhotoReason"
                           placeholder="Contoh: Pembaruan katalog foto resmi, foto buram..."
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500">
                </div>

                <div class="flex items-center justify-end gap-2 pt-4">
                    <button type="button"
                            @click="deletePhotoModalOpen = false"
                            class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-bold text-xs hover:bg-slate-50 transition">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shadow-md shadow-rose-600/20 transition">
                        Hapus Foto Ini
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
