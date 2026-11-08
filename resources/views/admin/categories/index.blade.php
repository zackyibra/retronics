@extends('admin.layout')

@section('title', 'Kategori gear')

@section('content')
@php
    $selectedCategory = $categories->first();
    $totalCategories = $categories->count();
    $totalModels = $categories->sum('products_count');
@endphp

<div class="space-y-6"
     x-data="{
        selectedId: {{ $selectedCategory ? $selectedCategory->id : 'null' }},
        editor: {
            id: {{ $selectedCategory ? $selectedCategory->id : 'null' }},
            name: '{{ $selectedCategory ? addslashes($selectedCategory->name) : '' }}',
            slug: '{{ $selectedCategory ? addslashes($selectedCategory->slug) : '' }}',
            description: '{{ $selectedCategory ? addslashes($selectedCategory->description ?? '') : '' }}',
            isActive: true,
            isEdit: true
        },
        selectCategory(cat) {
            this.selectedId = cat.id;
            this.editor.id = cat.id;
            this.editor.name = cat.name;
            this.editor.slug = cat.slug;
            this.editor.description = cat.description || '';
            this.editor.isEdit = true;
        },
        newCategory() {
            this.selectedId = null;
            this.editor.id = null;
            this.editor.name = '';
            this.editor.slug = '';
            this.editor.description = '';
            this.editor.isEdit = false;
        }
     }">

    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-xs text-slate-500">
        <span>Admin</span>
        <span class="text-slate-300">›</span>
        <span class="text-slate-800 font-semibold">Kategori gear</span>
    </nav>

    <!-- Header -->
    <div>
        <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 font-display tracking-tight">
            Kategori gear
        </h1>
        <p class="text-slate-500 text-xs sm:text-sm mt-1">
            Susun katalog agar pelanggan mudah menemukan perlengkapan yang tepat.
        </p>
    </div>

    <!-- 3 Metric Cards (Matching Figma Screenshot) -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <!-- 1. Kategori aktif -->
        <div class="p-5 sm:p-6 rounded-3xl bg-white border border-slate-200 shadow-sm space-y-1">
            <span class="text-xs text-slate-500 font-medium block">Kategori aktif</span>
            <div class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-display">
                {{ $totalCategories }}
            </div>
            <span class="text-[11px] text-brand-600 font-semibold block">Tampil di filter katalog</span>
        </div>

        <!-- 2. Model gear -->
        <div class="p-5 sm:p-6 rounded-3xl bg-white border border-slate-200 shadow-sm space-y-1">
            <span class="text-xs text-slate-500 font-medium block">Model gear</span>
            <div class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-display">
                {{ $totalModels }}
            </div>
            <span class="text-[11px] text-slate-400 font-semibold block">{{ $totalModels }} model terdaftar siap sewa</span>
        </div>

        <!-- 3. Kategori terpilih -->
        <div class="p-5 sm:p-6 rounded-3xl bg-white border border-slate-200 shadow-sm space-y-1">
            <span class="text-xs text-slate-500 font-medium block">Kategori terpilih</span>
            <div class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-display truncate" x-text="editor.name || 'Semua'">
                {{ $selectedCategory ? $selectedCategory->name : 'Kamera' }}
            </div>
            <span class="text-[11px] text-brand-600 font-semibold block">
                {{ $selectedCategory ? $selectedCategory->products_count . ' model aktif' : 'Pilih kategori' }}
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        <!-- LEFT COLUMN: Search & Table (lg:col-span-8) -->
        <div class="lg:col-span-8 space-y-4">

            <!-- Search bar with + Tambah button -->
            <div class="flex items-center gap-3">
                <div class="p-3 rounded-2xl border border-slate-200 bg-white shadow-sm flex-1 focus-within:border-brand-500">
                    <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">
                        Cari kategori
                    </label>
                    <input type="text"
                           placeholder="⌕ Nama kategori..."
                           class="w-full bg-transparent text-xs sm:text-sm font-semibold text-slate-900 focus:outline-none">
                </div>

                <button type="button"
                        @click="newCategory()"
                        class="px-5 py-3.5 rounded-2xl font-bold text-xs sm:text-sm text-white bg-brand-600 hover:bg-brand-700 shadow-md shadow-brand-500/20 transition flex-shrink-0">
                    + Tambah
                </button>
            </div>

            <!-- Categories Table -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm overflow-x-auto">
                <table class="w-full text-left text-xs sm:text-sm">
                    <thead>
                        <tr class="border-b border-slate-200 text-[11px] uppercase tracking-wider text-slate-400 font-bold">
                            <th class="pb-3">Kategori</th>
                            <th class="pb-3">Slug</th>
                            <th class="pb-3">Model</th>
                            <th class="pb-3">Status</th>
                            <th class="pb-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($categories as $cat)
                            <tr class="hover:bg-slate-50/70 transition cursor-pointer"
                                :class="selectedId === {{ $cat->id }} ? 'bg-blue-50/40' : ''"
                                @click="selectCategory({
                                    id: {{ $cat->id }},
                                    name: '{{ addslashes($cat->name) }}',
                                    slug: '{{ addslashes($cat->slug) }}',
                                    description: '{{ addslashes($cat->description ?? '') }}'
                                })">
                                <td class="py-4 font-bold text-slate-900">
                                    {{ $cat->name }}
                                </td>
                                <td class="py-4 font-mono text-slate-500 text-xs">
                                    {{ $cat->slug }}
                                </td>
                                <td class="py-4 font-semibold text-slate-700">
                                    {{ $cat->products_count }}
                                </td>
                                <td class="py-4">
                                    <span class="font-bold text-emerald-600">Aktif</span>
                                </td>
                                <td class="py-4 text-right">
                                    <span class="text-xs font-bold text-brand-600 hover:text-brand-700 mr-2">
                                        Ubah
                                    </span>
                                    @if($cat->products_count === 0)
                                        <form method="POST" action="{{ route('admin.categories.destroy', $cat->id) }}" class="inline" onsubmit="return confirm('Hapus kategori ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs font-bold text-rose-500 hover:text-rose-700">Hapus</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <p class="text-[11px] text-slate-400">
                {{ $totalCategories }} kategori · jumlah model berbeda dari jumlah unit fisik. Kategori dengan barang terkait tidak dapat dihapus untuk melindungi integritas transaksi.
            </p>
        </div>

        <!-- RIGHT COLUMN: Ubah / Tambah Kategori Card (lg:col-span-4) -->
        <div class="lg:col-span-4 bg-white p-6 sm:p-7 rounded-3xl border border-slate-200 shadow-sm space-y-5">
            <div>
                <h3 class="text-lg font-bold text-slate-900 font-display" x-text="editor.isEdit ? 'Ubah kategori' : 'Tambah kategori baru'">
                    Ubah kategori
                </h3>
                <div class="mt-2" x-show="editor.isEdit">
                    <span class="inline-block px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-brand-50 text-brand-700" x-text="editor.name + ' dipilih'">
                        Kamera dipilih
                    </span>
                </div>
            </div>

            <form :method="'POST'" :action="editor.isEdit ? '/admin/kategori/' + editor.id : '{{ route('admin.categories.store') }}'" class="space-y-4">
                @csrf
                <template x-if="editor.isEdit">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <!-- Nama Kategori -->
                <div class="p-3.5 rounded-2xl border border-slate-200 bg-slate-50/50 focus-within:border-brand-500">
                    <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">
                        Nama kategori *
                    </label>
                    <input type="text"
                           name="name"
                           x-model="editor.name"
                           required
                           class="w-full bg-transparent text-sm font-bold text-slate-900 focus:outline-none">
                </div>

                <!-- Slug -->
                <div class="p-3.5 rounded-2xl border border-slate-200 bg-slate-50/50 focus-within:border-brand-500">
                    <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">
                        Slug *
                    </label>
                    <input type="text"
                           name="slug"
                           x-model="editor.slug"
                           class="w-full bg-transparent text-xs font-mono font-semibold text-slate-700 focus:outline-none"
                           placeholder="Otomatis dari nama">
                </div>

                <!-- Deskripsi -->
                <div class="p-3.5 rounded-2xl border border-slate-200 bg-slate-50/50 focus-within:border-brand-500">
                    <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">
                        Deskripsi
                    </label>
                    <textarea name="description"
                              rows="3"
                              x-model="editor.description"
                              placeholder="Deskripsi peralatan dalam kategori ini"
                              class="w-full bg-transparent text-xs text-slate-800 leading-relaxed focus:outline-none resize-none"></textarea>
                </div>

                <!-- Aktif Checkbox -->
                <div class="pt-1">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" checked class="w-4 h-4 text-brand-600 rounded border-slate-300">
                        <span class="text-xs font-bold text-slate-800">Aktif di katalog</span>
                    </label>
                </div>

                <!-- Buttons -->
                <div class="space-y-2 pt-2">
                    <button type="submit"
                            class="w-full py-3.5 px-4 rounded-xl font-bold text-xs sm:text-sm text-white bg-brand-600 hover:bg-brand-700 shadow-md shadow-brand-500/20 transition">
                        Simpan Kategori
                    </button>

                    <button type="button"
                            @click="newCategory()"
                            class="w-full py-3 px-4 rounded-xl font-bold text-xs text-slate-700 bg-white hover:bg-slate-50 border border-slate-200 transition">
                        Batal
                    </button>
                </div>
            </form>

            <!-- Blue Subtext Note (Matching Figma Screenshot) -->
            <div class="p-4 rounded-2xl bg-blue-50/70 border border-blue-100 text-xs text-brand-800 leading-relaxed">
                Perubahan nama tidak mengubah stok atau pesanan yang sudah tercatat.
            </div>
        </div>

    </div>

</div>
@endsection
