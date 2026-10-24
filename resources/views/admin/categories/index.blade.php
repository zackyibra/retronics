@extends('admin.layout')

@section('title', 'Kelola Kategori')

@section('content')
<div class="space-y-8">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <span class="text-xs font-bold uppercase tracking-wider text-brand-600 bg-blue-50 px-3 py-1 rounded-full">
                Pilar 1 Admin
            </span>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-display mt-2">
                Kelola Kategori Peralatan
            </h1>
            <p class="text-slate-500 text-xs sm:text-sm mt-1">
                Sesuai flowchart admin: Tambah, edit, dan hapus kategori perlengkapan fotografi & kamera.
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

        <!-- Form Tambah Kategori (4 Cols) -->
        <div class="lg:col-span-4 bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
            <h2 class="text-base font-bold text-slate-900 mb-4 pb-2 border-b border-slate-100">
                + Tambah Kategori Baru
            </h2>

            <form method="POST" action="{{ route('admin.categories.store') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Nama Kategori
                    </label>
                    <input type="text"
                           id="name"
                           name="name"
                           required
                           placeholder="Contoh: Audio & Microphone"
                           class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-brand-500">
                </div>

                <div>
                    <label for="description" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Deskripsi
                    </label>
                    <textarea id="description"
                              name="description"
                              rows="3"
                              placeholder="Deskripsi singkat jenis alat dalam kategori ini"
                              class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-brand-500"></textarea>
                </div>

                <button type="submit" class="w-full py-3 px-4 rounded-xl font-bold text-xs text-white bg-brand-600 hover:bg-brand-700 shadow-sm transition">
                    Simpan Kategori Baru
                </button>
            </form>
        </div>

        <!-- Tabel Daftar Kategori (8 Cols) -->
        <div class="lg:col-span-8 bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
            <h2 class="text-base font-bold text-slate-900 mb-4 pb-2 border-b border-slate-100">
                Daftar Kategori Terdaftar
            </h2>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-200 text-slate-400 uppercase font-bold tracking-wider">
                            <th class="py-3 px-4">Nama Kategori</th>
                            <th class="py-3 px-4">Deskripsi</th>
                            <th class="py-3 px-4">Jumlah Barang</th>
                            <th class="py-3 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($categories as $cat)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="py-3.5 px-4 font-bold text-slate-900">
                                    {{ $cat->name }}
                                </td>
                                <td class="py-3.5 px-4 text-slate-500 max-w-xs">
                                    {{ $cat->description ?? '-' }}
                                </td>
                                <td class="py-3.5 px-4 font-bold text-brand-600">
                                    {{ $cat->products_count }} unit
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <!-- Delete Form -->
                                        <form method="POST" action="{{ route('admin.categories.destroy', $cat->id) }}" onsubmit="return confirm('Hapus kategori ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-2.5 py-1 rounded-lg text-rose-600 hover:bg-rose-50 font-bold transition">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-6 text-center text-slate-400 italic">
                                    Belum ada kategori yang dibuat.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</div>
@endsection
