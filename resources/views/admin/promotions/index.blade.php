@extends('admin.layout')

@section('title', 'Kelola promo')

@section('content')
@php
    $firstPromo = $promotions->first();
@endphp

<div class="space-y-6"
     x-data="{
        mode: 'edit', // 'create' or 'edit'
        selectedId: {{ $firstPromo ? $firstPromo->id : 'null' }},
        deleteBannerModalOpen: false,
        deleteBannerReason: '',
        editor: {
            id: {{ $firstPromo ? $firstPromo->id : 'null' }},
            code: '{{ $firstPromo ? $firstPromo->code : '' }}',
            name: '{{ $firstPromo ? addslashes($firstPromo->name) : '' }}',
            quota: {{ $firstPromo ? $firstPromo->quota_total : 50 }},
            used: {{ $firstPromo ? $firstPromo->quota_used : 0 }},
            type: '{{ $firstPromo ? $firstPromo->type : 'percentage' }}',
            value: {{ $firstPromo ? (float) $firstPromo->discount_value : 10 }},
            startDate: '{{ $firstPromo ? $firstPromo->start_date->format('Y-m-d') : date('Y-m-d') }}',
            endDate: '{{ $firstPromo ? $firstPromo->end_date->format('Y-m-d') : date('Y-12-31') }}',
            minTransaction: {{ $firstPromo ? (float) $firstPromo->min_transaction : 200000 }},
            maxDiscount: '{{ $firstPromo && $firstPromo->max_discount ? (float) $firstPromo->max_discount : '' }}',
            categoryId: '{{ $firstPromo ? ($firstPromo->category_id ?? '') : '' }}',
            isActive: {{ $firstPromo && $firstPromo->is_active ? 'true' : 'false' }},
            hasImage: {{ $firstPromo && !empty($firstPromo->image) ? 'true' : 'false' }},
            imageUrl: '{{ $firstPromo && !empty($firstPromo->image) ? asset('storage/' . $firstPromo->image) : '' }}',
            imageFilename: '{{ $firstPromo && !empty($firstPromo->image) ? basename($firstPromo->image) : '' }}',
            description: '{{ $firstPromo ? addslashes($firstPromo->description ?? '') : '' }}'
        },
        resetForCreate() {
            this.mode = 'create';
            this.selectedId = null;
            this.editor = {
                id: null,
                code: '',
                name: '',
                quota: 50,
                used: 0,
                type: 'percentage',
                value: 10,
                startDate: '{{ date('Y-m-d') }}',
                endDate: '{{ date('Y-12-31') }}',
                minTransaction: 200000,
                maxDiscount: '',
                categoryId: '',
                isActive: true,
                hasImage: false,
                imageUrl: '',
                imageFilename: '',
                description: ''
            };
        }
     }">

    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-xs text-slate-500">
        <span>Admin</span>
        <span class="text-slate-300">›</span>
        <span class="text-slate-800 font-semibold">Kelola promo</span>
    </nav>

    <!-- Header -->
    <div>
        <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 font-display tracking-tight">
            Kelola promo
        </h1>
        <p class="text-slate-500 text-xs sm:text-sm mt-1">
            Atur program, masa berlaku, dan kuota voucher yang tersimpan dalam basis data MySQL.
        </p>
    </div>

    @if (session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm font-semibold flex items-center justify-between shadow-xs">
            <span>✓ {{ session('success') }}</span>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-800 font-bold">✕</button>
        </div>
    @endif

    @if ($errors->any())
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-xs sm:text-sm">
            <span class="font-bold block mb-1">Periksa isian formulir promo:</span>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- 3 KPI Metric Cards (Connected to Database) -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <!-- Program Aktif -->
        <div class="p-5 sm:p-6 rounded-3xl bg-white border border-slate-200 shadow-sm space-y-1">
            <span class="text-xs text-slate-500 font-medium block">Program aktif</span>
            <div class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-display">
                {{ $activeProgramsCount }}
            </div>
            <span class="text-[11px] text-brand-600 font-semibold block">Tampil aktif di halaman publik</span>
        </div>

        <!-- Penggunaan Terverifikasi -->
        <div class="p-5 sm:p-6 rounded-3xl bg-white border border-slate-200 shadow-sm space-y-1">
            <span class="text-xs text-slate-500 font-medium block">Penggunaan terverifikasi</span>
            <div class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-display">
                {{ $verifiedUsagesCount }} kali
            </div>
            <span class="text-[11px] text-brand-600 font-semibold block">Tercatat dalam riwayat transaksi</span>
        </div>

        <!-- Potongan Terealisasi -->
        <div class="p-5 sm:p-6 rounded-3xl bg-white border border-slate-200 shadow-sm space-y-1">
            <span class="text-xs text-slate-500 font-medium block">Potongan terealisasi</span>
            <div class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-display">
                Rp{{ number_format($totalDiscountRealized, 0, ',', '.') }}
            </div>
            <span class="text-[11px] text-brand-600 font-semibold block">Total subsidi potongan harga</span>
        </div>
    </div>

    <!-- Filters & Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <form method="GET" action="{{ route('admin.promotions.index') }}" class="grid grid-cols-1 sm:grid-cols-2 gap-4 flex-1 max-w-2xl">
            <!-- Cari Kode Promo -->
            <div class="p-3 rounded-2xl border border-slate-200 bg-white shadow-sm focus-within:border-brand-500">
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">
                    Cari kode promo
                </label>
                <input type="text"
                       name="q"
                       value="{{ request('q') }}"
                       onchange="this.form.submit()"
                       placeholder="⌕ Kode atau nama program..."
                       class="w-full bg-transparent text-xs sm:text-sm font-semibold text-slate-900 focus:outline-none">
            </div>

            <!-- Status Filter -->
            <div class="p-3 rounded-2xl border border-slate-200 bg-white shadow-sm">
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">
                    Status
                </label>
                <select name="status"
                        onchange="this.form.submit()"
                        class="w-full bg-transparent text-xs sm:text-sm font-semibold text-slate-800 focus:outline-none cursor-pointer">
                    <option value="">Semua program</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>
        </form>

        <button type="button"
                @click="resetForCreate()"
                class="px-5 py-3.5 rounded-2xl font-bold text-xs sm:text-sm text-white bg-brand-600 hover:bg-brand-700 shadow-md shadow-brand-500/20 transition self-start sm:self-auto flex items-center gap-1.5">
            <span>+ Tambah Promo Baru</span>
        </button>
    </div>

    <!-- Promo Table from Database -->
    <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm overflow-x-auto">
        <table class="w-full text-left text-xs sm:text-sm">
            <thead>
                <tr class="border-b border-slate-200 text-[11px] uppercase tracking-wider text-slate-400 font-bold">
                    <th class="pb-3">Banner</th>
                    <th class="pb-3">Kode promo</th>
                    <th class="pb-3">Potongan</th>
                    <th class="pb-3">Minimum</th>
                    <th class="pb-3">Kuota sisa / total</th>
                    <th class="pb-3">Masa berlaku</th>
                    <th class="pb-3 text-right">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($promotions as $promo)
                    <tr class="hover:bg-slate-50/70 transition cursor-pointer"
                        :class="selectedId === {{ $promo->id }} ? 'bg-blue-50/40' : ''"
                        @click="mode = 'edit';
                                selectedId = {{ $promo->id }};
                                editor = {
                                    id: {{ $promo->id }},
                                    code: '{{ $promo->code }}',
                                    name: '{{ addslashes($promo->name) }}',
                                    quota: {{ $promo->quota_total }},
                                    used: {{ $promo->quota_used }},
                                    type: '{{ $promo->type }}',
                                    value: {{ (float) $promo->discount_value }},
                                    startDate: '{{ $promo->start_date->format('Y-m-d') }}',
                                    endDate: '{{ $promo->end_date->format('Y-m-d') }}',
                                    minTransaction: {{ (float) $promo->min_transaction }},
                                    maxDiscount: '{{ $promo->max_discount ? (float) $promo->max_discount : '' }}',
                                    categoryId: '{{ $promo->category_id ?? '' }}',
                                    isActive: {{ $promo->is_active ? 'true' : 'false' }},
                                    hasImage: {{ !empty($promo->image) ? 'true' : 'false' }},
                                    imageUrl: '{{ !empty($promo->image) ? asset('storage/' . $promo->image) : '' }}',
                                    imageFilename: '{{ !empty($promo->image) ? basename($promo->image) : '' }}',
                                    description: '{{ addslashes($promo->description ?? '') }}'
                                };">
                        <td class="py-4">
                            @if(!empty($promo->image))
                                <img src="{{ asset('storage/' . $promo->image) }}"
                                     alt="Banner {{ $promo->code }}"
                                     class="w-12 h-8 object-cover rounded-lg border border-slate-200">
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-slate-100 text-slate-500">
                                    Tanpa berkas
                                </span>
                            @endif
                        </td>
                        <td class="py-4 font-mono font-bold text-slate-900">
                            {{ $promo->code }}
                        </td>
                        <td class="py-4 font-semibold text-slate-800">
                            {{ $promo->discount_label }}
                        </td>
                        <td class="py-4 text-slate-600">
                            Rp{{ number_format($promo->min_transaction, 0, ',', '.') }}
                        </td>
                        <td class="py-4 text-slate-600">
                            {{ $promo->quota_remaining }} / {{ $promo->quota_total }}
                        </td>
                        <td class="py-4 text-slate-600">
                            s/d {{ $promo->end_date->format('d M Y') }}
                        </td>
                        <td class="py-4 text-right">
                            @if($promo->is_active)
                                <span class="font-bold text-emerald-600">Aktif</span>
                            @else
                                <span class="font-bold text-slate-400">Nonaktif</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center text-slate-400 text-xs">
                            Belum ada promo yang terdaftar di basis data. Klik tombol "+ Tambah Promo Baru" di atas.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Dynamic Editor / Creator Promo Form -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-6">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <div>
                <span class="text-[10px] font-bold text-brand-600 uppercase tracking-wider block"
                      x-text="mode === 'create' ? 'MODE: TAMBAH KODE BARU' : 'MODE: PERBARUI KODE'"></span>
                <h2 class="text-xl font-bold text-slate-900 font-display"
                    x-text="mode === 'create' ? 'Buat Promo Baru' : ('Editor Promo · ' + editor.code)">
                </h2>
            </div>

            <template x-if="mode === 'edit' && editor.id">
                <form :action="'{{ url('admin/promo') }}/' + editor.id"
                      method="POST"
                      onsubmit="return confirm('Hapus promo ini secara permanen dari database?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 rounded-xl text-xs font-bold text-rose-600 bg-rose-50 hover:bg-rose-100 transition">
                        Hapus Promo
                    </button>
                </form>
            </template>
        </div>

        <form :action="mode === 'create' ? '{{ route('admin.promotions.store') }}' : ('{{ url('admin/promo') }}/' + editor.id)"
              method="POST"
              enctype="multipart/form-data"
              class="space-y-4">
            @csrf
            <template x-if="mode === 'edit'">
                <input type="hidden" name="_method" value="PUT">
            </template>

            <!-- Banner Promo Berkas -->
            <div class="p-4 rounded-2xl border border-slate-200 bg-slate-50/50 space-y-3">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                            Gambar / Banner Promo (Opsional)
                        </span>
                        <p class="text-xs text-slate-500">Format JPEG, PNG, JPG, WEBP maks. 2 MB</p>
                    </div>

                    <template x-if="mode === 'edit' && editor.hasImage">
                        <div class="flex items-center gap-2">
                            <span class="inline-block px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                Berkas Tersedia
                            </span>
                            <button type="button"
                                    @click="deleteBannerModalOpen = true"
                                    title="Hapus Banner Promo"
                                    class="px-2.5 py-1 rounded-xl text-rose-600 bg-rose-50 hover:bg-rose-100 font-bold text-xs inline-flex items-center gap-1 transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                <span>Hapus Banner</span>
                            </button>
                        </div>
                    </template>
                    <template x-if="!editor.hasImage">
                        <span class="inline-block px-2.5 py-0.5 rounded text-[11px] font-semibold bg-slate-100 text-slate-500">
                            Tanpa berkas
                        </span>
                    </template>
                </div>

                <div class="flex flex-col sm:flex-row items-center gap-4">
                    <template x-if="editor.hasImage">
                        <div class="w-32 h-20 rounded-xl overflow-hidden border border-slate-200 bg-white shrink-0">
                            <img :src="editor.imageUrl" alt="Banner Preview" class="w-full h-full object-cover">
                        </div>
                    </template>
                    <div class="flex-1 w-full">
                        <input type="file"
                               name="image"
                               accept="image/*"
                               class="w-full text-xs text-slate-600 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100 border border-slate-200 rounded-xl p-1.5 bg-white">
                    </div>
                </div>
            </div>

            <!-- Row 1: Kode promo, Nama promo, Kuota total -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="p-3.5 rounded-2xl border border-slate-200 bg-slate-50/50">
                    <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">
                        Kode promo *
                    </label>
                    <input type="text"
                           name="code"
                           x-model="editor.code"
                           required
                           placeholder="CONTOH10"
                           class="w-full bg-transparent text-sm font-bold text-slate-900 uppercase font-mono focus:outline-none">
                </div>

                <div class="p-3.5 rounded-2xl border border-slate-200 bg-slate-50/50">
                    <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">
                        Nama program *
                    </label>
                    <input type="text"
                           name="name"
                           x-model="editor.name"
                           required
                           placeholder="Promo Diskon Kreator"
                           class="w-full bg-transparent text-sm font-bold text-slate-900 focus:outline-none">
                </div>

                <div class="p-3.5 rounded-2xl border border-slate-200 bg-slate-50/50">
                    <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">
                        Kuota total *
                    </label>
                    <input type="number"
                           name="quota_total"
                           x-model="editor.quota"
                           required
                           min="1"
                           class="w-full bg-transparent text-sm font-bold text-slate-900 focus:outline-none">
                </div>
            </div>

            <!-- Row 2: Jenis, Nilai potongan, Mulai, Berakhir -->
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                <div class="p-3.5 rounded-2xl border border-slate-200 bg-slate-50/50">
                    <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">
                        Jenis potongan *
                    </label>
                    <select name="type"
                            x-model="editor.type"
                            class="w-full bg-transparent text-sm font-bold text-slate-900 focus:outline-none cursor-pointer">
                        <option value="percentage">Persen (%)</option>
                        <option value="fixed">Nominal Tetap (Rp)</option>
                    </select>
                </div>

                <div class="p-3.5 rounded-2xl border border-slate-200 bg-slate-50/50">
                    <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">
                        Nilai potongan *
                    </label>
                    <input type="number"
                           name="discount_value"
                           x-model="editor.value"
                           required
                           min="0"
                           step="any"
                           class="w-full bg-transparent text-sm font-bold text-slate-900 focus:outline-none">
                </div>

                <div class="p-3.5 rounded-2xl border border-slate-200 bg-slate-50/50">
                    <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">
                        Tanggal mulai *
                    </label>
                    <input type="date"
                           name="start_date"
                           x-model="editor.startDate"
                           required
                           class="w-full bg-transparent text-sm font-bold text-slate-900 focus:outline-none">
                </div>

                <div class="p-3.5 rounded-2xl border border-slate-200 bg-slate-50/50">
                    <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">
                        Tanggal berakhir *
                    </label>
                    <input type="date"
                           name="end_date"
                           x-model="editor.endDate"
                           required
                           class="w-full bg-transparent text-sm font-bold text-slate-900 focus:outline-none">
                </div>
            </div>

            <!-- Row 3: Minimum transaksi, Batas maksimal potongan -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="p-3.5 rounded-2xl border border-slate-200 bg-slate-50/50">
                    <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">
                        Minimum transaksi sewa (Rp)
                    </label>
                    <input type="number"
                           name="min_transaction"
                           x-model="editor.minTransaction"
                           min="0"
                           class="w-full bg-transparent text-sm font-bold text-slate-900 focus:outline-none">
                    <span class="block text-[11px] text-slate-400 mt-1">Subtotal setelah diskon durasi, sebelum promo diterapkan.</span>
                </div>

                <div class="p-3.5 rounded-2xl border border-slate-200 bg-slate-50/50">
                    <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">
                        Batas maksimal potongan Rp (opsional jika persen)
                    </label>
                    <input type="number"
                           name="max_discount"
                           x-model="editor.maxDiscount"
                           min="0"
                           placeholder="Kosongkan jika tanpa batas"
                           class="w-full bg-transparent text-sm font-bold text-slate-900 focus:outline-none">
                </div>
            </div>

            <!-- Row 4: Kategori berlaku & Aktif Checkbox -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="p-3.5 rounded-2xl border border-slate-200 bg-slate-50/50">
                    <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">
                        Kategori berlaku
                    </label>
                    <select name="category_id"
                            x-model="editor.categoryId"
                            class="w-full bg-transparent text-sm font-bold text-slate-900 focus:outline-none cursor-pointer">
                        <option value="">Semua kategori peralatan</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="p-3.5 rounded-2xl border border-slate-200 bg-slate-50/50 flex items-center">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox"
                               name="is_active"
                               value="1"
                               x-model="editor.isActive"
                               class="w-4 h-4 text-brand-600 rounded border-slate-300">
                        <span class="text-xs font-bold text-slate-800">Aktif dan dapat digunakan pada saat checkout</span>
                    </label>
                </div>
            </div>

            <!-- Keterangan / Deskripsi Ringkas -->
            <div class="p-3.5 rounded-2xl border border-slate-200 bg-slate-50/50">
                <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">
                    Deskripsi / Syarat ringkas voucher
                </label>
                <textarea rows="2"
                          name="description"
                          x-model="editor.description"
                          placeholder="Tuliskan syarat dan ketentuan ringkas voucher..."
                          class="w-full bg-transparent text-xs text-slate-700 leading-relaxed focus:outline-none resize-none"></textarea>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center gap-3 pt-2">
                <button type="submit"
                        class="px-6 py-3 rounded-xl font-bold text-xs sm:text-sm text-white bg-brand-600 hover:bg-brand-700 shadow-md shadow-brand-500/20 transition">
                    <span x-text="mode === 'create' ? 'Simpan Promo Baru' : 'Simpan Perubahan Promo'"></span>
                </button>

                <button type="button"
                        @click="resetForCreate()"
                        class="px-5 py-3 rounded-xl font-bold text-xs sm:text-sm text-slate-700 bg-white hover:bg-slate-50 border border-slate-200 transition">
                    Batal / Bersihkan Form
                </button>
            </div>
        </form>
    </div>

    <!-- Modal Konfirmasi Hapus Banner Promo -->
    <div x-show="deleteBannerModalOpen"
         class="fixed inset-0 z-50 bg-black/60 backdrop-blur-xs flex items-center justify-center p-4"
         @click="deleteBannerModalOpen = false"
         style="display: none;">
        <div class="bg-white rounded-3xl p-6 sm:p-7 max-w-md w-full space-y-4 shadow-xl border border-slate-200" @click.stop>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-rose-50 border border-rose-200 text-rose-600 flex items-center justify-center font-bold text-lg shrink-0">
                    🎟️
                </div>
                <div>
                    <h3 class="text-base font-extrabold text-slate-900 font-display">
                        Hapus Gambar Banner Promo?
                    </h3>
                    <p class="text-xs text-slate-500">
                        Hanya file banner yang dihapus, kode promo dan diskon tetap aktif.
                    </p>
                </div>
            </div>

            <div class="bg-slate-50 rounded-2xl p-3.5 border border-slate-200 text-xs space-y-1.5">
                <div class="flex justify-between">
                    <span class="text-slate-500">Kode Promo:</span>
                    <span class="font-mono font-bold text-slate-800" x-text="editor.code"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Nama File:</span>
                    <span class="font-mono text-slate-700 truncate max-w-[200px]" x-text="editor.imageFilename || 'Banner Promo'"></span>
                </div>
                <div class="pt-1 text-[11px] text-amber-700 font-medium">
                    ℹ️ Kode promo, besaran potongan harga, kuota, dan masa berlaku promo tetap berjalan seperti biasa.
                </div>
            </div>

            <form method="POST" :action="'{{ url('admin/promo') }}/' + editor.id + '/gambar'">
                @csrf
                @method('DELETE')

                <div>
                    <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-1">
                        Alasan Penghapusan (Audit Log)
                    </label>
                    <input type="text"
                           name="reason"
                           x-model="deleteBannerReason"
                           placeholder="Contoh: Pembaruan materi promosi..."
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500">
                </div>

                <div class="flex items-center justify-end gap-2 pt-4">
                    <button type="button"
                            @click="deleteBannerModalOpen = false"
                            class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-bold text-xs hover:bg-slate-50 transition">
                        Batal
                    </button>
                    <button type="submit"
                            class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shadow-md shadow-rose-600/20 transition">
                        Hapus Banner
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
