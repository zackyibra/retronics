@extends('layouts.app')

@section('title', 'Surat Perjanjian Sewa ' . $rental->rental_code . ' | Retronics')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 py-8">

    <!-- Action Bar (Kembali & Print) - Hidden on Print -->
    <div class="flex items-center justify-between gap-4 mb-6 print:hidden">
        <a href="{{ route('rentals.show', $rental->id) }}" class="inline-flex items-center gap-2 text-xs font-bold text-slate-600 hover:text-brand-600 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            <span>Kembali ke Detail Pesanan</span>
        </a>

        <div class="flex items-center gap-2">
            <button type="button" onclick="window.print()" class="px-4 py-2 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs shadow-sm transition flex items-center gap-1.5 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                <span>Cetak / Simpan PDF</span>
            </button>
        </div>
    </div>

    <!-- Official Document Paper -->
    <div class="bg-white p-8 sm:p-12 rounded-3xl border border-slate-200 shadow-lg text-slate-900 leading-relaxed font-sans print:shadow-none print:border-none print:p-0">

        <!-- KOP SURAT / HEADER -->
        <div class="border-b-2 border-slate-900 pb-5 mb-8 flex items-center justify-between">
            <div class="flex items-center gap-3.5">
                <img src="{{ asset('images/logo.jpg') }}" alt="Logo Retronics" class="w-12 h-12 object-contain rounded-2xl border border-slate-300 bg-white shadow-sm flex-shrink-0">
                <div>
                    <h1 class="text-2xl font-extrabold tracking-tight font-display text-slate-900 uppercase">RETRONICS INDONESIA</h1>
                    <p class="text-xs text-slate-500 font-medium">Layanan Penyewaan Kamera, Lensa & Peralatan Produksi Multimedia Profesional</p>
                    <p class="text-[11px] text-slate-400">Jl. Pabuaran, Kp. Pabuaran, Desa Cikeas Udik, Kecamatan Gunung Putri, Kabupaten Bogor, Jawa Barat, Indonesia · WA: +62 819-2951-3780</p>
                </div>
            </div>
            <div class="text-right">
                <span class="inline-block px-3 py-1 bg-slate-100 text-slate-800 rounded-lg text-[11px] font-bold tracking-wider uppercase mb-1">
                    Dokumen Resmi
                </span>
                <p class="text-xs font-mono font-bold text-slate-700">No: SPK/{{ $rental->created_at->format('Ymd') }}/{{ $rental->id }}</p>
            </div>
        </div>

        <!-- JUDUL DOKUMEN -->
        <div class="text-center mb-8">
            <h2 class="text-xl font-extrabold font-display uppercase tracking-wider text-slate-900 border-b border-slate-300 pb-1 inline-block">
                SURAT PERJANJIAN PENYEWAAN PERALATAN FOTOGRAFI (SPK)
            </h2>
            <p class="text-xs text-slate-500 mt-1">Kode Transaksi: <strong class="text-slate-800 font-mono">{{ $rental->rental_code }}</strong></p>
        </div>

        <!-- PEMBUKAAN -->
        <p class="text-xs sm:text-sm text-justify mb-6">
            Pada hari ini, <strong class="text-slate-800">{{ $verification->agreement_signed_at?->translatedFormat('l, d F Y') ?? now()->translatedFormat('l, d F Y') }}</strong>, bertempat di Gunung Putri, Kabupaten Bogor, telah dibuat, disepakati, dan ditandatangani perjanjian sewa menyewa peralatan fotografi antara pihak-pihak di bawah ini:
        </p>

        <!-- IDENTITAS PARA PIHAK -->
        <div class="space-y-4 mb-6 text-xs sm:text-sm">
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                <strong class="text-slate-900 block mb-1 font-bold uppercase tracking-wider text-[11px]">1. PIHAK PERTAMA (Pemberi Sewa):</strong>
                <table class="w-full text-slate-700">
                    <tr><td class="w-36 py-0.5 text-slate-500">Nama Usaha / Store</td><td class="w-4">:</td><td class="font-semibold text-slate-800">Retronics Indonesia</td></tr>
                    <tr><td class="py-0.5 text-slate-500">Alamat Store Resmi</td><td>:</td><td>Jl. Pabuaran, Kp. Pabuaran, Desa Cikeas Udik, Kec. Gunung Putri, Kab. Bogor, Jawa Barat, Indonesia</td></tr>
                    <tr><td class="py-0.5 text-slate-500">Kontak Resmi Operasional</td><td>:</td><td>+62 819-2951-3780 (WhatsApp)</td></tr>
                </table>
            </div>

            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                <strong class="text-slate-900 block mb-1 font-bold uppercase tracking-wider text-[11px]">2. PIHAK KEDUA (Penyewa):</strong>
                <table class="w-full text-slate-700">
                    <tr><td class="w-36 py-0.5 text-slate-500">Nama Lengkap</td><td class="w-4">:</td><td class="font-semibold text-slate-800">{{ $rental->user->name }}</td></tr>
                    <tr><td class="py-0.5 text-slate-500">Nomor NIK KTP</td><td>:</td><td class="font-mono font-bold">{{ $verification->id_card_number ?? $rental->user->id_card_number ?? '-' }}</td></tr>
                    @if($verification->birth_date)
                        <tr><td class="py-0.5 text-slate-500">Tanggal Lahir / Usia</td><td>:</td><td>{{ $verification->birth_date->translatedFormat('d F Y') }} ({{ \Carbon\Carbon::parse($verification->birth_date)->age }} tahun - Memenuhi syarat min. 17 tahun)</td></tr>
                    @endif
                    <tr><td class="py-0.5 text-slate-500">No. WhatsApp</td><td>:</td><td>{{ $rental->user->phone ?? '-' }}</td></tr>
                    <tr><td class="py-0.5 text-slate-500">Alamat Domisili</td><td>:</td><td>{{ $rental->user->address ?? 'Gunung Putri, Bogor' }}</td></tr>
                    <tr><td class="py-0.5 text-slate-500">Tujuan Penyewaan</td><td>:</td><td class="italic text-slate-800">{{ $verification->rental_purpose ?? 'Keperluan Produksi Fotografi' }}</td></tr>
                    <tr><td class="py-0.5 text-slate-500">Kontak Darurat</td><td>:</td><td>{{ $verification->emergency_contact_name ?? '-' }} ({{ $verification->emergency_contact_relation ?? '-' }} · {{ $verification->emergency_contact_phone ?? '-' }})</td></tr>
                </table>
            </div>
        </div>

        <!-- RINCIAN BARANG SEWA -->
        <div class="mb-6">
            <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider mb-2">Rincian Peralatan yang Disewa:</h3>
            <table class="w-full text-xs border border-slate-200 rounded-xl overflow-hidden">
                <thead class="bg-slate-100 text-slate-700">
                    <tr>
                        <th class="py-2.5 px-3 text-left">No</th>
                        <th class="py-2.5 px-3 text-left">Nama Gear / Peralatan</th>
                        <th class="py-2.5 px-3 text-center">Kondisi Serah Terima</th>
                        <th class="py-2.5 px-3 text-center">Jumlah</th>
                        <th class="py-2.5 px-3 text-right">Tarif / Hari</th>
                        <th class="py-2.5 px-3 text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($rental->items as $idx => $item)
                        <tr>
                            <td class="py-2 px-3 text-slate-500">{{ $idx + 1 }}</td>
                            <td class="py-2 px-3 font-semibold text-slate-900">{{ $item->product->name }} <span class="text-slate-400 font-normal">({{ $item->product->category->name ?? 'Gear' }})</span></td>
                            <td class="py-2 px-3 text-center text-emerald-700 font-medium">Baik, Bersih & Normal [✓]</td>
                            <td class="py-2 px-3 text-center font-bold">{{ $item->quantity }} unit</td>
                            <td class="py-2 px-3 text-right">Rp{{ number_format($item->price_per_day, 0, ',', '.') }}</td>
                            <td class="py-2 px-3 text-right font-bold text-slate-900">Rp{{ number_format($item->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-slate-50 font-bold border-t border-slate-200 text-xs">
                    <tr>
                        <td colspan="5" class="py-2 px-3 text-right text-slate-700">Jadwal Sewa & Durasi:</td>
                        <td class="py-2 px-3 text-right text-slate-900">{{ $rental->start_date->translatedFormat('d M Y') }} s/d {{ $rental->end_date->translatedFormat('d M Y') }} ({{ $rental->duration_days }} Hari Kalender)</td>
                    </tr>
                    @php
                        $grossSum = $rental->items->sum('subtotal');
                        $diff = $grossSum - $rental->total_price;
                    @endphp
                    @if($diff > 0)
                        <tr>
                            <td colspan="5" class="py-1 px-3 text-right text-emerald-700 font-semibold">Potongan Diskon Promo / Durasi:</td>
                            <td class="py-1 px-3 text-right text-emerald-700">-Rp{{ number_format($diff, 0, ',', '.') }}</td>
                        </tr>
                    @endif
                    @if($verification->requires_stamp_duty || $rental->total_price >= 5000000)
                        <tr>
                            <td colspan="5" class="py-1 px-3 text-right text-slate-600 font-semibold">Bea Meterai UU No. 10/2020:</td>
                            <td class="py-1 px-3 text-right text-slate-900">Rp10.000 (Lunas Difasilitasi Toko)</td>
                        </tr>
                    @endif
                    <tr class="bg-slate-100 border-t border-slate-300">
                        <td colspan="5" class="py-2.5 px-3 text-right text-slate-900 font-extrabold uppercase">Total Biaya Sewa:</td>
                        <td class="py-2.5 px-3 text-right text-brand-600 text-sm font-extrabold">Rp{{ number_format($rental->total_price, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- KLAUSUL PASAL-PASAL PERJANJIAN SEWA RETRONICS -->
        <div class="mb-8 space-y-4 text-xs sm:text-sm text-justify">
            <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider mb-2 border-b border-slate-200 pb-1">
                Klausul dan Syarat Perjanjian Sewa:
            </h3>

            <!-- Pasal 1 -->
            <div class="space-y-1">
                <strong class="text-slate-900 font-bold block text-xs">Pasal 1: Hak dan Kewajiban Retronics (Pihak Pertama)</strong>
                <p class="text-slate-600 text-[11px] leading-relaxed">
                    <strong>1. Hak Pihak Pertama:</strong> (a) Menerima pelunasan biaya sewa sesuai tagihan sebelum barang diserahkan; (b) Menerima dan memverifikasi dokumen fisik KTP asli Penyewa yang sah; (c) Melakukan pemeriksaan teknis bersama (*joint inspection*) fisik, sensor, dan fungsi tombol saat serah terima barang; (d) Menolak atau membatalkan permohonan sewa jika identitas tidak memenuhi syarat usia minimal 17 tahun atau terindikasi melanggar hukum; (e) Memperoleh pengembalian barang tepat waktu sesuai jadwal operasional toko; (f) Menuntut biaya perbaikan atas kerusakan akibat kelalaian pemakaian di luar keausan wajar (*fair wear and tear*) atau penggantian atas kehilangan barang.
                </p>
                <p class="text-slate-600 text-[11px] leading-relaxed mt-1">
                    <strong>2. Kewajiban Pihak Pertama:</strong> (a) Menyerahkan peralatan sewa dalam kondisi bersih, normal, dan siap pakai; (b) Menyertakan aksesori standar operasional (baterai terisi, charger, penutup lensa/bodi, tali strap, dan tas pelindung); (c) Memberikan penjelasan operasional teknis dasar keselamatan peralatan apabila diminta; (d) Melakukan uji coba fungsi bersama Penyewa di Store; (e) Menjaga kerahasiaan data identitas Penyewa sesuai ketentuan UU Perlindungan Data Pribadi; (f) Memberikan bukti pembayaran dan tanda terima yang sah; (g) Menyelesaikan setiap kendala teknis atau pengembalian secara musyawarah dan berkeadilan.
                </p>
            </div>

            <!-- Pasal 2 -->
            <div class="space-y-1">
                <strong class="text-slate-900 font-bold block text-xs">Pasal 2: Hak dan Kewajiban Penyewa (Pihak Kedua)</strong>
                <p class="text-slate-600 text-[11px] leading-relaxed">
                    <strong>1. Hak Pihak Kedua:</strong> (a) Menerima dan menggunakan peralatan sewa dalam kondisi prima dan layak fungsi selama durasi sewa; (b) Menerima kelengkapan aksesori standar yang tercatat tanpa biaya tersembunyi; (c) Memeriksa dan menguji fisik, fungsi sensor, tombol, dan optik secara menyeluruh bersama petugas toko sebelum meninggalkan store; (d) Mengajukan penggantian unit atau pembatalan tanpa penalti apabila saat serah terima awal terbukti terdapat kerusakan fungsi; (e) Memperoleh bukti transaksi resmi dan salinan Surat Perjanjian Sewa; (f) Memperoleh panduan teknis operasional wajar dari tim Retronics; (g) Mendapatkan jaminan perlindungan atas kerahasiaan data identitas diri; (h) Menerima pengembalian jaminan (apabila ada titipan khusus) secara penuh setelah pemeriksaan akhir dinyatakan lengkap dan baik.
                </p>
                <p class="text-slate-600 text-[11px] leading-relaxed mt-1">
                    <strong>2. Kewajiban Pihak Kedua:</strong> (a) Berusia minimal 17 tahun, memiliki KTP sah, dan mengisi data identitas yang valid; (b) Melunasi seluruh biaya sewa sebelum penyerahan barang; (c) Mengambil dan mengembalikan barang secara mandiri langsung di Store Retronics Gunung Putri, Bogor; (d) Memperlakukan dan menjaga kebersihan peralatan dengan standar kehati-hatian profesional; (e) Menggunakan peralatan untuk tujuan legal dan menghindarkan dari paparan bahaya (air, pasir laut, benturan keras, suhu ekstrem); (f) Dilarang keras memindahtangankan, menyewakan kembali (*sub-rent*), atau menggadaikan unit kepada pihak ketiga; (g) Mengembalikan barang selambat-lambatnya pukul 21.00 WIB pada tanggal berakhirnya sewa; (h) Melaporkan segera kepada Retronics apabila mengalami kendala selama pemakaian; (i) Bertanggung jawab mengganti kerugian atas kerusakan atau kehilangan yang timbul akibat kelalaian selama masa sewa.
                </p>
            </div>

            <!-- Pasal 3 -->
            <div>
                <strong class="text-slate-900 font-bold block text-xs">Pasal 3: Prosedur Pengambilan dan Pengembalian (Store Pickup Only)</strong>
                <p class="text-slate-600 text-[11px] leading-relaxed">
                    Pengambilan dan pengembalian peralatan sewa wajib dilakukan langsung oleh Penyewa di Store Retronics, beralamat di <em>Jl. Pabuaran, Kp. Pabuaran, Desa Cikeas Udik, Kecamatan Gunung Putri, Kabupaten Bogor</em>. Retronics tidak melayani pengantaran ekspedisi atau kurir tanpa perjanjian khusus guna menjamin terlaksananya pemeriksaan fisik bersama secara langsung (*joint inspection*) oleh kedua belah pihak.
                </p>
            </div>

            <!-- Pasal 4 -->
            <div>
                <strong class="text-slate-900 font-bold block text-xs">Pasal 4: Batas Waktu Pengembalian & Denda Keterlambatan</strong>
                <p class="text-slate-600 text-[11px] leading-relaxed">
                    Batas waktu pengembalian peralatan selambat-lambatnya adalah pada tanggal berakhirnya sewa pukul 21.00 WIB. Keterlambatan pengembalian tanpa konfirmasi perpanjangan resmi dikenakan denda keterlambatan Retronics sebesar <strong class="text-slate-800">Rp50.000,- (lima puluh ribu rupiah) per hari kalender per unit barang</strong>, dihitung secara kumulatif hingga barang diserahkan kembali secara utuh ke Store Retronics.
                </p>
            </div>

            <!-- Pasal 5 -->
            <div>
                <strong class="text-slate-900 font-bold block text-xs">Pasal 5: Kerusakan, Kehilangan, dan Tanggung Jawab Hukum</strong>
                <p class="text-slate-600 text-[11px] leading-relaxed">
                    1. Keausan alami akibat pemakaian wajar (*fair wear and tear*) bukan merupakan kesalahan penyewa dan tidak dibebankan biaya ganti rugi.<br>
                    2. Kerusakan akibat benturan fisik, kemasukan cairan, kelalaian pengguna, atau kehilangan perlengkapan/unit menjadi tanggung jawab penuh PIHAK KEDUA.<br>
                    3. Biaya perbaikan kerusakan dihitung secara wajar dan transparan berdasarkan estimasi resmi dari *authorized service center* atau teknisi resmi mitra Retronics.<br>
                    4. Dalam hal terjadi kehilangan unit atau kerusakan total (*total loss*), PIHAK KEDUA wajib mengganti unit dengan tipe yang sama atau membayar ganti rugi senilai harga pasar wajar barang saat terjadinya peristiwa.
                </p>
            </div>

            <!-- Pasal 6 -->
            <div>
                <strong class="text-slate-900 font-bold block text-xs">Pasal 6: Kebijakan Pembatalan dan Pengembalian Dana (Refund)</strong>
                <p class="text-slate-600 text-[11px] leading-relaxed">
                    Pembatalan yang dilakukan sebelum verifikasi pembayaran disetujui tidak dikenakan biaya pembatalan. Jika pembatalan dilakukan setelah pembayaran diverifikasi namun sebelum barang diambil di store, penyewa dapat mengajukan pengembalian dana setelah dikurangi biaya administrasi toko sebesar 10%. Pembatalan sepihak setelah barang diambil dari store tidak memberikan hak pengembalian dana sewa.
                </p>
            </div>

            <!-- Pasal 7 -->
            <div>
                <strong class="text-slate-900 font-bold block text-xs">Pasal 7: Keadaan Kahar (Force Majeure)</strong>
                <p class="text-slate-600 text-[11px] leading-relaxed">
                    Para pihak dibebaskan dari tuntutan hukum apabila terjadi peristiwa di luar kekuasaan dan kehendak manusia yang wajar (keadaan kahar / *force majeure*), meliputi gempa bumi, banjir bandang, kebakaran hebat, kerusuhan massa, atau kebijakan darurat pemerintah yang berdampak langsung. Pihak yang terdampak wajib mengabarkan dalam tempo 1x24 jam untuk penyelesaian secara musyawarah dan adil.
                </p>
            </div>

            <!-- Pasal 8 -->
            <div>
                <strong class="text-slate-900 font-bold block text-xs">Pasal 8: Legalitas Tanda Tangan Elektronik & Integritas Dokumen</strong>
                <p class="text-slate-600 text-[11px] leading-relaxed">
                    Perjanjian ini dibuat dan disetujui secara elektronik yang sah dan berkekuatan hukum mengikat kedua pihak sesuai ketentuan Undang-Undang Informasi dan Transaksi Elektronik (UU ITE). Tanda tangan elektronik PIHAK KEDUA yang terekam pada sistem digital Retronics memiliki kekuatan pembuktian yang setara dengan tanda tangan basah di atas kertas.
                </p>
            </div>

            <!-- Pasal 9 -->
            <div>
                <strong class="text-slate-900 font-bold block text-xs">Pasal 9: Ketentuan Bea Meterai (UU No. 10 Tahun 2020 & Pasal 1320 KUHPerdata)</strong>
                <p class="text-slate-600 text-[11px] leading-relaxed">
                    Berdasarkan UU No. 10 Tahun 2020 tentang Bea Meterai, dokumen perdata bernilai nominal lebih dari Rp5.000.000,- dikenakan Bea Meterai Rp10.000,- sebagai pajak dokumen untuk keperluan pembuktian di pengadilan. Sesuai ketentuan hukum yang berlaku, ketiadaan Bea Meterai tidak membatalkan keabsahan perjanjian ini, karena syarat sahnya perjanjian perdata tunduk sepenuhnya pada <strong>Pasal 1320 Kitab Undang-Undang Hukum Perdata (KUHPerdata)</strong> yaitu adanya kesepakatan, kecakapan bertindak, suatu hal tertentu, dan suatu sebab yang halal.
                </p>
            </div>

            <!-- Pasal 10 -->
            <div>
                <strong class="text-slate-900 font-bold block text-xs">Pasal 10: Perlindungan Data Pribadi (UU No. 27/2022) & Penyelesaian Sengketa</strong>
                <p class="text-slate-600 text-[11px] leading-relaxed">
                    1. Retronics berkomitmen melindungi data pribadi dan dokumen KTP Penyewa sesuai Undang-Undang Perlindungan Data Pribadi (UU No. 27/2022). Data hanya dipergunakan untuk keperluan administrasi sewa dan tidak diserahkan ke pihak ketiga tanpa izin sah.<br>
                    2. Segala perselisihan yang timbul dari perjanjian ini akan diselesaikan secara musyawarah untuk mencapai mufakat. Apabila kesepakatan tidak tercapai, para pihak sepakat memilih kedudukan hukum tetap di Pengadilan Negeri Kabupaten Bogor.
                </p>
            </div>
        </div>

        <!-- KOLOM TANDA TANGAN DIGITAL & METERAI -->
        <div class="pt-6 border-t-2 border-slate-900 page-break-avoid">
            <div class="text-right text-xs text-slate-500 mb-3">
                Gunung Putri, Kabupaten Bogor, {{ $verification->agreement_signed_at?->translatedFormat('d F Y') ?? now()->translatedFormat('d F Y') }}
            </div>

            <div class="grid grid-cols-2 gap-8 items-start text-xs sm:text-sm">

                <!-- Tanda Tangan Pihak Pertama -->
                <div class="text-center p-4 rounded-2xl bg-slate-50 border border-slate-200">
                    <p class="text-slate-500 mb-1 font-bold text-[11px] uppercase tracking-wider">PIHAK PERTAMA,</p>
                    <p class="font-extrabold text-slate-900">Retronics Indonesia</p>
                    <div class="h-28 flex items-center justify-center my-2">
                        <div class="w-24 h-24 rounded-full border-2 border-brand-500/50 flex flex-col items-center justify-center text-[10px] text-brand-700 font-bold uppercase tracking-wider transform -rotate-12 bg-blue-50/60 p-2 shadow-xs">
                            <span>RETRONICS</span>
                            <span class="text-[9px] text-brand-500 font-semibold">STORE RESMI</span>
                            <span>BOGOR</span>
                        </div>
                    </div>
                    <p class="font-bold text-slate-900 underline">Tim Operasional Store</p>
                    <p class="text-[11px] text-slate-500">Pemberi Sewa Sah</p>
                </div>

                <!-- Tanda Tangan Pihak Kedua + Meterai -->
                <div class="text-center p-4 rounded-2xl bg-slate-50 border border-slate-200 relative">
                    <p class="text-slate-500 mb-1 font-bold text-[11px] uppercase tracking-wider">PIHAK KEDUA,</p>
                    <p class="font-extrabold text-slate-900">
                        {{ $rental->user->name }}
                    </p>

                    <!-- Area Tanda Tangan & Bea Meterai Badge -->
                    <div class="h-28 flex items-center justify-center my-2 relative">
                        <!-- Bea Meterai Badge (Jika >= 5 Juta) -->
                        @if($verification->requires_stamp_duty || $rental->total_price >= 5000000)
                            <div class="absolute -top-1 left-3 w-20 h-24 rounded-xl border border-dashed border-emerald-600 bg-emerald-50/95 text-emerald-800 p-1.5 flex flex-col items-center justify-between shadow-xs z-0 rotate-2">
                                <span class="text-[9px] font-extrabold uppercase tracking-widest text-emerald-700">METERAI</span>
                                <span class="text-xs font-black font-display text-emerald-900">10000</span>
                                <span class="text-[8px] font-semibold text-center text-emerald-600">UU 10/2020 ELEKTRONIK</span>
                            </div>
                        @endif

                        <!-- Signature Canvas Display -->
                        <div class="relative z-10">
                            @if($verification->signature_data)
                                <img src="{{ $verification->signature_data }}" alt="Tanda Tangan Pelanggan" class="max-h-24 max-w-[200px] object-contain mx-auto">
                            @else
                                <div class="text-xs text-slate-400 italic py-8">(Tanda tangan digital tercatat)</div>
                            @endif
                        </div>
                    </div>

                    <p class="font-bold text-slate-900 underline">{{ $rental->user->name }}</p>
                    <p class="text-[11px] text-slate-500">
                        Ditandatangani secara elektronik pada: {{ $verification->agreement_signed_at?->format('d/m/Y H:i') ?? now()->format('d/m/Y H:i') }} WIB
                    </p>
                </div>

            </div>
        </div>

        <!-- FOOTER STATUS VERIFIKASI RESMI -->
        <div class="mt-8 pt-4 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-400">
            <div>Status Dokumen: <strong class="text-slate-700">{{ $verification->status_label }}</strong></div>
            <div>Dicetak melalui Portal Resmi Retronics Photography Equipment Rental · Store Gunung Putri, Bogor</div>
        </div>

    </div>

</div>
@endsection
