@extends('layouts.app')

@section('title', 'Katalog Promo & Voucher Diskon Sewa Kamera')
@section('meta_description', 'Daftar promo dan kode voucher diskon sewa kamera, lensa, dan lighting di Retronics Gunung Putri, Bogor. Nikmati potongan harga sewa hingga 25% untuk produksi kreatif Anda.')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full flex-grow">

    <!-- Breadcrumb -->
    <x-breadcrumb :items="[['label' => 'Promo & Voucher', 'url' => '']]" />

    <!-- Page Header -->
    <div class="text-center max-w-3xl mx-auto mb-12">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-rose-50 text-rose-700 border border-rose-200 mb-3">
            <span>🎟️ Promo & Voucher Aktif</span>
        </div>
        <h1 class="text-3xl sm:text-5xl font-black text-slate-900 font-display tracking-tight">
            Hemat Biaya Produksi Anda
        </h1>
        <p class="mt-3 text-xs sm:text-base text-slate-600 leading-relaxed">
            Salin kode promo resmi di bawah ini dan masukkan pada formulir pemesanan saat checkout untuk menikmati potongan harga sewa seketika.
        </p>
    </div>

    <!-- Voucher Cards Grid (Dynamic from MySQL Database) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-16">
        @forelse($promotions as $promo)
            <div class="bg-white rounded-3xl border border-slate-200/90 shadow-sm hover:shadow-xl hover:border-brand-400 transition-all duration-300 p-6 flex flex-col justify-between relative overflow-hidden group">
                <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-blue-50 rounded-full opacity-60 pointer-events-none"></div>

                <div>
                    <div class="flex items-center justify-between gap-2 mb-3">
                        <span class="text-[10px] font-bold px-2.5 py-0.5 rounded-full bg-blue-50 text-brand-700 border border-blue-200">
                            {{ $promo->category ? $promo->category->name : 'Semua Gear' }}
                        </span>
                        <span class="text-[11px] font-semibold text-emerald-600 flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            Aktif
                        </span>
                    </div>

                    <div class="mb-4">
                        <span class="text-xs text-slate-400 block font-medium">Nilai Diskon:</span>
                        <span class="text-2xl font-black text-slate-900 font-display">
                            {{ $promo->type === 'percentage' ? 'Diskon ' . ((int) $promo->discount_value == $promo->discount_value ? (int) $promo->discount_value : $promo->discount_value) . '%' : 'Potongan Rp' . number_format($promo->discount_value, 0, ',', '.') }}
                        </span>
                    </div>

                    <p class="text-xs text-slate-600 leading-relaxed mb-4">
                        {{ $promo->description ?: 'Gunakan kode promo ini saat melakukan pemesanan sewa peralatan kamera.' }}
                    </p>

                    <div class="space-y-1.5 text-[11px] text-slate-500 bg-slate-50 p-3 rounded-2xl border border-slate-100 mb-5">
                        <div class="flex justify-between">
                            <span>Min. Transaksi:</span>
                            <strong class="text-slate-800">Rp{{ number_format($promo->min_transaction, 0, ',', '.') }}</strong>
                        </div>
                        <div class="flex justify-between">
                            <span>Masa Berlaku:</span>
                            <strong class="text-slate-800">s/d {{ $promo->end_date->translatedFormat('d M Y') }}</strong>
                        </div>
                        <div class="flex justify-between">
                            <span>Batas Kuota:</span>
                            <strong class="text-emerald-700">Tersisa {{ $promo->quota_remaining }}x Pakai</strong>
                        </div>
                    </div>
                </div>

                <!-- Code & Copy Button -->
                <div class="p-3 bg-slate-100/80 rounded-2xl border border-dashed border-slate-300 flex items-center justify-between gap-2">
                    <div>
                        <span class="text-[9px] text-slate-400 uppercase tracking-widest block font-bold">Kode Kupon</span>
                        <span class="font-mono font-black text-sm text-brand-700">{{ $promo->code }}</span>
                    </div>

                    <button type="button"
                            onclick="copyToClipboard('{{ $promo->code }}', 'Kode {{ $promo->code }} berhasil disalin! Silakan gunakan di checkout.')"
                            class="px-3 py-1.5 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs transition shadow-xs">
                        Salin Kode
                    </button>
                </div>
            </div>
        @empty
            <div class="col-span-full py-12 text-center bg-white rounded-3xl border border-slate-200 p-8">
                <span class="text-4xl block mb-2">🎟️</span>
                <h3 class="text-base font-bold text-slate-800">Belum Ada Promo Aktif</h3>
                <p class="text-xs text-slate-500 mt-1">Nantikan penawaran menarik sewa kamera berikutnya.</p>
            </div>
        @endforelse
    </div>

    <!-- Syarat & Ketentuan Penggunaan Promo Retronics -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-sm max-w-3xl mx-auto">
        <h3 class="text-base font-bold text-slate-900 font-display mb-4 flex items-center gap-2">
            <span>📋</span>
            <span>Syarat & Ketentuan Penggunaan Promo:</span>
        </h3>
        <ol class="list-decimal list-inside space-y-2 text-xs sm:text-sm text-slate-600 leading-relaxed">
            <li>Setiap transaksi penyewaan hanya dapat menggunakan <strong>1 kode promo</strong>.</li>
            <li>Kode promo harus dimasukkan dan divalidasi pada halaman formulir pemesanan (*checkout*) sebelum melakukan transfer pembayaran.</li>
            <li>Diskon dihitung dari total biaya sewa yang memenuhi syarat minimum transaksi, tidak berlaku terhadap biaya denda keterlambatan atau deposit.</li>
            <li>Kode promo memiliki batas kuota pemakaian global dan masa berlaku yang ditentukan oleh pihak Retronics.</li>
            <li>Pihak Retronics berhak membatalkan penggunaan promo apabila ditemukan indikasi kecurangan atau duplikasi akun.</li>
        </ol>

        <div class="mt-6 pt-6 border-t border-slate-100 flex justify-center">
            <a href="{{ route('products.index') }}"
               class="px-6 py-3 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs sm:text-sm transition shadow-sm flex items-center gap-2">
                <span>Jelajahi Gear & Gunakan Promo →</span>
            </a>
        </div>
    </div>

</div>
@endsection
