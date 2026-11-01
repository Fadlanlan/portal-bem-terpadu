<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Alamat Email Notifikasi Administrator
    |--------------------------------------------------------------------------
    |
    | Alamat surel pengelola / BEM yang akan menerima pemberitahuan setiap
    | kali ada aspirasi mahasiswa baru yang masuk ke dalam sistem.
    |
    */
    'admin_notification_email' => env('ADMIN_NOTIFICATION_EMAIL', 'admin@bem.kampus.ac.id'),

    /*
    |--------------------------------------------------------------------------
    | Pengaturan Batas Ekspor Laporan
    |--------------------------------------------------------------------------
    |
    | Rentang hari maksimal yang diizinkan untuk query filter dan ekspor CSV
    | guna menjaga performa basis data dan mencegah beban kueri berlebih.
    |
    */
    'reports' => [
        'max_export_days' => 1825, // Maksimal 5 tahun
    ],
];
