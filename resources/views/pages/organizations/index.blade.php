@extends('layouts.app')

@section('title', 'Struktur & Lembaga Organisasi Mahasiswa')
@section('meta_description', 'Mengenal struktur kelembagaan kemahasiswaan: BEM, MAM, DPM, HIMA, HMPS, dan Unit Kegiatan Mahasiswa (UKM).')

@section('content')
    <div class="text-white py-12 border-b" style="background-color: var(--theme-primary); border-bottom-color: var(--theme-secondary);">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-breadcrumb :items="['Struktur Organisasi' => null]" />
            <div class="mt-4 max-w-3xl">
                <span class="text-xs font-bold uppercase tracking-wider" style="color: var(--theme-accent);">Sinergi Kelembagaan</span>
                <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight mt-1">Struktur Organisasi Mahasiswa</h1>
                <p class="text-sm sm:text-base text-slate-300 mt-2">
                    Pemahaman menyeluruh mengenai hierarki, fungsi eksekutif, perwakilan legislatif, himpunan fakultas/prodi, serta wadah minat bakat mahasiswa.
                </p>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-12">
        <!-- Notice box -->
        <div class="p-4 sm:p-5 rounded-2xl bg-amber-50 border border-amber-200/80 text-amber-900 text-xs sm:text-sm flex items-start gap-3">
            <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div>
                <strong class="font-bold block mb-0.5">Kerangka Arsitektur Kelembagaan (Data Demonstrasi):</strong>
                Uraian fungsi di bawah ini merupakan pedoman tata kelola umum organisasi kemahasiswaan. Profil kepengurusan definitif, biodata fungsionaris resmi, dan logo resmi kampus akan diintegrasikan melalui modul manajemen organisasi pada tahap backend selanjutnya.
            </div>
        </div>

        <!-- Hierarchy Flowchart Visual Representation -->
        <div class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 shadow-sm">
            <h2 class="text-lg font-bold text-slate-900 mb-2">Bagan Hubungan Kelembagaan Mahasiswa</h2>
            <p class="text-xs sm:text-sm text-slate-500 mb-6">Prinsip pemisahan kekuasaan eksekutif, perwakilan, dan koordinasi tingkat fakultas.</p>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-center">
                <!-- Box 1 -->
                <div class="p-4 rounded-xl bg-slate-900 text-white border border-slate-800">
                    <span class="text-xs font-mono text-amber-400 font-bold block mb-1">LEGISLATIF & ASPIRATIF</span>
                    <h3 class="font-extrabold text-base">MAM & DPM</h3>
                    <p class="text-xs text-slate-300 mt-1">Musyawarah, Pengawasan, & Regulasi Ormawa</p>
                </div>

                <!-- Box 2 -->
                <div class="p-4 rounded-xl bg-blue-950 text-white border border-blue-900">
                    <span class="text-xs font-mono text-amber-400 font-bold block mb-1">EKSEKUTIF UNIVERSITAS</span>
                    <h3 class="font-extrabold text-base">BEM (Badan Eksekutif Mahasiswa)</h3>
                    <p class="text-xs text-slate-300 mt-1">Koordinator Program Kerja & Advokasi Kampus</p>
                </div>

                <!-- Box 3 -->
                <div class="p-4 rounded-xl bg-slate-100 text-slate-800 border border-slate-200">
                    <span class="text-xs font-mono text-slate-500 font-bold block mb-1">FAKULTAS, PRODI & MINAT</span>
                    <h3 class="font-extrabold text-base">HIMA, HMPS, & UKM</h3>
                    <p class="text-xs text-slate-600 mt-1">Spesialisasi Akademik, Profesi, & Bakat</p>
                </div>
            </div>

            <!-- Banner Tautan ke Struktur Kepengurusan Kabinet BEM -->
            <div class="mt-6 p-4 rounded-xl bg-gradient-to-r from-blue-900 to-indigo-900 text-white flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="space-y-1">
                    <span class="text-xs font-bold uppercase tracking-wider text-amber-400">Fungsionaris Eksekutif BEM</span>
                    <h3 class="text-sm sm:text-base font-extrabold">Ingin melihat susunan menteri dan staf kabinet BEM periode ini?</h3>
                    <p class="text-xs text-slate-300">Lihat profil Presiden Mahasiswa, Wapresma, BPH, dan departemen kementerian secara lengkap.</p>
                </div>
                <a href="{{ route('bem.structure') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs shrink-0 transition-colors">
                    <span>Lihat Struktur Pengurus BEM</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </a>
            </div>
        </div>

        <!-- Organizations Detail Cards -->
        <div class="space-y-8">
            <h2 class="text-xl sm:text-2xl font-bold text-slate-900">Rincian Peran Tiap Entitas Organisasi</h2>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                @foreach($organizations as $org)
                    <div class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 shadow-sm flex flex-col justify-between hover:shadow-md hover:border-slate-300 transition-all space-y-6">
                        <div class="space-y-4">
                            <div class="flex items-center justify-between gap-3 border-b border-slate-100 pb-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 rounded-xl bg-slate-900 text-amber-400 font-extrabold text-base flex items-center justify-center shrink-0 border border-amber-400/30">
                                        {{ $org['code'] }}
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-slate-900 text-lg sm:text-xl">{{ $org['name'] }}</h3>
                                        <span class="text-xs text-slate-500 font-medium">{{ $org['level'] }}</span>
                                    </div>
                                </div>
                                <span class="px-2.5 py-1 rounded text-xs font-semibold bg-slate-100 text-slate-800 border border-slate-200 shrink-0">
                                    {{ $org['role_type'] }}
                                </span>
                            </div>

                            <p class="text-xs sm:text-sm text-slate-700 leading-relaxed">
                                {{ $org['description'] }}
                            </p>

                            <!-- Fungsi Pokok -->
                            <div class="space-y-2 pt-2">
                                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500">Tugas Pokok & Fungsi:</h4>
                                <ul class="space-y-1.5 text-xs sm:text-sm text-slate-600">
                                    @foreach($org['functions'] as $fn)
                                        <li class="flex items-start gap-2">
                                            <span class="text-amber-600 font-bold">&bull;</span>
                                            <span>{{ $fn }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>

                            <!-- Struktur Overview -->
                            <div class="space-y-2 pt-2">
                                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500">Gambaran Bidang / Rumpun:</h4>
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach($org['structure_overview'] as $unit)
                                        <span class="px-2.5 py-1 rounded-md text-xs bg-slate-50 border border-slate-200 text-slate-700">
                                            {{ $unit }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-400">
                            <span class="italic">Pengurus resmi akan terhubung di Fase Backend</span>
                            <a href="{{ route('aspirations.info') }}" class="font-semibold text-amber-700 hover:text-amber-800 flex items-center gap-1">
                                <span>Kirim Masukan untuk {{ $org['code'] }} &rarr;</span>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
