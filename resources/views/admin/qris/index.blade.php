@extends('admin.layout')

@section('title', 'Pengaturan QRIS Pembayaran')

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 font-display tracking-tight">
                Pengaturan QRIS Pembayaran
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Kelola kode QRIS resmi Retronics (QRIS Standar Pembayaran Nasional / ASPI) untuk pembayaran transaksi sewa.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold {{ $activeQris ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                <span class="w-2 h-2 rounded-full {{ $activeQris ? 'bg-emerald-500 animate-pulse' : 'bg-amber-500' }}"></span>
                {{ $activeQris ? 'QRIS Toko Aktif' : 'QRIS Belum Aktif' }}
            </span>
        </div>
    </div>

    <!-- Active QRIS Preview Banner & Upload Form Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        <!-- Active QRIS Preview Card (5 cols) -->
        <div class="lg:col-span-5 bg-white p-6 rounded-3xl border border-slate-200 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Status Penayangan</span>
                    <h2 class="text-base font-bold text-slate-900 font-display">QRIS Aktif Saat Ini</h2>
                </div>
                @if($activeQris)
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800">
                        Tayang di Halaman Bayar
                    </span>
                @else
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-rose-100 text-rose-800">
                        Tidak Ada QRIS Aktif
                    </span>
                @endif
            </div>

            @if($activeQris)
                <div class="text-center space-y-3">
                    <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 inline-block shadow-xs max-w-full">
                        <img src="{{ asset('storage/' . $activeQris->image_path) }}"
                             alt="QRIS {{ $activeQris->merchant_name }}"
                             class="max-h-72 w-auto object-contain mx-auto rounded-xl border border-slate-200 bg-white shadow-xs">
                    </div>

                    <div class="text-left space-y-2 bg-slate-50 p-4 rounded-2xl border border-slate-200 text-xs">
                        <div class="flex justify-between py-1 border-b border-slate-200/60">
                            <span class="text-slate-500">Nama Merchant</span>
                            <span class="font-bold text-slate-900">{{ $activeQris->merchant_name }}</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-slate-200/60">
                            <span class="text-slate-500">NMID</span>
                            <span class="font-mono font-bold text-slate-900">{{ $activeQris->nmid }}</span>
                        </div>
                        @if($activeQris->account_notes)
                            <div class="flex justify-between py-1 border-b border-slate-200/60">
                                <span class="text-slate-500">Catatan</span>
                                <span class="text-slate-700 italic">{{ $activeQris->account_notes }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between py-1">
                            <span class="text-slate-500">Diunggah Oleh</span>
                            <span class="text-slate-700">{{ $activeQris->uploader?->name ?? 'Admin Store' }} ({{ $activeQris->created_at->format('d/m/Y H:i') }} WIB)</span>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('admin.qris.toggle', $activeQris->id) }}">
                        @csrf
                        <button type="submit"
                                onclick="return confirm('Nonaktifkan QRIS ini? Halaman pembayaran sewa tidak akan menampilkan opsi QRIS sampai ada yang diaktifkan kembali.')"
                                class="w-full py-2.5 px-4 rounded-xl text-xs font-bold text-amber-700 bg-amber-50 hover:bg-amber-100 border border-amber-200 transition">
                            Nonaktifkan Penayangan QRIS Ini
                        </button>
                    </form>
                </div>
            @else
                <div class="p-8 text-center bg-slate-50 rounded-2xl border border-dashed border-slate-300 space-y-3">
                    <div class="w-12 h-12 mx-auto rounded-2xl bg-amber-100 text-amber-600 flex items-center justify-center font-bold text-xl">
                        !
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-800">Belum Ada QRIS yang Aktif</h3>
                        <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                            Pelanggan yang membuka halaman pembayaran tidak dapat memilih metode QRIS dan hanya akan melihat opsi Transfer Bank BCA / Mandiri.
                        </p>
                    </div>
                </div>
            @endif
        </div>

        <!-- Form Upload QRIS Baru (7 cols) -->
        <div class="lg:col-span-7 bg-white p-6 sm:p-7 rounded-3xl border border-slate-200 shadow-sm space-y-5">
            <div class="border-b border-slate-100 pb-3">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Formulir Konfigurasi</span>
                <h2 class="text-base font-bold text-slate-900 font-display">Unggah & Pasang QRIS Baru</h2>
                <p class="text-xs text-slate-500 mt-0.5">
                    Gunakan gambar QRIS standar resmi yang jelas, proporsional, dan dapat dipindai oleh semua aplikasi e-wallet / mobile banking.
                </p>
            </div>

            @if ($errors->any())
                <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-xs">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.qris.store') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf

                <!-- Nama Merchant & NMID -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label for="merchant_name" class="block text-xs font-bold text-slate-700">
                            Nama Merchant QRIS *
                        </label>
                        <input type="text"
                               id="merchant_name"
                               name="merchant_name"
                               value="{{ old('merchant_name', 'Warpit') }}"
                               placeholder="Contoh: Warpit / Retronics Store"
                               required
                               class="w-full px-4 py-2.5 text-xs font-semibold rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-brand-500 bg-white">
                        <p class="text-[11px] text-slate-400">Nama yang tercetak pada sertifikat QRIS.</p>
                    </div>

                    <div class="space-y-1.5">
                        <label for="nmid" class="block text-xs font-bold text-slate-700">
                            National Merchant ID (NMID) *
                        </label>
                        <input type="text"
                               id="nmid"
                               name="nmid"
                               value="{{ old('nmid', 'ID1026549194243') }}"
                               placeholder="Contoh: ID1026549194243"
                               required
                               class="w-full px-4 py-2.5 text-xs font-mono font-bold rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-brand-500 bg-white">
                        <p class="text-[11px] text-slate-400">Kode unik identitas merchant nasional.</p>
                    </div>
                </div>

                <!-- File Unggah Gambar -->
                <div class="space-y-1.5">
                    <label for="image" class="block text-xs font-bold text-slate-700">
                        File Gambar QRIS (JPG, PNG, WEBP) *
                    </label>
                    <input type="file"
                           id="image"
                           name="image"
                           accept="image/png,image/jpeg,image/jpg,image/webp"
                           required
                           class="w-full px-3 py-2 text-xs rounded-xl border border-slate-300 bg-slate-50 focus:outline-none focus:ring-2 focus:ring-brand-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-brand-600 file:text-white hover:file:bg-brand-700">
                    <p class="text-[11px] text-slate-500">
                        Disarankan gambar tegak lurus dengan kontras tinggi agar mudah discan oleh kamera smartphone pelanggan (maks 3 MB).
                    </p>
                </div>

                <!-- Catatan Tambahan -->
                <div class="space-y-1.5">
                    <label for="account_notes" class="block text-xs font-bold text-slate-700">
                        Keterangan Tambahan / Lokasi Penggunaan (Opsional)
                    </label>
                    <input type="text"
                           id="account_notes"
                           name="account_notes"
                           value="{{ old('account_notes', 'QRIS DANA Toko Warpit Cikeas Udik, Gunung Putri') }}"
                           placeholder="Contoh: QRIS DANA Resmi Warpit untuk Store Gunung Putri"
                           class="w-full px-4 py-2.5 text-xs rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-brand-500 bg-white">
                </div>

                <!-- Checkbox Langsung Aktifkan -->
                <div class="pt-2">
                    <label class="flex items-center gap-2.5 text-xs text-slate-700 cursor-pointer">
                        <input type="checkbox"
                               name="is_active"
                               value="1"
                               {{ old('is_active', true) ? 'checked' : '' }}
                               class="w-4 h-4 text-brand-600 rounded border-slate-300 focus:ring-brand-500">
                        <span class="font-medium">
                            Langsung aktifkan dan pasang QRIS ini sebagai metode pembayaran utama toko
                        </span>
                    </label>
                </div>

                <div class="pt-3">
                    <button type="submit"
                            class="w-full py-3 px-6 rounded-2xl font-bold text-xs text-white bg-brand-600 hover:bg-brand-700 shadow-md shadow-brand-600/30 transition flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                        <span>Simpan & Pasang QRIS Baru</span>
                    </button>
                </div>
            </form>
        </div>

    </div>

    <!-- Riwayat / Daftar Arsip QRIS -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-base font-bold text-slate-900 font-display">Riwayat Dokumen QRIS</h3>
                <p class="text-xs text-slate-500">Daftar arsip QRIS yang pernah diunggah ke sistem Retronics.</p>
            </div>
            <span class="text-xs text-slate-500 font-bold">Total: {{ $qrisList->total() }}</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-50 text-slate-600 font-bold border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-4">Preview</th>
                        <th class="py-3 px-4">Merchant & NMID</th>
                        <th class="py-3 px-4">Catatan</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">Pengunggah / Waktu</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($qrisList as $item)
                        <tr class="hover:bg-slate-50/60 transition {{ $item->is_active ? 'bg-blue-50/20' : '' }}">
                            <td class="py-3 px-4">
                                @if(!empty($item->image_path))
                                    <a href="{{ asset('storage/' . $item->image_path) }}" target="_blank" class="block w-12 h-12 rounded-lg border border-slate-200 overflow-hidden bg-white hover:opacity-80 transition">
                                        <img src="{{ asset('storage/' . $item->image_path) }}" alt="{{ $item->merchant_name }}" class="w-full h-full object-cover">
                                    </a>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-slate-100 text-slate-500">
                                        Tanpa berkas
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                <span class="font-bold text-slate-900 block">{{ $item->merchant_name }}</span>
                                <span class="font-mono text-[11px] text-slate-500">{{ $item->nmid }}</span>
                            </td>
                            <td class="py-3 px-4 text-slate-600">
                                {{ $item->account_notes ?? '-' }}
                            </td>
                            <td class="py-3 px-4">
                                @if($item->is_active)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                        Aktif Digunakan
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600">
                                        Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-slate-500 text-[11px]">
                                <div>{{ $item->uploader?->name ?? 'Admin Store' }}</div>
                                <div>{{ $item->created_at->format('d/m/Y H:i') }} WIB</div>
                            </td>
                            <td class="py-3 px-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <form method="POST" action="{{ route('admin.qris.toggle', $item->id) }}" class="inline">
                                        @csrf
                                        @if($item->is_active)
                                            <button type="submit"
                                                    title="Nonaktifkan"
                                                    class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-amber-50 text-amber-700 hover:bg-amber-100 border border-amber-200 transition">
                                                Nonaktifkan
                                            </button>
                                        @else
                                            <button type="submit"
                                                    title="Aktifkan sebagai QRIS toko"
                                                    class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200 transition">
                                                Jadikan Aktif
                                            </button>
                                        @endif
                                    </form>

                                    @if($item->is_active)
                                        <span class="px-2.5 py-1 rounded-lg text-[11px] font-semibold bg-slate-100 text-slate-400 border border-slate-200 cursor-not-allowed"
                                              title="QRIS aktif terlindungi dan tidak dapat dihapus. Nonaktifkan atau aktifkan QRIS lain terlebih dahulu.">
                                            🔒 Dilindungi
                                        </span>
                                    @else
                                        <form method="POST"
                                              action="{{ route('admin.qris.destroy', $item->id) }}"
                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus arsip QRIS {{ $item->merchant_name }}?')"
                                              class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    title="Hapus Arsip QRIS"
                                                    class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-rose-50 text-rose-600 hover:bg-rose-100 border border-rose-200 transition inline-flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                <span>Hapus</span>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400">
                                Belum ada arsip QRIS yang tersimpan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($qrisList->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $qrisList->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
