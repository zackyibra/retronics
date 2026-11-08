@extends('layouts.app')

@section('title', 'Syarat dan Ketentuan Layanan | Retronics – Camera Rental Service')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-14">

    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-xs text-slate-500 mb-6">
        <a href="{{ route('home') }}" class="hover:text-brand-600 transition">Beranda</a>
        <span class="text-slate-300">›</span>
        <span class="text-slate-800 font-semibold">Syarat & Ketentuan</span>
    </nav>

    <!-- Header Section -->
    <div class="border-b border-slate-200 pb-8 mb-10">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold bg-brand-50 text-brand-600 border border-brand-100 mb-3">
            <span>⚖️ Dokumen Hukum & Kebijakan Operasional Retronics</span>
        </div>
        <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 font-display tracking-tight">
            Syarat dan Ketentuan Layanan
        </h1>
        <p class="text-xs sm:text-sm text-slate-500 mt-2 max-w-3xl leading-relaxed">
            Berlaku efektif sejak 01 November 2026 · Versi Dokumen: <strong>v1.0-2026</strong>. Harap membaca seluruh ketentuan dengan saksama sebelum mendaftarkan akun atau melakukan penyewaan peralatan fotografi di Retronics.
        </p>
    </div>

    <!-- Main Content Clauses -->
    <div class="bg-white rounded-3xl p-6 sm:p-10 border border-slate-200 shadow-sm space-y-10 text-slate-700 text-xs sm:text-sm leading-relaxed">

        <!-- 1. Ketentuan Umum & Akun -->
        <section class="space-y-3">
            <h2 class="text-base sm:text-lg font-bold text-slate-900 font-display flex items-center gap-2">
                <span class="w-7 h-7 rounded-lg bg-brand-50 text-brand-600 flex items-center justify-center text-xs font-extrabold">1</span>
                <span>Ketentuan Pembuatan dan Penggunaan Akun</span>
            </h2>
            <p>
                Layanan penyewaan kamera, lensa, dan aksesori multimedia Retronics hanya dapat diakses melalui akun resmi yang terdaftar pada sistem website Retronics. Setiap pengguna wajib:
            </p>
            <ul class="list-disc list-inside space-y-1.5 pl-2 text-slate-600">
                <li>Mengisi data diri asli dan akurat (nama lengkap sesuai KTP, alamat email aktif, nomor WhatsApp yang dapat dihubungi, dan domisili aktual).</li>
                <li>Menjaga kerahasiaan kata sandi dan kode verifikasi akun (*One-Time Password / OTP*). Segala aktivitas yang terjadi melalui akun terdaftar merupakan tanggung jawab penuh pemilik akun.</li>
                <li>Tidak memindahtangankan, memperjualbelikan, atau meminjamkan akun pengguna kepada pihak lain tanpa persetujuan tertulis dari pihak Retronics.</li>
            </ul>
        </section>

        <!-- 2. Persyaratan Usia & Verifikasi KTP -->
        <section class="space-y-3">
            <h2 class="text-base sm:text-lg font-bold text-slate-900 font-display flex items-center gap-2">
                <span class="w-7 h-7 rounded-lg bg-brand-50 text-brand-600 flex items-center justify-center text-xs font-extrabold">2</span>
                <span>Persyaratan Usia Minimal & Verifikasi Identitas KTP</span>
            </h2>
            <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-950 text-xs sm:text-sm">
                <strong>Ketentuan Usia Mutlak:</strong> Retronics hanya melayani penyewa yang telah berusia <strong>minimal 17 (tujuh belas) tahun</strong> dan telah memiliki Kartu Tanda Penduduk (KTP) asli yang sah secara hukum di Republik Indonesia.
            </div>
            <p>
                Pelanggan di bawah usia 17 tahun tidak diperbolehkan menyewa, baik secara mandiri maupun melalui perwakilan dokumen wali. Untuk setiap pengajuan sewa, penyewa wajib:
            </p>
            <ul class="list-disc list-inside space-y-1.5 pl-2 text-slate-600">
                <li>Mengisi nomor NIK KTP dan tanggal lahir yang sebenarnya pada formulir verifikasi sewa.</li>
                <li>Mengunggah foto atau pindaian KTP asli yang jelas, tidak buram, tidak terpotong, dan dapat dibaca sistem audit Retronics.</li>
                <li>Mencantumkan kontak darurat penjamin yang aktif dan dapat dihubungi saat koordinasi sewa.</li>
            </ul>
        </section>

        <!-- 3. Pemesanan, Tarif, dan Pembayaran -->
        <section class="space-y-3">
            <h2 class="text-base sm:text-lg font-bold text-slate-900 font-display flex items-center gap-2">
                <span class="w-7 h-7 rounded-lg bg-brand-50 text-brand-600 flex items-center justify-center text-xs font-extrabold">3</span>
                <span>Pemesanan, Tarif Sewa, dan Tata Cara Pembayaran</span>
            </h2>
            <p>
                Pemesanan unit gear dilakukan melalui katalog dan keranjang booking website Retronics. Ketentuan pemesanan:
            </p>
            <ul class="list-disc list-inside space-y-1.5 pl-2 text-slate-600">
                <li>Durasi sewa dihitung per hari (24 jam) terhitung sejak jadwal pengambilan unit yang disepakati.</li>
                <li>Metode pembayaran yang didukung meliputi <strong>Transfer Bank Manual (BCA & Mandiri)</strong> dan <strong>QRIS DANA Nasional</strong>.</li>
                <li>Pembayaran bersifat manual: Penyewa mentransfer nominal persis tagihan, kemudian mengunggah bukti transfer sah pada halaman pesanan.</li>
                <li>Status penyewaan dinyatakan terkonfirmasi dan stok unit direservasi hanya setelah dokumen identitas disetujui dan mutasi pembayaran diverifikasi valid oleh admin store Retronics.</li>
            </ul>
        </section>

        <!-- 4. Pengambilan Langsung di Toko (Store Pickup) -->
        <section class="space-y-3">
            <h2 class="text-base sm:text-lg font-bold text-slate-900 font-display flex items-center gap-2">
                <span class="w-7 h-7 rounded-lg bg-brand-50 text-brand-600 flex items-center justify-center text-xs font-extrabold">4</span>
                <span>Pengambilan Barang Langsung di Toko (Store Pickup Only)</span>
            </h2>
            <p>
                Retronics saat ini <strong>hanya melayani pengambilan barang langsung di toko fisik kami</strong> (tidak melayani kurir instan tanpa verifikasi tatap muka atau pengiriman ekspedisi antar-kota):
            </p>
            <ul class="list-disc list-inside space-y-1.5 pl-2 text-slate-600">
                <li><strong>Lokasi Store:</strong> Jl. Pabuaran, Kp. Pabuaran, Desa Cikeas Udik, Kecamatan Gunung Putri, Kabupaten Bogor, Jawa Barat.</li>
                <li>Pengambilan dilakukan mulai pukul 10.00 WIB pada tanggal mulai sewa dengan menunjukkan identitas fisik KTP asli yang sesuai dengan data akun.</li>
                <li>Sebelum unit diserahkan, staf store dan penyewa wajib melakukan uji fungsi dan cek fisik bersama (*joint inspection*) meliputi sensor, optik lensa, tombol, shutter, layar, baterai, dan kelengkapan unit. Hasil pemeriksaan dicatat pada formulir serah terima.</li>
            </ul>
        </section>

        <!-- 5. Penggunaan & Pemeliharaan Gear -->
        <section class="space-y-3">
            <h2 class="text-base sm:text-lg font-bold text-slate-900 font-display flex items-center gap-2">
                <span class="w-7 h-7 rounded-lg bg-brand-50 text-brand-600 flex items-center justify-center text-xs font-extrabold">5</span>
                <span>Penggunaan dan Pemeliharaan Peralatan Selama Masa Sewa</span>
            </h2>
            <p>
                Penyewa berhak menggunakan gear sesuai tujuan sewa yang dinyatakan dan wajib:
            </p>
            <ul class="list-disc list-inside space-y-1.5 pl-2 text-slate-600">
                <li>Memperlakukan peralatan dengan kehati-hatian tingkat tinggi sesuai petunjuk teknis pabrikan.</li>
                <li>Tidak memindahtangankan, menyewakan kembali (*sub-rent*), menjaminkan, atau menggadaikan unit kamera dan lensa kepada pihak mana pun.</li>
                <li>Menghindarkan peralatan dari paparan air hujan, pasir pantai tanpa pelindung, benturan keras, serta suhu ekstrem.</li>
            </ul>
        </section>

        <!-- 6. Pengembalian & Denda Keterlambatan -->
        <section class="space-y-3">
            <h2 class="text-base sm:text-lg font-bold text-slate-900 font-display flex items-center gap-2">
                <span class="w-7 h-7 rounded-lg bg-brand-50 text-brand-600 flex items-center justify-center text-xs font-extrabold">6</span>
                <span>Ketentuan Pengembalian Barang & Denda Keterlambatan</span>
            </h2>
            <p>
                Pengembalian dilakukan langsung ke Store Retronics Gunung Putri, Bogor, selambat-lambatnya pada tanggal berakhirnya sewa pukul 21.00 WIB.
            </p>
            <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-950 font-semibold text-xs sm:text-sm">
                Aturan Denda Resmi: Keterlambatan pengembalian unit dikenakan denda keterlambatan sebesar <strong>Rp50.000,- (lima puluh ribu rupiah) per hari untuk setiap unit barang</strong> yang terlambat, dihitung secara kumulatif terpisah dari biaya sewa pokok.
            </div>
            <p>
                Saat pengembalian, staf store akan melakukan pemeriksaan kelengkapan aksesoris dan tes fungsi fisik kembali.
            </p>
        </section>

        <!-- 7. Kerusakan dan Kehilangan -->
        <section class="space-y-3">
            <h2 class="text-base sm:text-lg font-bold text-slate-900 font-display flex items-center gap-2">
                <span class="w-7 h-7 rounded-lg bg-brand-50 text-brand-600 flex items-center justify-center text-xs font-extrabold">7</span>
                <span>Ketentuan Tanggung Jawab Kerusakan dan Kehilangan</span>
            </h2>
            <p>
                Retronics menerapkan klausul yang adil dan seimbang bagi kedua belah pihak:
            </p>
            <ul class="list-disc list-inside space-y-1.5 pl-2 text-slate-600">
                <li>Kerusakan yang telah dicatat sebelum barang diserahkan tidak dibebankan kepada penyewa.</li>
                <li>Keausan normal akibat pemakaian wajar (*normal wear and tear*) bukan merupakan kelalaian penyewa.</li>
                <li>Jika terjadi kerusakan fisik, mekanik, atau optik akibat benturan, kelalaian, atau kesengajaan selama masa penguasaan penyewa, penyewa bertanggung jawab atas biaya perbaikan resmi berdasarkan estimasi wajar dan transparan dari *service center* resmi.</li>
                <li>Jika barang hilang, penyewa wajib mengganti dengan unit yang sama atau nilai kerugian yang disepakati secara musyawarah mufakat.</li>
            </ul>
        </section>

        <!-- 8. Pembatalan & Pengembalian Dana -->
        <section class="space-y-3">
            <h2 class="text-base sm:text-lg font-bold text-slate-900 font-display flex items-center gap-2">
                <span class="w-7 h-7 rounded-lg bg-brand-50 text-brand-600 flex items-center justify-center text-xs font-extrabold">8</span>
                <span>Pembatalan Pesanan dan Pengembalian Dana (*Refund*)</span>
            </h2>
            <ul class="list-disc list-inside space-y-1.5 pl-2 text-slate-600">
                <li>Penyewa dapat membatalkan pesanan secara mandiri sebelum barang disiapkan/diambil melalui menu Rincian Sewa.</li>
                <li>Apabila Retronics berhalangan menyediakan unit akibat kendala teknis internal yang tidak terduga, dana yang telah dibayarkan akan dikembalikan penuh (100%).</li>
                <li>Pengembalian dana diproses via transfer rekening bank penyewa dalam waktu maksimal 2 (dua) hari kerja operasional.</li>
            </ul>
        </section>

        <!-- 9. Penanganan Keluhan & Kontak Resmi -->
        <section class="space-y-3">
            <h2 class="text-base sm:text-lg font-bold text-slate-900 font-display flex items-center gap-2">
                <span class="w-7 h-7 rounded-lg bg-brand-50 text-brand-600 flex items-center justify-center text-xs font-extrabold">9</span>
                <span>Layanan Pelanggan & Informasi Kontak Resmi</span>
            </h2>
            <p>
                Untuk koordinasi penyewaan, penanganan kendala teknis, atau pertanyaan terkait perjanjian sewa, hubungi kontak resmi Retronics:
            </p>
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-xs space-y-1.5">
                <div><strong>Nama Usaha:</strong> Retronics – Camera Rental Service</div>
                <div><strong>Alamat Store:</strong> Jl. Pabuaran, Kp. Pabuaran, Desa Cikeas Udik, Kecamatan Gunung Putri, Kabupaten Bogor, Jawa Barat, Indonesia</div>
                <div><strong>WhatsApp Customer Care:</strong> <a href="https://wa.me/6281929513780" target="_blank" class="text-brand-600 font-bold hover:underline">+62 819-2951-3780</a></div>
                <div><strong>Jam Operasional Store:</strong> Setiap Hari, 08.00 – 21.00 WIB</div>
            </div>
        </section>

    </div>

    <!-- Bottom Action -->
    <div class="mt-8 text-center text-xs text-slate-400">
        <a href="{{ route('register') }}" class="text-brand-600 font-bold hover:underline">← Kembali ke Formulir Pendaftaran Akun</a>
        <span class="mx-2">·</span>
        <a href="{{ route('privacy') }}" class="text-brand-600 font-bold hover:underline">Baca Kebijakan Privasi Retronics →</a>
    </div>

</div>
@endsection
