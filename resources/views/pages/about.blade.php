@extends('layouts.app')

@section('title', 'Tentang Portal & Visi Misi')
@section('meta_description', 'Mengenal tujuan, visi, misi, dan pilar tata kelola Portal BEM Terpadu bagi mahasiswa.')

@section('content')
    <div class="text-white py-12 border-b" style="background-color: var(--theme-primary); border-bottom-color: var(--theme-secondary);">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-breadcrumb :items="['Tentang Portal' => null]" />
            <div class="mt-4 max-w-3xl">
                <span class="text-xs font-bold uppercase tracking-wider" style="color: var(--theme-accent);">Profil & Komitmen</span>
                <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight mt-1">Tentang Portal BEM Terpadu</h1>
                <p class="text-sm sm:text-base text-slate-300 mt-2">
                    Inisiatif sistem informasi digital yang dirancang untuk menjawab kebutuhan transparansi, sinergi kegiatan ormawa, dan advokasi mahasiswa yang modern dan akuntabel.
                </p>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-12">
        <!-- Visi & Misi Section -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Visi Card -->
            <div class="bg-white rounded-2xl border border-slate-200 p-8 shadow-sm space-y-4">
                <div class="w-12 h-12 rounded-xl bg-amber-500/10 text-amber-700 flex items-center justify-center font-bold text-lg border border-amber-500/20">
                    V
                </div>
                <h2 class="text-2xl font-bold text-slate-900">Visi Kelembagaan</h2>
                <p class="text-sm sm:text-base text-slate-700 leading-relaxed">
                    "Terwujudnya iklim kemahasiswaan yang kritis, inklusif, transparan, dan berdaya saing tinggi melalui tata kelola keorganisasian modern serta advokasi kepentingan mahasiswa yang terpercaya dan terukur."
                </p>
            </div>

            <!-- Misi Card -->
            <div class="bg-white rounded-2xl border border-slate-200 p-8 shadow-sm space-y-4">
                <div class="w-12 h-12 rounded-xl bg-blue-900/10 text-blue-900 flex items-center justify-center font-bold text-lg border border-blue-900/20">
                    M
                </div>
                <h2 class="text-2xl font-bold text-slate-900">Misi Utama</h2>
                <ul class="space-y-3 text-sm sm:text-base text-slate-700">
                    <li class="flex items-start gap-2.5">
                        <span class="w-5 h-5 rounded-full bg-blue-100 text-blue-900 flex items-center justify-center shrink-0 mt-0.5 text-xs font-bold">1</span>
                        <span>Membangun kanal komunikasi dan penyerapan aspirasi mahasiswa yang transparan, mudah diakses, dan terlacak secara objektif.</span>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <span class="w-5 h-5 rounded-full bg-blue-100 text-blue-900 flex items-center justify-center shrink-0 mt-0.5 text-xs font-bold">2</span>
                        <span>Mewujudkan transparansi pelaporan program kerja, evaluasi berkala, dan akuntabilitas keuangan organisasi kemahasiswaan.</span>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <span class="w-5 h-5 rounded-full bg-blue-100 text-blue-900 flex items-center justify-center shrink-0 mt-0.5 text-xs font-bold">3</span>
                        <span>Mengintegrasikan agenda seluruh organisasi mahasiswa (BEM, MAM, DPM, HIMA, HMPS, UKM) dalam satu kalender publik yang harmonis.</span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- 4 Pilar Nilai -->
        <div>
            <div class="text-center max-w-2xl mx-auto mb-10">
                <span class="text-xs font-bold uppercase tracking-wider text-amber-600">Landasan Nilai</span>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-1">Empat Pilar Pelayanan</h2>
                <p class="text-sm text-slate-600 mt-2">Prinsip dasar yang memandu perancangan portal dan pelayanan kemahasiswaan.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($pillars as $pillar)
                    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm hover:border-slate-300 transition-colors space-y-3">
                        <div class="w-10 h-10 rounded-lg bg-slate-900 text-amber-400 flex items-center justify-center font-bold text-sm">
                            &diams;
                        </div>
                        <h3 class="font-bold text-slate-900 text-base">{{ $pillar['title'] }}</h3>
                        <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">{{ $pillar['description'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Standarisasi & Disclaimer Kelembagaan -->
        <div class="p-6 sm:p-8 rounded-2xl bg-slate-100 border border-slate-200 text-slate-700 space-y-3">
            <h3 class="font-bold text-slate-900 text-base">Komitmen Keterbukaan Informasi Publik Mahasiswa</h3>
            <p class="text-xs sm:text-sm leading-relaxed">
                Portal BEM Terpadu dikembangkan dengan semangat menjunjung tinggi kebebasan akademik dan etika keorganisasian. Sistem ini dirancang untuk dioperasikan secara mandiri oleh tim mahasiswa dengan pengawasan lembaga perwakilan (MAM & DPM), serta menjunjung tinggi perlindungan data pribadi mahasiswa sesuai regulasi hukum yang berlaku.
            </p>
            <p class="text-xs text-slate-500 italic">
                Catatan Pengembang: Seluruh nama fungsionaris spesifik dan rincian kontak operasional akan dimutakhirkan secara terpadu setelah konfigurasi basis data resmi disahkan pada tahap operasional.
            </p>
        </div>
    </div>
@endsection
