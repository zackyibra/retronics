<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulir Penyewaan {{ $rental->rental_code }} - Retronics Store</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @page {
            size: A4 portrait;
            margin: 10mm 12mm 10mm 12mm;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f1f5f9;
            color: #0f172a;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .font-mono {
            font-family: 'JetBrains Mono', monospace;
        }

        @media print {
            .no-print {
                display: none !important;
            }

            body {
                background-color: #ffffff !important;
                padding: 0 !important;
                margin: 0 !important;
            }

            .print-paper {
                box-shadow: none !important;
                border: none !important;
                border-radius: 0 !important;
                padding: 0 !important;
                max-width: 100% !important;
                width: 100% !important;
            }

            .page-break-avoid {
                page-break-inside: avoid;
                break-inside: avoid;
            }
        }
    </style>
</head>
<body class="min-h-screen py-6 px-4 print:p-0 print:bg-white">

    <!-- FLOATING TOP ACTION BAR (HIDDEN IN PRINT) -->
    <div class="no-print max-w-4xl mx-auto mb-6 flex flex-col sm:flex-row items-center justify-between gap-3 bg-slate-900 text-white px-5 py-3.5 rounded-2xl shadow-lg">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.rentals.index', ['rental' => $rental->id]) }}"
               class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-xs font-bold text-slate-200 transition flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                <span>Kembali</span>
            </a>
            <div>
                <span class="text-xs font-bold text-slate-100 block">Formulir Penyewaan Siap Cetak (A4)</span>
                <span class="text-[11px] text-slate-400 font-mono">Kode: {{ $rental->rental_code }}</span>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <button type="button"
                    onclick="window.print()"
                    class="px-5 py-2 rounded-xl bg-brand-600 hover:bg-brand-500 text-white font-bold text-xs shadow-md shadow-brand-500/20 transition flex items-center gap-2 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Cetak / Simpan PDF</span>
            </button>
        </div>
    </div>

    <!-- A4 PAPER CONTAINER -->
    <div class="print-paper max-w-4xl mx-auto bg-white p-8 sm:p-10 rounded-3xl border border-slate-200 shadow-xl text-slate-900 leading-relaxed font-sans">

        <!-- KOP SURAT RESMI RETRONICS -->
        <div class="border-b-2 border-slate-900 pb-3 mb-5 flex items-start justify-between">
            <div class="flex items-center gap-3.5">
                <img src="{{ asset('images/logo.jpg') }}" alt="Logo Retronics" class="w-12 h-12 object-contain rounded-2xl border border-slate-300 bg-white shadow-sm flex-shrink-0">
                <div>
                    <h1 class="text-xl font-extrabold tracking-tight text-slate-900 uppercase leading-none">
                        RETRONICS INDONESIA
                    </h1>
                    <p class="text-xs font-bold text-slate-600 mt-1">
                        Pusat Penyewaan Kamera, Lensa & Peralatan Produksi Multimedia Profesional
                    </p>
                    <p class="text-[11px] text-slate-500">
                        Jl. Pabuaran, Kp. Pabuaran, Desa Cikeas Udik, Kecamatan Gunung Putri, Kabupaten Bogor, Jawa Barat, Indonesia · WA: 0819-2951-3780
                    </p>
                </div>
            </div>

            <div class="text-right flex-shrink-0">
                <span class="inline-block px-2.5 py-1 bg-slate-100 text-slate-800 rounded-md text-[10px] font-bold tracking-wider uppercase mb-1">
                    DOKUMEN RESMI TOKO
                </span>
                <p class="text-[11px] font-mono font-bold text-slate-800">
                    No: SPK/{{ $rental->created_at->format('Ymd') }}/{{ $rental->id }}
                </p>
                <p class="text-[10px] text-slate-500">
                    Tgl: {{ $rental->created_at->translatedFormat('d F Y') }}
                </p>
            </div>
        </div>

        <!-- JUDUL FORMULIR -->
        <div class="text-center mb-5">
            <h2 class="text-base sm:text-lg font-extrabold uppercase tracking-wider text-slate-900 border-b border-slate-300 pb-1 inline-block">
                FORMULIR PENYEWAAN & BUKTI SERAH TERIMA BARANG
            </h2>
            <div class="text-xs text-slate-600 mt-1 flex items-center justify-center gap-3">
                <span>Kode Sewa: <strong class="font-mono text-slate-900">{{ $rental->rental_code }}</strong></span>
                <span>·</span>
                <span>Status Transaksi: <strong class="text-slate-900">{{ $rental->status_label }}</strong></span>
            </div>
        </div>

        @php
            $maskedNik = $maskedNik ?? '-';
        @endphp

        <!-- 01 / DATA PENYEWA & JADWAL SEWA (2 KOLOM) -->
        <div class="grid grid-cols-2 gap-4 mb-4 text-xs">

            <!-- Data Penyewa (PIHAK KEDUA) -->
            <div class="p-3.5 rounded-xl border border-slate-300 bg-slate-50/50 space-y-1">
                <strong class="text-slate-900 block font-bold uppercase tracking-wider text-[11px] border-b border-slate-200 pb-1">
                    I. Identitas Penyewa (Pihak Kedua)
                </strong>
                <table class="w-full text-[11px]">
                    <tr>
                        <td class="w-28 text-slate-500 py-0.5">Nama Lengkap</td>
                        <td class="w-2">:</td>
                        <td class="font-bold text-slate-900">{{ $rental->user->name }}</td>
                    </tr>
                    <tr>
                        <td class="text-slate-500 py-0.5">Nomor NIK KTP</td>
                        <td>:</td>
                        <td class="font-mono font-bold text-slate-800">{{ $maskedNik ?? '-' }}</td>
                    </tr>
                    @if($rental->verification?->birth_date)
                        <tr>
                            <td class="text-slate-500 py-0.5">Tanggal Lahir</td>
                            <td>:</td>
                            <td class="text-slate-800">{{ $rental->verification->birth_date->translatedFormat('d F Y') }} ({{ \Carbon\Carbon::parse($rental->verification->birth_date)->age }} th - Memenuhi batas usia min. 17 th)</td>
                        </tr>
                    @endif
                    <tr>
                        <td class="text-slate-500 py-0.5">WhatsApp / HP</td>
                        <td>:</td>
                        <td class="text-slate-800">{{ $rental->user->phone ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="text-slate-500 py-0.5">Alamat Domisili</td>
                        <td>:</td>
                        <td class="text-slate-800">{{ $rental->user->address ?? 'Gunung Putri, Bogor' }}</td>
                    </tr>
                    <tr>
                        <td class="text-slate-500 py-0.5">Tujuan Sewa</td>
                        <td>:</td>
                        <td class="italic text-slate-800">{{ $rental->verification?->rental_purpose ?? 'Keperluan Produksi Fotografi' }}</td>
                    </tr>
                    <tr>
                        <td class="text-slate-500 py-0.5">Kontak Darurat</td>
                        <td>:</td>
                        <td class="text-slate-800">{{ $rental->verification?->emergency_contact_name ?? '-' }} ({{ $rental->verification?->emergency_contact_relation ?? '-' }} · {{ $rental->verification?->emergency_contact_phone ?? '-' }})</td>
                    </tr>
                </table>
            </div>

            <!-- Jadwal & Durasi Sewa -->
            <div class="p-3.5 rounded-xl border border-slate-300 bg-slate-50/50 space-y-1 flex flex-col justify-between">
                <div>
                    <strong class="text-slate-900 block font-bold uppercase tracking-wider text-[11px] border-b border-slate-200 pb-1">
                        II. Jadwal & Prosedur Serah Terima
                    </strong>
                    <table class="w-full text-[11px] mt-1">
                        <tr>
                            <td class="w-28 text-slate-500 py-0.5">Pengambilan</td>
                            <td class="w-2">:</td>
                            <td class="font-bold text-slate-900">{{ $rental->start_date->translatedFormat('d F Y') }} (pkl 10.00 WIB)</td>
                        </tr>
                        <tr>
                            <td class="text-slate-500 py-0.5">Batas Kembali</td>
                            <td class="w-2">:</td>
                            <td class="font-bold text-slate-900">{{ $rental->end_date->translatedFormat('d F Y') }} (maks pkl 21.00 WIB)</td>
                        </tr>
                        <tr>
                            <td class="text-slate-500 py-0.5">Durasi Total</td>
                            <td>:</td>
                            <td class="font-bold text-brand-700">{{ $rental->duration_days }} Hari Kalender</td>
                        </tr>
                        <tr>
                            <td class="text-slate-500 py-0.5">Metode Bayar</td>
                            <td>:</td>
                            <td class="text-slate-800 uppercase font-semibold">{{ str_replace('_', ' ', $rental->payment_method ?? 'Transfer') }}</td>
                        </tr>
                    </table>
                </div>

                <div class="pt-2 border-t border-slate-200 text-[10px] text-slate-600">
                    Lokasi Serah Terima: <strong>Store Retronics Gunung Putri, Bogor</strong> (Jl. Pabuaran, Kp. Pabuaran). Store Pickup Only.
                </div>
            </div>

        </div>

        <!-- 02 / TABEL PERALATAN YANG DISEWA -->
        <div class="mb-4">
            <strong class="text-slate-900 block font-bold uppercase tracking-wider text-[11px] mb-1">
                III. Rincian Peralatan / Unit yang Disewa
            </strong>
            <table class="w-full text-xs border border-slate-300 rounded-lg overflow-hidden">
                <thead class="bg-slate-100 text-slate-800 border-b border-slate-300">
                    <tr>
                        <th class="py-1 px-2 text-center w-8 font-bold">No</th>
                        <th class="py-1 px-3 text-left font-bold">Nama Gear / Peralatan</th>
                        <th class="py-1 px-2 text-left font-bold">Kategori</th>
                        <th class="py-1 px-2 text-center font-bold">Kondisi Fisik Serah Terima</th>
                        <th class="py-1 px-2 text-center font-bold">Jumlah</th>
                        <th class="py-1 px-3 text-right font-bold">Tarif / Hari</th>
                        <th class="py-1 px-3 text-right font-bold">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 text-[11px]">
                    @foreach($rental->items as $idx => $item)
                        <tr>
                            <td class="py-1 px-2 text-center text-slate-500">{{ $idx + 1 }}</td>
                            <td class="py-1 px-3 font-bold text-slate-900">{{ $item->product->name }}</td>
                            <td class="py-1 px-2 text-slate-600">{{ $item->product->category->name ?? 'Gear' }}</td>
                            <td class="py-1 px-2 text-center text-slate-700">Baik, Bersih & Normal [✓]</td>
                            <td class="py-1 px-2 text-center font-bold text-slate-900">{{ $item->quantity }} unit</td>
                            <td class="py-1 px-3 text-right text-slate-700">Rp{{ number_format($item->price_per_day, 0, ',', '.') }}</td>
                            <td class="py-1 px-3 text-right font-bold text-slate-900">Rp{{ number_format($item->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-slate-50 border-t border-slate-300 text-xs">
                    @php
                        $grossSum = $rental->items->sum('subtotal');
                        $diff = $grossSum - $rental->total_price;
                    @endphp
                    <tr>
                        <td colspan="6" class="py-1 px-3 text-right font-semibold text-slate-600">Subtotal Biaya Sewa:</td>
                        <td class="py-1 px-3 text-right font-bold text-slate-900">Rp{{ number_format($grossSum, 0, ',', '.') }}</td>
                    </tr>
                    @if($diff > 0)
                        <tr>
                            <td colspan="6" class="py-1 px-3 text-right font-semibold text-emerald-700">
                                Potongan Diskon Durasi / Promo:
                            </td>
                            <td class="py-1 px-3 text-right font-bold text-emerald-700">-Rp{{ number_format($diff, 0, ',', '.') }}</td>
                        </tr>
                    @endif
                    @if($rental->verification?->requires_stamp_duty || $rental->total_price >= 5000000)
                        <tr>
                            <td colspan="6" class="py-1 px-3 text-right font-semibold text-slate-600">
                                Bea Meterai UU No. 10 Tahun 2020:
                            </td>
                            <td class="py-1 px-3 text-right font-bold text-slate-900">Rp10.000 (Lunas Difasilitasi Toko)</td>
                        </tr>
                    @endif
                    <tr class="border-t-2 border-slate-900 bg-slate-100">
                        <td colspan="6" class="py-1.5 px-3 text-right font-extrabold text-slate-900 text-xs uppercase">
                            Total Biaya Sewa yang Disepakati:
                        </td>
                        <td class="py-1.5 px-3 text-right font-extrabold text-brand-700 text-sm">
                            Rp{{ number_format($rental->total_price, 0, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- 03 / KETENTUAN POKOK PERJANJIAN SEWA (RINGKASAN YURIDIS LENGKAP) -->
        <div class="mb-4 p-3 rounded-xl border border-slate-300 bg-slate-50/70 text-[10px] text-slate-700 space-y-1 leading-relaxed">
            <strong class="text-slate-900 block font-bold uppercase tracking-wider text-[10px] mb-0.5">
                IV. Ringkasan Klausul & Ketentuan Pokok Perjanjian Sewa Retronics
            </strong>
            <p><strong>1. Hak & Kewajiban Toko:</strong> Retronics wajib menyerahkan gear layak pakai dan bersih, menguji fungsi awal bersama penyewa, menjaga kerahasiaan data pribadi, serta berhak menolak sewa tanpa identitas KTP sah atau di bawah 17 tahun.</p>
            <p><strong>2. Hak & Kewajiban Penyewa:</strong> Penyewa berhak menerima unit prima dan konsultasi teknis, serta berkewajiban merawat unit, melarang pemindahtanganan / gadai / *sub-rent*, dan mengembalikan barang tepat waktu.</p>
            <p><strong>3. Pengambilan & Pengembalian:</strong> Dilakukan langsung di Store Retronics Gunung Putri, Kab. Bogor (*Store Pickup Only*) disertai pemeriksaan bersama (*joint inspection*).</p>
            <p><strong>4. Batas Waktu & Denda Terlambat:</strong> Batas pengembalian maks pukul 21.00 WIB pada tanggal {{ $rental->end_date->translatedFormat('d F Y') }}. Keterlambatan dikenakan denda resmi <strong>Rp50.000,- / hari kalender / unit</strong> secara kumulatif.</p>
            <p><strong>5. Kerusakan & Kehilangan:</strong> Keausan normal (*fair wear and tear*) bukan kelalaian. Kerusakan akibat benturan, cairan, atau kelalaian pemakaian dibebankan biaya perbaikan resmi. Kehilangan unit wajib diganti unit setara atau nilai pasar wajar.</p>
            <p><strong>6. Bea Meterai & Keabsahan Perjanjian:</strong> Sesuai UU No. 10/2020 Bea Meterai adalah pajak dokumen perdata pembuktian. Ketiadaan meterai tidak membatalkan keabsahan perjanjian perdata berdasarkan <strong>Pasal 1320 KUHPerdata</strong>.</p>
            <p><strong>7. UU PDP & Penyelesaian Sengketa:</strong> Perlindungan dokumen dan data pribadi tunduk pada UU No. 27/2022. Perselisihan diselesaikan melalui musyawarah mufakat atau domisili hukum Pengadilan Negeri Kabupaten Bogor.</p>
        </div>

        <!-- 04 / LEMBAR PENGESAHAN & TANDA TANGAN PARA PIHAK -->
        <div class="page-break-avoid pt-2 border-t-2 border-slate-900">
            <div class="text-right text-[11px] text-slate-600 mb-2">
                Gunung Putri, Bogor, {{ $rental->created_at->translatedFormat('d F Y') }}
            </div>

            <div class="grid grid-cols-2 gap-6 items-start text-xs">

                <!-- SISI KIRI: PIHAK PERTAMA (ADMIN / PETUGAS TOKO) -->
                <div class="text-center p-3 rounded-xl border border-slate-300 bg-slate-50/30">
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">PIHAK PERTAMA</p>
                    <p class="font-extrabold text-slate-900 text-xs">Petugas Operasional Retronics Store</p>

                    <!-- RUANG TANDA TANGAN MANUAL ADMIN (TIDAK DIBUAT OTOMATIS) -->
                    <div class="h-24 flex flex-col items-center justify-center my-2 border border-dashed border-slate-300 rounded-lg bg-white p-2">
                        <span class="text-[10px] text-slate-400 font-medium italic">
                            (Ruang tanda tangan basah & stempel toko saat penyerahan barang di store)
                        </span>
                    </div>

                    <div class="border-t border-slate-400 pt-1 mt-1">
                        <p class="font-bold text-slate-900">( .................................................... )</p>
                        <p class="text-[10px] text-slate-500">Petugas Serah Terima Toko</p>
                    </div>
                </div>

                <!-- SISI KANAN: PIHAK KEDUA (PENYEWA) -->
                <div class="text-center p-3 rounded-xl border border-slate-300 bg-slate-50/30 relative">
                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">PIHAK KEDUA</p>
                    <p class="font-extrabold text-slate-900 text-xs">Penyewa / Penerima Barang</p>

                    <!-- TANDA TANGAN DIGITAL PELANGGAN (JIKA ADA) ATAU STATUS BELUM BERTANDA TANGAN -->
                    <div class="h-24 flex flex-col items-center justify-center my-2 border border-slate-300 rounded-lg bg-white p-2 relative overflow-hidden">
                        @if($rental->verification?->requires_stamp_duty || $rental->total_price >= 5000000)
                            <div class="absolute -top-1 left-2 w-16 h-20 rounded-lg border border-dashed border-emerald-600 bg-emerald-50/95 text-emerald-800 p-1 flex flex-col items-center justify-between shadow-xs z-0 rotate-1">
                                <span class="text-[8px] font-extrabold uppercase text-emerald-700">METERAI</span>
                                <span class="text-[10px] font-black text-emerald-900">10000</span>
                                <span class="text-[7px] font-semibold text-center text-emerald-600">UU 10/2020</span>
                            </div>
                        @endif

                        <div class="relative z-10">
                            @if($rental->verification && $rental->verification->signature_data)
                                <img src="{{ $rental->verification->signature_data }}"
                                     alt="Tanda Tangan Digital Penyewa"
                                     class="max-h-16 max-w-full object-contain mx-auto">
                                <span class="text-[8px] text-emerald-700 font-bold block mt-0.5">
                                    ✓ Ditandatangani secara elektronik
                                </span>
                            @else
                                <div class="p-2 text-center">
                                    <span class="text-[9px] font-bold text-rose-700 block">
                                        [ DOKUMEN BELUM DITANDATANGANI SECARA ELEKTRONIK ]
                                    </span>
                                    <span class="text-[8px] text-slate-400 block mt-1">
                                        (Bubuhkan tanda tangan basah langsung pada area ini)
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="border-t border-slate-400 pt-1 mt-1">
                        <p class="font-bold text-slate-900 font-display">
                            {{ $rental->user->name }}
                        </p>
                        @if($rental->verification && $rental->verification->agreement_signed_at)
                            <p class="text-[9px] text-slate-500">
                                Waktu TTD: {{ $rental->verification->agreement_signed_at->translatedFormat('d M Y, H.i') }} WIB
                            </p>
                        @else
                            <p class="text-[10px] text-slate-500">Penyewa Utama</p>
                        @endif
                    </div>
                </div>

            </div>
        </div>

    </div>

</body>
</html>
