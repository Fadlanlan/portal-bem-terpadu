@extends('layouts.app')

@section('title', 'Kanal Aspirasi Mahasiswa')
@section('meta_description', 'Panduan dan kanal resmi penyampaian aspirasi, saran, keluhan, dan advokasi hak mahasiswa dengan sistem tiket terlacak.')

@section('content')
    <div class="text-white py-12 border-b" style="background-color: var(--theme-primary); border-bottom-color: var(--theme-secondary);">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-breadcrumb :items="['Kanal Aspirasi Mahasiswa' => null]" />
            <div class="mt-4">
                <span class="text-xs font-bold uppercase tracking-wider" style="color: var(--theme-accent);">Fitur Utama Mahasiswa</span>
                <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight mt-1">Kanal Aspirasi Mahasiswa Terpadu</h1>
                <p class="text-sm sm:text-base text-slate-300 mt-2">
                    Kanal resmi penyampaian aspirasi, kritik konstruktif, pengaduan fasilitas, dan permohonan advokasi dengan alur penanganan terukur dan nomor tiket pelacakan.
                </p>
            </div>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-10">
        <!-- Quick Action Hero Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <!-- Card 1: Kirim Aspirasi -->
            <div class="rounded-2xl p-6 sm:p-8 border shadow-md flex flex-col justify-between space-y-6 text-white"
                 style="background: linear-gradient(135deg, var(--theme-secondary) 0%, var(--theme-primary) 100%); border-color: var(--theme-secondary);">
                <div class="space-y-3">
                    <div class="w-10 h-10 rounded-xl border flex items-center justify-center font-bold"
                         style="background-color: var(--theme-primary); border-color: var(--theme-accent); color: var(--theme-accent);">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-white">Sampaikan Aspirasi Baru</h2>
                    <p class="text-xs sm:text-sm text-slate-300 leading-relaxed">
                        Kirimkan suara, saran perbaikan fasilitas, atau keluhan akademik secara langsung. Tanpa perlu registrasi akun, dengan pilihan identitas anonim atau terbuka.
                    </p>
                </div>
                <a href="{{ route('aspirations.create') }}"
                   class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl text-slate-950 font-bold text-sm shadow-md transition-all hover:opacity-90"
                   style="background-color: var(--theme-accent); color: #020617;">
                    <span>Isi Formulir Aspirasi</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </a>
            </div>

            <!-- Card 2: Lacak Tiket -->
            <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200 shadow-sm flex flex-col justify-between space-y-6">
                <div class="space-y-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-900 flex items-center justify-center font-bold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-slate-900">Lacak Status Aspirasi</h2>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                        Sudah memiliki Nomor Tiket dan Kode Pelacakan Rahasia? Pantau perkembangan penanganan, catatan tim advokasi, dan respons resmi pengelola secara berkala.
                    </p>
                </div>
                <a href="{{ route('aspirations.tracking.form') }}"
                   class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-semibold text-sm transition-colors">
                    <span>Buka Pelacakan Tiket</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </a>
            </div>
        </div>

        @if(session('status_notice'))
            <div class="p-4 rounded-xl bg-blue-50 border border-blue-200 text-blue-900 text-xs sm:text-sm">
                {{ session('status_notice') }}
            </div>
        @endif

        <!-- 4 Pilar Privasi & Keamanan Aspirasi -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm space-y-2">
                <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-900 flex items-center justify-center font-bold text-xs">
                    1
                </div>
                <h3 class="font-bold text-slate-900 text-base">Nomor Tiket & Kode Rahasia</h3>
                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                    Setiap laporan yang terkirim menghasilkan Nomor Tiket publik dan Kode Pelacakan Rahasia yang disimpan dalam bentuk hash enkripsi di basis data server.
                </p>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm space-y-2">
                <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-900 flex items-center justify-center font-bold text-xs">
                    2
                </div>
                <h3 class="font-bold text-slate-900 text-base">Pilihan Identitas Jujur</h3>
                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                    Sistem mendukung mode anonim sejati. Jika Anda memilih anonim, server sama sekali tidak menyimpan nama, surel, atau identitas pribadi Anda di basis data.
                </p>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm space-y-2">
                <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-900 flex items-center justify-center font-bold text-xs">
                    3
                </div>
                <h3 class="font-bold text-slate-900 text-base">Perlindungan Anti-Spam & Rate Limit</h3>
                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                    Setiap pengiriman dan upaya pelacakan dilindungi oleh pembatasan frekuensi (rate limiting) dan verifikasi token keamanan CSRF otomatis.
                </p>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm space-y-2">
                <div class="w-8 h-8 rounded-lg bg-purple-100 text-purple-900 flex items-center justify-center font-bold text-xs">
                    4
                </div>
                <h3 class="font-bold text-slate-900 text-base">Privasi Pelapor Terjaga</h3>
                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                    Halaman pelacakan status tidak pernah menampilkan identitas nama atau surel pelapor untuk mencegah kebocoran informasi kepada pihak lain.
                </p>
            </div>
        </div>

        <!-- 6 Status Siklus Hidup Aspirasi -->
        <div class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 shadow-sm space-y-4">
            <h2 class="text-lg font-bold text-slate-900 border-b border-slate-100 pb-3">6 Status Siklus Hidup Penanganan</h2>

            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 text-xs">
                <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 text-center">
                    <span class="px-2 py-0.5 rounded font-bold bg-slate-200 text-slate-800">DITERIMA</span>
                    <p class="text-slate-500 mt-2">Laporan baru masuk ke antrean sistem.</p>
                </div>
                <div class="p-3 rounded-xl bg-blue-50/60 border border-blue-200 text-center">
                    <span class="px-2 py-0.5 rounded font-bold bg-blue-100 text-blue-800">DITINJAU</span>
                    <p class="text-slate-500 mt-2">Petugas memeriksa kelayakan laporan.</p>
                </div>
                <div class="p-3 rounded-xl bg-amber-50/60 border border-amber-200 text-center">
                    <span class="px-2 py-0.5 rounded font-bold bg-amber-100 text-amber-800">DIPROSES</span>
                    <p class="text-slate-500 mt-2">Diteruskan ke kementerian BEM / biro kampus terkait.</p>
                </div>
                <div class="p-3 rounded-xl bg-purple-50/60 border border-purple-200 text-center">
                    <span class="px-2 py-0.5 rounded font-bold bg-purple-100 text-purple-800">MENUNGGU TINDAK LANJUT</span>
                    <p class="text-slate-500 mt-2">Menunggu respons audiensi pimpinan.</p>
                </div>
                <div class="p-3 rounded-xl bg-emerald-50/60 border border-emerald-200 text-center">
                    <span class="px-2 py-0.5 rounded font-bold bg-emerald-100 text-emerald-800">SELESAI</span>
                    <p class="text-slate-500 mt-2">Solusi telah diberikan & laporan ditutup.</p>
                </div>
                <div class="p-3 rounded-xl bg-rose-50/60 border border-rose-200 text-center">
                    <span class="px-2 py-0.5 rounded font-bold bg-rose-100 text-rose-800">DITOLAK</span>
                    <p class="text-slate-500 mt-2">Disertai alasan resmi penolakan yang objektif.</p>
                </div>
            </div>
        </div>

        <!-- Back to Home Button -->
        <div class="pt-4 text-center">
            <a href="{{ route('home') }}"
               class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-slate-900 text-white hover:bg-slate-800 font-semibold text-sm transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                <span>Kembali ke Halaman Beranda</span>
            </a>
        </div>
    </div>
@endsection
