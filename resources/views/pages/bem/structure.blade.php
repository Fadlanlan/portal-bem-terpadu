@extends('layouts.app')

@section('title', 'Struktur Kepengurusan BEM Periode ' . $activePeriod)
@section('meta_description', 'Susunan pengurus dan kabinet eksekutif Badan Eksekutif Mahasiswa (BEM) — Presiden, Wakil Presiden, BPH, Menteri, dan Koordinator Bidang.')

@section('content')
<!-- Page Header Banner -->
<div class="text-white py-14 border-b relative overflow-hidden" style="background: linear-gradient(135deg, var(--theme-primary) 0%, var(--theme-secondary) 100%); border-bottom-color: var(--theme-secondary);">
    <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#f59e0b_1px,transparent_1px)] [background-size:16px_16px]"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <x-breadcrumb :items="['Pengurus BEM' => null]" />

        <div class="mt-6 max-w-3xl space-y-3">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold border"
                 style="background-color: var(--theme-secondary); border-color: var(--theme-secondary); color: var(--theme-accent);">
                <span class="w-2 h-2 rounded-full animate-pulse" style="background-color: var(--theme-accent);"></span>
                <span>Kabinet Eksekutif Periode {{ $activePeriod }}</span>
            </div>
            <h1 class="text-3xl sm:text-5xl font-black tracking-tight text-white leading-tight">
                Struktur Kepengurusan & Fungsionaris BEM
            </h1>
            <p class="text-sm sm:text-base text-slate-300 leading-relaxed max-w-2xl">
                Jajaran pimpinan eksekutif, Badan Pengurus Harian (BPH), menteri koordinator, hingga staf departemen yang berdedikasi mengadvokasikan kepentingan mahasiswa perguruan tinggi.
            </p>
        </div>

        <!-- Quick Jump Navigation Tabs -->
        <div class="mt-8 flex flex-wrap items-center gap-2 pt-4 border-t border-slate-800 text-xs">
            <a href="#presma-wapresma" class="px-3.5 py-1.5 rounded-xl bg-slate-800/80 hover:bg-slate-700 text-amber-400 font-bold transition-colors">
                Presma & Wapresma
            </a>
            <a href="#bph" class="px-3.5 py-1.5 rounded-xl bg-slate-800/80 hover:bg-slate-700 text-slate-300 hover:text-white font-medium transition-colors">
                BPH Utama
            </a>
            <a href="#menteri" class="px-3.5 py-1.5 rounded-xl bg-slate-800/80 hover:bg-slate-700 text-slate-300 hover:text-white font-medium transition-colors">
                Menteri & Biro
            </a>
            @if($staffByDept->isNotEmpty())
                <a href="#staf" class="px-3.5 py-1.5 rounded-xl bg-slate-800/80 hover:bg-slate-700 text-slate-300 hover:text-white font-medium transition-colors">
                    Staf Departemen
                </a>
            @endif
            <div class="ml-auto">
                <a href="{{ route('organizations.index') }}" class="inline-flex items-center gap-1.5 text-slate-400 hover:text-amber-400 transition-colors font-medium">
                    <span>Direktori Lembaga Ormawa Kampus</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                    </svg>
                </a>
            </div>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-16">

    @if($totalMembers === 0)
        <!-- Empty State Info -->
        <div class="bg-white rounded-3xl border border-slate-200/80 p-8 sm:p-12 text-center max-w-2xl mx-auto shadow-sm space-y-4">
            <div class="w-16 h-16 rounded-2xl bg-amber-50 text-amber-600 mx-auto flex items-center justify-center">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </div>
            <h3 class="text-xl font-bold text-slate-900">Data Kepengurusan Sedang Dalam Penyusunan</h3>
            <p class="text-xs sm:text-sm text-slate-500 leading-relaxed">
                Struktur kabinet fungsionaris definitif untuk periode {{ $activePeriod }} sedang dipersiapkan oleh sekretariat BEM. Silakan kembali beberapa saat lagi atau hubungi narahubung resmi.
            </p>
            <div class="pt-2">
                <a href="{{ route('organizations.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow-sm transition-all">
                    <span>Lihat Struktur Lembaga Ormawa</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </a>
            </div>
        </div>
    @else

        <!-- TIER 1: PIMPINAN UTAMA (PRESMA & WAPRESMA) -->
        <section id="presma-wapresma" class="scroll-mt-24 space-y-6">
            <div class="text-center max-w-xl mx-auto space-y-2">
                <span class="text-xs font-black tracking-widest uppercase text-amber-600 block">Pimpinan Eksekutif Tertinggi</span>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900">Presiden & Wakil Presiden Mahasiswa</h2>
                <div class="w-16 h-1 bg-amber-500 mx-auto rounded-full"></div>
            </div>

            @if($presmaWapresma->isNotEmpty())
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-4xl mx-auto">
                    @foreach($presmaWapresma as $lead)
                        <div class="bg-white rounded-3xl border border-slate-200/90 shadow-md hover:shadow-xl transition-all duration-300 overflow-hidden flex flex-col group">
                            <!-- Foto Pimpinan -->
                            <div class="relative aspect-4/3 sm:aspect-square overflow-hidden bg-slate-100">
                                <img src="{{ $lead->photo_url }}"
                                     alt="{{ $lead->name }}"
                                     class="w-full h-full object-cover object-top transition-transform duration-500 group-hover:scale-105">
                                <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-transparent to-transparent"></div>
                                <div class="absolute bottom-4 left-4 right-4 text-white">
                                    <span class="inline-block px-3 py-1 rounded-full bg-amber-500 text-slate-950 font-black text-xs uppercase tracking-wider mb-1 shadow-sm">
                                        {{ $lead->position }}
                                    </span>
                                    <h3 class="text-xl sm:text-2xl font-black text-white leading-tight drop-shadow-sm">
                                        {{ $lead->name }}
                                    </h3>
                                </div>
                            </div>

                            <!-- Detail Deskripsi & Periode -->
                            <div class="p-6 flex-1 flex flex-col justify-between space-y-4">
                                @if($lead->description)
                                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed italic">
                                        "{{ $lead->description }}"
                                    </p>
                                @endif

                                <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                                    <span class="font-semibold text-slate-700">Periode Amanah</span>
                                    <span class="font-mono font-bold text-blue-900 bg-blue-50 px-2.5 py-0.5 rounded-lg border border-blue-200">
                                        {{ $lead->period ?? $activePeriod }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-center text-xs text-slate-400">Data Presma & Wapresma belum dipublikasikan.</p>
            @endif
        </section>

        <!-- TIER 2: BADAN PENGURUS HARIAN (BPH) -->
        <section id="bph" class="scroll-mt-24 space-y-6 pt-6">
            <div class="text-center max-w-xl mx-auto space-y-2">
                <span class="text-xs font-black tracking-widest uppercase text-blue-900 block">Tata Kelola & Kesekretariatan</span>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900">Badan Pengurus Harian (BPH)</h2>
                <p class="text-xs sm:text-sm text-slate-500">Mengelola administrasi, kesekretariatan jenderal, dan tata kelola perbendaharaan keuangan organisasi.</p>
                <div class="w-12 h-1 bg-blue-900 mx-auto rounded-full"></div>
            </div>

            @if($bph->isNotEmpty())
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 max-w-6xl mx-auto">
                    @foreach($bph as $officer)
                        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm hover:shadow-md transition-all duration-200 overflow-hidden flex flex-col group">
                            <div class="aspect-square bg-slate-100 overflow-hidden relative">
                                <img src="{{ $officer->photo_url }}"
                                     alt="{{ $officer->name }}"
                                     class="w-full h-full object-cover object-top transition-transform duration-300 group-hover:scale-105">
                                <span class="absolute top-3 left-3 px-2.5 py-1 rounded-lg bg-blue-900/90 text-white font-bold text-[11px] shadow-sm">
                                    {{ $officer->position }}
                                </span>
                            </div>

                            <div class="p-5 flex-1 flex flex-col justify-between space-y-3">
                                <div>
                                    <h4 class="font-bold text-base text-slate-900 group-hover:text-blue-900 transition-colors">
                                        {{ $officer->name }}
                                    </h4>
                                    @if($officer->department)
                                        <span class="text-xs text-slate-500 block mt-0.5">{{ $officer->department }}</span>
                                    @endif
                                </div>

                                @if($officer->description)
                                    <p class="text-xs text-slate-600 line-clamp-2 leading-relaxed">
                                        {{ $officer->description }}
                                    </p>
                                @endif

                                <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-400">
                                    <span>Tingkat BPH</span>
                                    <span class="font-mono text-slate-600">{{ $officer->period ?? $activePeriod }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-center text-xs text-slate-400">Data BPH belum dipublikasikan.</p>
            @endif
        </section>

        <!-- TIER 3: MENTERI & KEPALA BIRO (PER DEPARTEMEN) -->
        <section id="menteri" class="scroll-mt-24 space-y-8 pt-6">
            <div class="text-center max-w-xl mx-auto space-y-2">
                <span class="text-xs font-black tracking-widest uppercase text-purple-700 block">Jajaran Bidang Kerja</span>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900">Menteri & Kepala Biro</h2>
                <p class="text-xs sm:text-sm text-slate-500">Para pemimpin kementerian strategis yang mengeksekusi program kerja dan pengabdian.</p>
                <div class="w-12 h-1 bg-purple-700 mx-auto rounded-full"></div>
            </div>

            @if($ministersByDept->isNotEmpty())
                <div class="space-y-10 max-w-6xl mx-auto">
                    @foreach($ministersByDept as $deptName => $deptMinisters)
                        <div class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-8 shadow-sm space-y-6">
                            <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
                                <div class="w-3 h-3 rounded-full bg-purple-600"></div>
                                <h3 class="text-lg font-black text-slate-900 tracking-tight">{{ $deptName }}</h3>
                                <span class="ml-auto text-xs font-bold text-purple-800 bg-purple-50 px-2.5 py-0.5 rounded-full border border-purple-200">
                                    {{ $deptMinisters->count() }} Pejabat
                                </span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                                @foreach($deptMinisters as $minister)
                                    <div class="p-4 rounded-2xl border border-slate-200/70 hover:border-purple-300 transition-all flex items-center gap-4 bg-slate-50/40 hover:bg-white group">
                                        <div class="w-16 h-16 rounded-2xl overflow-hidden bg-slate-200 shrink-0 border border-slate-200">
                                            <img src="{{ $minister->photo_url }}"
                                                 alt="{{ $minister->name }}"
                                                 class="w-full h-full object-cover">
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <span class="text-[11px] font-bold text-purple-700 uppercase tracking-wider block truncate">
                                                {{ $minister->position }}
                                            </span>
                                            <h4 class="font-bold text-sm text-slate-900 truncate group-hover:text-purple-900">
                                                {{ $minister->name }}
                                            </h4>
                                            @if($minister->description)
                                                <p class="text-[11px] text-slate-500 line-clamp-1 mt-0.5">{{ $minister->description }}</p>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-center text-xs text-slate-400">Data Menteri & Kepala Biro belum dipublikasikan.</p>
            @endif
        </section>

        <!-- TIER 4: STAF & ANGGOTA KABINET -->
        @if($staffByDept->isNotEmpty())
            <section id="staf" class="scroll-mt-24 space-y-6 pt-6">
                <div class="text-center max-w-xl mx-auto space-y-2">
                    <span class="text-xs font-black tracking-widest uppercase text-slate-600 block">Kekuatan Penggerak</span>
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900">Staf & Anggota Kabinet</h2>
                    <p class="text-xs sm:text-sm text-slate-500">Para fungsionaris muda yang menjadi garda terdepan operasional program kerja.</p>
                    <div class="w-12 h-1 bg-slate-600 mx-auto rounded-full"></div>
                </div>

                <div class="space-y-8 max-w-6xl mx-auto">
                    @foreach($staffByDept as $deptName => $deptStaff)
                        <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-sm space-y-4">
                            <h4 class="text-sm font-bold text-slate-800 uppercase tracking-wider border-b border-slate-100 pb-2">
                                Staf {{ $deptName }}
                            </h4>

                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                                @foreach($deptStaff as $staffMember)
                                    <div class="p-3 rounded-2xl border border-slate-200/60 text-center space-y-2 hover:bg-slate-50 transition-all">
                                        <div class="w-12 h-12 rounded-xl overflow-hidden bg-slate-200 mx-auto border border-slate-200">
                                            <img src="{{ $staffMember->photo_url }}" alt="{{ $staffMember->name }}" class="w-full h-full object-cover">
                                        </div>
                                        <div>
                                            <span class="font-bold text-xs text-slate-900 block truncate">{{ $staffMember->name }}</span>
                                            <span class="text-[10px] text-slate-500 block truncate">{{ $staffMember->position }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

    @endif

    <!-- Banner Ajak Sinergi & Aspirasi -->
    <div class="rounded-3xl p-8 sm:p-12 text-white shadow-xl flex flex-col sm:flex-row sm:items-center justify-between gap-6 border"
         style="background: linear-gradient(135deg, var(--theme-secondary) 0%, var(--theme-primary) 100%); border-color: var(--theme-secondary);">
        <div class="space-y-2 max-w-xl">
            <h3 class="text-2xl font-black tracking-tight">Sampaikan Ide & Masukan ke Kabinet BEM</h3>
            <p class="text-xs sm:text-sm text-slate-300 leading-relaxed">
                Punya pertanyaan untuk menteri terkait atau keluhan yang perlu kami perjuangkan? Salurkan melalui sistem kanal aspirasi mahasiswa resmi.
            </p>
        </div>
        <div class="flex items-center gap-3 shrink-0">
            <a href="{{ route('aspirations.create') }}"
               class="px-5 py-3 rounded-xl text-slate-950 font-extrabold text-xs sm:text-sm shadow-md transition-all hover:opacity-90"
               style="background-color: var(--theme-accent); color: #020617;">
                Kirim Aspirasi Sekarang
            </a>
        </div>
    </div>
</div>
@endsection
