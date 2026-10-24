<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Tarif Denda Keterlambatan Per Hari (Late Fee Per Day)
    |--------------------------------------------------------------------------
    |
    | Tarif denda keterlambatan per hari dalam satuan Rupiah (IDR).
    | Nilai ini diterapkan saat transaksi penyewaan dikembalikan melewati
    | tanggal batas akhir sewa (end_date).
    |
    | Pengaturan tarif dapat disesuaikan melalui environment variable:
    | RENTAL_LATE_FEE_PER_DAY=50000 pada file .env
    |
    */
    'late_fee_per_day' => (float) env('RENTAL_LATE_FEE_PER_DAY', 50000),
];
