@extends('layouts.app')

@section('title', $program['name'])
@section('meta_description', $program['description'])
@section('og_title', $program['name'])
@section('og_description', $program['description'])
@section('og_type', 'article')

@section('content')
    <div class="text-white py-10 border-b" style="background-color: var(--theme-primary); border-bottom-color: var(--theme-secondary);">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-breadcrumb :items="['Program Kerja' => route('programs.index'), $program['name'] => null]" />

            <div class="mt-4 space-y-4">
                <div class="flex items-center gap-3 flex-wrap">
                    <span class="px-2.5 py-1 rounded-md text-xs font-semibold bg-blue-900/60 text-blue-200 border border-blue-700/50">
                        {{ $program['department'] }}
                    </span>
                    <x-badge :type="$program['status']">{{ $program['status'] }}</x-badge>
                    @if(!($program instanceof \App\Models\WorkProgram))
                        <span class="text-xs text-amber-400/80 italic">&bull; Data Demonstrasi</span>
                    @endif
                </div>

                <h1 class="text-2xl sm:text-4xl font-extrabold tracking-tight text-white leading-tight">
                    {{ $program['name'] }}
                </h1>

                <div class="text-xs sm:text-sm text-slate-300">
                    Penyelenggara: <strong class="text-white font-semibold">{{ $program['organization'] }}</strong> &bull; Periode: {{ $program['period'] }}
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <!-- Main Details -->
            <div class="lg:col-span-8 space-y-6">
                <!-- Deskripsi Umum -->
                <div class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 shadow-sm space-y-4">
                    <h2 class="text-lg font-bold text-slate-900 border-b border-slate-100 pb-3">Deskripsi Program</h2>
                    <p class="text-sm sm:text-base text-slate-700 leading-relaxed">
                        {{ $program['description'] }}
                    </p>

                    <h2 class="text-lg font-bold text-slate-900 border-b border-slate-100 pb-3 pt-3">Tujuan & Urgensi</h2>
                    <p class="text-sm sm:text-base text-slate-700 leading-relaxed">
                        {{ $program['objectives'] }}
                    </p>
                </div>

                <!-- Target & Indikator Capaian -->
                <div class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 shadow-sm space-y-4">
                    <h2 class="text-lg font-bold text-slate-900 border-b border-slate-100 pb-3">Sasaran Kegiatan</h2>
                    <ul class="space-y-3">
                        @foreach($program['targets'] as $target)
                            <li class="flex items-start gap-3 text-sm text-slate-700">
                                <span class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-800 flex items-center justify-center shrink-0 mt-0.5 text-xs font-bold">&check;</span>
                                <span>{{ $target }}</span>
                            </li>
                        @endforeach
                    </ul>

                    <div class="mt-6 p-4 rounded-xl bg-amber-50/70 border border-amber-200">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-amber-800 mb-1">Indikator Kinerja Utama (IKU)</h3>
                        <p class="text-xs sm:text-sm text-amber-900 leading-relaxed">
                            {{ $program['indicators'] }}
                        </p>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-2">
                    <a href="{{ route('programs.index') }}"
                       class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs sm:text-sm transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        <span>Kembali ke Daftar Program</span>
                    </a>
                </div>
            </div>

            <!-- Sidebar Info -->
            <div class="lg:col-span-4 space-y-6">
                <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm space-y-4">
                    <h2 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3">Informasi Pelaksanaan</h2>

                    <div class="space-y-3 text-xs sm:text-sm">
                        <div>
                            <span class="text-slate-400 block text-xs">Penanggung Jawab (PIC):</span>
                            <span class="font-semibold text-slate-800">{{ $program['pic'] }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-xs">Jadwal / Timeline:</span>
                            <span class="font-semibold text-slate-800">{{ $program['timeline'] }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-xs">Periode:</span>
                            <span class="font-semibold text-slate-800">{{ $program['period'] }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-xs">Status Terkini:</span>
                            <div class="mt-1">
                                <x-badge :type="$program['status']">{{ $program['status'] }}</x-badge>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-slate-900 text-white rounded-2xl p-6 shadow-sm space-y-3">
                    <h2 class="text-sm font-bold text-amber-400">Punya Saran untuk Program Ini?</h2>
                    <p class="text-xs text-slate-300 leading-relaxed">
                        Anda dapat memberikan masukan atau usulan kolaborasi kegiatan melalui kanal aspirasi terpadu.
                    </p>
                    <a href="{{ route('aspirations.info') }}"
                       class="w-full inline-flex items-center justify-center gap-1.5 py-2 px-3 rounded-lg bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs transition-colors">
                        Sampaikan Aspirasi
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
