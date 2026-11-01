@extends('layouts.app')

@section('title', 'Kontak & Sekretariat')
@section('meta_description', 'Informasi alamat sekretariat, jam pelayanan, saluran komunikasi resmi BEM, dan panduan pengajuan kerja sama.')

@section('content')
    <div class="text-white py-12 border-b" style="background-color: var(--theme-primary); border-bottom-color: var(--theme-secondary);">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-breadcrumb :items="['Kontak & Sekretariat' => null]" />
            <div class="mt-4 max-w-3xl">
                <span class="text-xs font-bold uppercase tracking-wider" style="color: var(--theme-accent);">Pusat Layanan Mahasiswa</span>
                <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight mt-1">Kontak & Sekretariat BEM</h1>
                <p class="text-sm sm:text-base text-slate-300 mt-2">
                    Kunjungi kantor sekretariat kemahasiswaan atau hubungi kanal resmi untuk keperluan audiensi, perizinan kegiatan ormawa, dan kemitraan kampus.
                </p>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <!-- Left Info Cards -->
            <div class="lg:col-span-6 space-y-6">
                <!-- Card 1: Alamat & Jam Kerja -->
                <div class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 shadow-sm space-y-6">
                    <h2 class="text-xl font-bold text-slate-900 border-b border-slate-100 pb-3">Sekretariat Utama</h2>

                    <div class="space-y-4 text-xs sm:text-sm text-slate-700">
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-900 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            </div>
                            <div>
                                <span class="text-xs text-slate-400 font-medium block">Lokasi Kantor:</span>
                                <span class="font-semibold text-slate-900">Gedung Pusat Kegiatan Mahasiswa (PKM) Lt. 1</span>
                                <p class="text-slate-500 mt-0.5">Kompleks Perguruan Tinggi Terpadu</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-800 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div>
                                <span class="text-xs text-slate-400 font-medium block">Jam Layanan Mahasiswa:</span>
                                <span class="font-semibold text-slate-900">Senin &ndash; Jumat: 09:00 &ndash; 16:30 WIB</span>
                                <p class="text-slate-500 mt-0.5">Sabtu & Minggu: Tutup (Khusus agenda ormawa terjadwal)</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-800 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            </div>
                            <div>
                                <span class="text-xs text-slate-400 font-medium block">Surel Korespondensi Resmi:</span>
                                <span class="font-mono text-slate-900 font-semibold">sekretariat@bem.kampus.ac.id</span>
                                <p class="text-slate-500 mt-0.5 text-xs italic">(Alamat simulasi pengembangan)</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Saluran Media Sosial Ormawa -->
                <div class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 shadow-sm space-y-4">
                    <h2 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3">Kanal Informasi Digital</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs sm:text-sm">
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 flex items-center gap-3">
                            <span class="w-7 h-7 rounded-lg bg-pink-100 text-pink-700 flex items-center justify-center font-bold">IG</span>
                            <div>
                                <span class="font-semibold text-slate-800 block">@bemkampus_official</span>
                                <span class="text-xs text-slate-400">Instagram Resmi</span>
                            </div>
                        </div>
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 flex items-center gap-3">
                            <span class="w-7 h-7 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center font-bold">TG</span>
                            <div>
                                <span class="font-semibold text-slate-800 block">Kanal Warta BEM</span>
                                <span class="text-xs text-slate-400">Telegram Mahasiswa</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Box: Panduan Pengajuan & Banner Aspirasi -->
            <div class="lg:col-span-6 space-y-6">
                <!-- Panduan Prosedur Audiensi -->
                <div class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 shadow-sm space-y-4">
                    <h2 class="text-xl font-bold text-slate-900 border-b border-slate-100 pb-3">Tata Cara Permohonan Audiensi / Pertemuan</h2>
                    <ol class="space-y-3 text-xs sm:text-sm text-slate-700">
                        <li class="flex items-start gap-3">
                            <span class="w-6 h-6 rounded-full bg-slate-900 text-white flex items-center justify-center text-xs font-bold shrink-0">1</span>
                            <div>
                                <strong class="text-slate-900 block">Pengajuan Surat Resmi</strong>
                                <span>Kirimkan surat permohonan audiensi minimal 5 hari kerja sebelum tanggal pertemuan yang diusulkan.</span>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="w-6 h-6 rounded-full bg-slate-900 text-white flex items-center justify-center text-xs font-bold shrink-0">2</span>
                            <div>
                                <strong class="text-slate-900 block">Penyertaan Pokok Pembahasan (TOR)</strong>
                                <span>Lampirkan uraian pokok gagasan atau agenda kerja sama yang akan didiskusikan bersama pengurus.</span>
                            </div>
                        </li>
                        <li class="flex items-start gap-3">
                            <span class="w-6 h-6 rounded-full bg-slate-900 text-white flex items-center justify-center text-xs font-bold shrink-0">3</span>
                            <div>
                                <strong class="text-slate-900 block">Konfirmasi Jadwal dari Sekretariat</strong>
                                <span>Biro sekretariat akan memvalidasi ketersediaan waktu dan mengirimkan tautan / undangan ruang sidang resmi.</span>
                            </div>
                        </li>
                    </ol>
                </div>

                <!-- Perhatian Aspirasi -->
                <div class="rounded-2xl p-6 sm:p-8 text-white space-y-4 border"
                     style="background: linear-gradient(135deg, var(--theme-secondary) 0%, var(--theme-primary) 100%); border-color: var(--theme-secondary);">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-md text-xs font-semibold"
                         style="background-color: var(--theme-secondary); color: var(--theme-accent);">
                        Kanal Aspirasi Lebih Efektif
                    </div>
                    <h3 class="text-lg sm:text-xl font-bold">Ingin Mengajukan Keluhan atau Usulan Kampus?</h3>
                    <p class="text-xs sm:text-sm text-slate-300 leading-relaxed">
                        Untuk penanganan keluhan fasilitas atau masalah akademik, silakan gunakan kanal aspirasi resmi agar mendapatkan tiket pelacakan otomatis dan penanganan terukur oleh divisi advokasi.
                    </p>
                    <a href="{{ route('aspirations.info') }}"
                       class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg text-slate-950 font-bold text-xs sm:text-sm transition-all hover:opacity-90"
                       style="background-color: var(--theme-accent); color: #020617;">
                        <span>Menuju Informasi Aspirasi</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
