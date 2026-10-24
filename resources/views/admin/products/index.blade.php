@extends('admin.layout')

@section('title', 'Kelola Barang & Stok')

@section('content')
<div class="space-y-8">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <span class="text-xs font-bold uppercase tracking-wider text-indigo-600 bg-indigo-50 px-3 py-1 rounded-full">
                Pilar 2 Admin
            </span>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-display mt-2">
                Kelola Barang & Kelola Stok
            </h1>
            <p class="text-slate-500 text-xs sm:text-sm mt-1">
                Sesuai flowchart admin: Tambah gear baru, update stok barang, atur kondisi alat, dan hapus inventaris.
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

        <!-- Form Tambah Barang (4 Cols) -->
        <div class="lg:col-span-4 bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
            <h2 class="text-base font-bold text-slate-900 mb-4 pb-2 border-b border-slate-100">
                + Tambah Barang Baru
            </h2>

            <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data" class="space-y-3.5">
                @csrf

                <div>
                    <label for="category_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Kategori
                    </label>
                    <select id="category_id" name="category_id" required class="w-full px-3 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs focus:outline-none focus:ring-2 focus:ring-brand-500">
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Nama Barang / Kamera
                    </label>
                    <input type="text"
                           id="name"
                           name="name"
                           required
                           placeholder="Contoh: Sony Alpha A7 IV"
                           class="w-full px-3 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs focus:outline-none focus:ring-2 focus:ring-brand-500">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="price_per_day" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            Tarif Sewa (Rp)
                        </label>
                        <input type="number"
                               id="price_per_day"
                               name="price_per_day"
                               required
                               placeholder="150000"
                               min="0"
                               class="w-full px-3 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs focus:outline-none focus:ring-2 focus:ring-brand-500">
                    </div>

                    <div>
                        <label for="stock" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            Jumlah Stok Unit
                        </label>
                        <input type="number"
                               id="stock"
                               name="stock"
                               required
                               value="1"
                               min="0"
                               class="w-full px-3 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs focus:outline-none focus:ring-2 focus:ring-brand-500">
                    </div>
                </div>

                <div>
                    <label for="condition" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Kondisi Barang
                    </label>
                    <select id="condition" name="condition" required class="w-full px-3 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs focus:outline-none focus:ring-2 focus:ring-brand-500">
                        <option value="sangat baik">Sangat Baik (Sensor Bersih, Optik Normal)</option>
                        <option value="baik">Baik (Lecet Pemakaian Wajar)</option>
                        <option value="perawatan">Dalam Perawatan / Perbaikan</option>
                    </select>
                </div>

                <div>
                    <label for="description" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Deskripsi Alat
                    </label>
                    <textarea id="description"
                              name="description"
                              rows="2"
                              placeholder="Keterangan spesifikasi & kelengkapan unit"
                              class="w-full px-3 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs focus:outline-none focus:ring-2 focus:ring-brand-500"></textarea>
                </div>

                <div>
                    <label for="image" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Foto Barang (Opsional)
                    </label>
                    <input type="file"
                           id="image"
                           name="image"
                           accept="image/*"
                           class="w-full text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-2.5 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-brand-700">
                </div>

                <button type="submit" class="w-full py-2.5 px-4 rounded-xl font-bold text-xs text-white bg-brand-600 hover:bg-brand-700 shadow-sm transition">
                    Simpan Barang & Stok
                </button>
            </form>
        </div>

        <!-- Tabel Daftar Barang & Stok (8 Cols) -->
        <div class="lg:col-span-8 bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
            <h2 class="text-base font-bold text-slate-900 mb-4 pb-2 border-b border-slate-100 flex items-center justify-between">
                <span>Daftar Inventaris Barang & Stok</span>
                <span class="text-xs font-normal text-slate-400">Total: {{ $products->total() }} unit model</span>
            </h2>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-200 text-slate-400 uppercase font-bold tracking-wider">
                            <th class="py-3 px-3">Barang</th>
                            <th class="py-3 px-3">Kategori</th>
                            <th class="py-3 px-3">Tarif / Hari</th>
                            <th class="py-3 px-3">Stok Unit</th>
                            <th class="py-3 px-3">Kondisi</th>
                            <th class="py-3 px-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($products as $p)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="py-3 px-3">
                                    <span class="font-bold text-slate-900 block">{{ $p->name }}</span>
                                    <span class="text-[10px] text-slate-400">{{ Str::limit($p->description, 35) }}</span>
                                </td>
                                <td class="py-3 px-3 text-slate-600">
                                    {{ $p->category->name }}
                                </td>
                                <td class="py-3 px-3 font-bold text-brand-600">
                                    Rp{{ number_format($p->price_per_day, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-3">
                                    <form method="POST" action="{{ route('admin.products.update', $p->id) }}" class="flex items-center gap-1.5">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="category_id" value="{{ $p->category_id }}">
                                        <input type="hidden" name="name" value="{{ $p->name }}">
                                        <input type="hidden" name="price_per_day" value="{{ $p->price_per_day }}">
                                        <input type="hidden" name="condition" value="{{ $p->condition }}">
                                        <input type="number"
                                               name="stock"
                                               value="{{ $p->stock }}"
                                               min="0"
                                               class="w-14 px-2 py-1 text-xs text-center border border-slate-200 rounded-lg font-bold bg-slate-50">
                                        <button type="submit" title="Simpan perubahan stok" class="p-1 rounded bg-slate-100 hover:bg-slate-200 text-[10px] font-bold">
                                            ✓
                                        </button>
                                    </form>
                                </td>
                                <td class="py-3 px-3">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $p->condition === 'perawatan' ? 'bg-amber-100 text-amber-800' : 'bg-emerald-50 text-emerald-700' }}">
                                        {{ ucfirst($p->condition) }}
                                    </span>
                                </td>
                                <td class="py-3 px-3 text-right">
                                    <form method="POST" action="{{ route('admin.products.destroy', $p->id) }}" onsubmit="return confirm('Hapus barang ini dari inventaris?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-rose-600 hover:text-rose-800 font-bold transition">
                                            Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-slate-400 italic">
                                    Belum ada barang di inventaris.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $products->links() }}
            </div>
        </div>

    </div>

</div>
@endsection
