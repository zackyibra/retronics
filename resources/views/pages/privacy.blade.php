@extends('layouts.app')

@section('title', 'Kebijakan Privasi Pengguna | Retronics – Camera Rental Service')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-14">

    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-xs text-slate-500 mb-6">
        <a href="{{ route('home') }}" class="hover:text-brand-600 transition">Beranda</a>
        <span class="text-slate-300">›</span>
        <span class="text-slate-800 font-semibold">Kebijakan Privasi</span>
    </nav>

    <!-- Header Section -->
    <div class="border-b border-slate-200 pb-8 mb-10">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-brand-600 border border-blue-100 mb-3">
            <span>🔒 Perlindungan Data Pribadi Pelanggan Retronics</span>
        </div>
        <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 font-display tracking-tight">
            Kebijakan Privasi
        </h1>
        <p class="text-xs sm:text-sm text-slate-500 mt-2 max-w-3xl leading-relaxed">
            Berlaku efektif sejak 01 November 2026 · Versi Dokumen: <strong>v1.0-2026</strong>. Dokumen ini menjelaskan komitmen Retronics dalam menghormati dan melindungi data pribadi Anda sesuai dengan <strong>Undang-Undang Republik Indonesia Nomor 27 Tahun 2022 tentang Pelindungan Data Pribadi (UU PDP)</strong>.
        </p>
    </div>

    <!-- Main Content Clauses -->
    <div class="bg-white rounded-3xl p-6 sm:p-10 border border-slate-200 shadow-sm space-y-10 text-slate-700 text-xs sm:text-sm leading-relaxed">

        <!-- 1. Data yang Dikumpulkan -->
        <section class="space-y-3">
            <h2 class="text-base sm:text-lg font-bold text-slate-900 font-display flex items-center gap-2">
                <span class="w-7 h-7 rounded-lg bg-brand-50 text-brand-600 flex items-center justify-center text-xs font-extrabold">1</span>
                <span>Data Pribadi yang Kami Kumpulkan</span>
            </h2>
            <p>
                Ketika Anda berinteraksi dengan website dan layanan penyewaan Retronics, kami mengumpulkan kategori data pribadi berikut:
            </p>
            <ul class="list-disc list-inside space-y-2 pl-2 text-slate-600">
                <li><strong>Data Akun Pendaftaran:</strong> Nama lengkap, alamat email aktif, nomor WhatsApp/telepon genggam, alamat domisili tempat tinggal, dan kata sandi akun yang disimpan dalam bentuk terenkripsi.</li>
                <li><strong>Data Identitas Verifikasi Sewa:</strong> Nomor Induk Kependudukan (NIK), tanggal lahir (untuk memastikan pemenuhan syarat usia minimal 17 tahun), dan pindaian/foto fisik KTP asli yang diunggah saat pengajuan pesanan.</li>
                <li><strong>Data Kontak Darurat & Penggunaan:</strong> Nama kontak penjamin/darurat, hubungan kekerabatan, nomor telepon penjamin, serta tujuan penggunaan peralatan fotografi.</li>
                <li><strong>Data Transaksi & Bukti Pembayaran:</strong> Riwayat barang yang disewa, durasi, total nominal, foto struk/bukti transfer bank atau QRIS, serta tanda tangan elektronik (*digital signature*) pada Surat Perjanjian Sewa.</li>
            </ul>
        </section>

        <!-- 2. Tujuan Pengumpulan & Pemrosesan -->
        <section class="space-y-3">
            <h2 class="text-base sm:text-lg font-bold text-slate-900 font-display flex items-center gap-2">
                <span class="w-7 h-7 rounded-lg bg-brand-50 text-brand-600 flex items-center justify-center text-xs font-extrabold">2</span>
                <span>Tujuan Pengumpulan dan Pemrosesan Data</span>
            </h2>
            <p>
                Kami hanya mengumpulkan dan memproses data pribadi Anda untuk tujuan-tujuan yang sah dan spesifik, yaitu:
            </p>
            <ul class="list-disc list-inside space-y-1.5 pl-2 text-slate-600">
                <li>Memvalidasi keabsahan identitas penyewa dan memastikan kepatuhan batas usia minimal 17 tahun demi keamanan unit inventaris store.</li>
                <li>Memproses reservasi, pencatatan transaksi sewa, dan konfirmasi serah terima peralatan.</li>
                <li>Mengirimkan notifikasi operasional (pemberitahuan persetujuan dokumen, konfirmasi pembayaran, dan pengingat jadwal pengembalian) melalui WhatsApp Gateway resmi.</li>
                <li>Membuat Surat Perjanjian Sewa (SPK) yang sah dan berkekuatan hukum perdata antara kedua belah pihak.</li>
                <li>Memfasilitasi pemulihan akun melalui kode verifikasi satu kali (*One-Time Password / OTP*) ke nomor WhatsApp terdaftar.</li>
            </ul>
        </section>

        <!-- 3. Akses dan Pembatasan Data -->
        <section class="space-y-3">
            <h2 class="text-base sm:text-lg font-bold text-slate-900 font-display flex items-center gap-2">
                <span class="w-7 h-7 rounded-lg bg-brand-50 text-brand-600 flex items-center justify-center text-xs font-extrabold">3</span>
                <span>Pihak yang Memiliki Akses Terhadap Data Anda</span>
            </h2>
            <p>
                Retronics memegang teguh privasi pengguna:
            </p>
            <ul class="list-disc list-inside space-y-1.5 pl-2 text-slate-600">
                <li><strong>Internal Retronics:</strong> Akses ke data pribadi dan berkas KTP dibatasi secara ketat hanya untuk staf administrasi dan operasional store yang memiliki wewenang khusus verifikasi.</li>
                <li><strong>Tidak Dijual atau Dipindahtangankan:</strong> Kami tidak pernah menjual, menyewakan, atau memberikan data pribadi Anda kepada pihak ketiga untuk tujuan periklanan atau komersial apa pun.</li>
                <li><strong>Kewajiban Hukum:</strong> Data hanya dapat diungkapkan kepada instansi penegak hukum apabila diwajibkan secara tegas oleh peraturan perundang-undangan atau putusan pengadilan yang sah di Indonesia.</li>
            </ul>
        </section>

        <!-- 4. Perlindungan & Keamanan Penyimpanan -->
        <section class="space-y-3">
            <h2 class="text-base sm:text-lg font-bold text-slate-900 font-display flex items-center gap-2">
                <span class="w-7 h-7 rounded-lg bg-brand-50 text-brand-600 flex items-center justify-center text-xs font-extrabold">4</span>
                <span>Perlindungan dan Keamanan Penyimpanan Data</span>
            </h2>
            <p>
                Kami menerapkan standar keamanan teknis dan organisasi yang sesuai:
            </p>
            <ul class="list-disc list-inside space-y-1.5 pl-2 text-slate-600">
                <li><strong>Enkripsi Kata Sandi:</strong> Seluruh kata sandi akun dan kode OTP disimpan menggunakan algoritma hashing satu arah yang aman (*Bcrypt*), sehingga tidak dapat dibaca oleh staf maupun pengelola sistem.</li>
                <li><strong>Penyimpanan Dokumen KTP Privat:</strong> Berkas foto KTP disimpan pada direktori penyimpanan privat server (*private local storage*) dan tidak dapat diakses secara publik melalui URL browser langsung. Setiap permintaan akses berkas mewajibkan pemeriksaan autentikasi dan otorisasi kepemilikan akun.</li>
                <li><strong>Penyamaran Data Sensitif:</strong> NIK KTP dan nomor identitas disamarkan sebagian pada dokumen formulir yang dapat dicetak demi meminimalkan paparan informasi sensitif.</li>
            </ul>
        </section>

        <!-- 5. Jangka Waktu Penyimpanan Data -->
        <section class="space-y-3">
            <h2 class="text-base sm:text-lg font-bold text-slate-900 font-display flex items-center gap-2">
                <span class="w-7 h-7 rounded-lg bg-brand-50 text-brand-600 flex items-center justify-center text-xs font-extrabold">5</span>
                <span>Jangka Waktu Penyimpanan (*Data Retention*)</span>
            </h2>
            <p>
                Data pribadi akun Anda disimpan selama akun Anda aktif di sistem Retronics. Dokumen transaksi, Surat Perjanjian Sewa, dan catatan pembayaran disimpan selama masa operasional yang wajar untuk kepentingan pembukuan, pertanggungjawaban hukum, dan bukti penyelesaian kewajiban sewa.
            </p>
        </section>

        <!-- 6. Hak Pengguna & Perubahan Data -->
        <section class="space-y-3">
            <h2 class="text-base sm:text-lg font-bold text-slate-900 font-display flex items-center gap-2">
                <span class="w-7 h-7 rounded-lg bg-brand-50 text-brand-600 flex items-center justify-center text-xs font-extrabold">6</span>
                <span>Hak Pengguna atas Data Pribadi</span>
            </h2>
            <p>
                Sebagai pemilik data pribadi, Anda memiliki hak untuk:
            </p>
            <ul class="list-disc list-inside space-y-1.5 pl-2 text-slate-600">
                <li>Mengakses dan memperbarui informasi profil akun (nama, email, nomor telepon, dan domisili) secara mandiri melalui menu <strong>Akun Saya → Profil</strong>.</li>
                <li>Mengubah kata sandi akun kapan saja melalui menu Keamanan Akun.</li>
                <li>Mengajukan permohonan penonaktifan atau penutupan akun setelah seluruh kewajiban penyewaan dan pengembalian unit selesai tuntas.</li>
            </ul>
        </section>

        <!-- 7. Kontak Resmi Pengaduan Privasi -->
        <section class="space-y-3">
            <h2 class="text-base sm:text-lg font-bold text-slate-900 font-display flex items-center gap-2">
                <span class="w-7 h-7 rounded-lg bg-brand-50 text-brand-600 flex items-center justify-center text-xs font-extrabold">7</span>
                <span>Pertanyaan dan Kontak Terkait Privasi</span>
            </h2>
            <p>
                Jika Anda memiliki pertanyaan, keberatan, atau membutuhkan informasi lebih lanjut mengenai pengelolaan data pribadi di Retronics, silakan hubungi tim kami:
            </p>
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-xs space-y-1.5">
                <div><strong>Pengelola Data:</strong> Manajemen Retronics Indonesia</div>
                <div><strong>Alamat:</strong> Jl. Pabuaran, Kp. Pabuaran, Desa Cikeas Udik, Kecamatan Gunung Putri, Kabupaten Bogor, Jawa Barat, Indonesia</div>
                <div><strong>WhatsApp Customer Care:</strong> <a href="https://wa.me/6281929513780" target="_blank" class="text-brand-600 font-bold hover:underline">+62 819-2951-3780</a></div>
                <div><strong>Email Dukungan:</strong> retronics.bogor@gmail.com</div>
            </div>
        </section>

    </div>

    <!-- Bottom Action -->
    <div class="mt-8 text-center text-xs text-slate-400">
        <a href="{{ route('terms') }}" class="text-brand-600 font-bold hover:underline">← Baca Syarat dan Ketentuan Layanan</a>
        <span class="mx-2">·</span>
        <a href="{{ route('register') }}" class="text-brand-600 font-bold hover:underline">Daftar Akun Penyewa Retronics →</a>
    </div>

</div>
@endsection
