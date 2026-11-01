@extends('layouts.app')

@section('title', 'Agenda Kegiatan')
@section('meta_description', 'Kalender agenda dan jadwal kegiatan kemahasiswaan, musyawarah ormawa, dan festival kampus.')

@section('content')
    <div class="text-white py-12 border-b" style="background-color: var(--theme-primary); border-bottom-color: var(--theme-secondary);">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-breadcrumb :items="['Agenda Kegiatan' => null]" />
            <div class="mt-4 max-w-3xl">
                <span class="text-xs font-bold uppercase tracking-wider" style="color: var(--theme-accent);">Kalender Bersama Ormawa</span>
                <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight mt-1">Agenda & Jadwal Kegiatan</h1>
                <p class="text-sm sm:text-base text-slate-300 mt-2">
                    Satu kalender terintegrasi untuk seluruh jadwal forum diskusi, sidang kemahasiswaan, pentas budaya, dan kompetisi kampus. Waktu mengacu pada zona WIB (Asia/Jakarta).
                </p>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-12">
        <!-- Section 1: Agenda Mendatang -->
        <div>
            <div class="flex items-center gap-3 mb-6">
                <span class="w-3 h-3 rounded-full bg-emerald-500 animate-pulse"></span>
                <h2 class="text-xl sm:text-2xl font-bold text-slate-900">Agenda Mendatang</h2>
            </div>

            @if(count($upcomingEvents) > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($upcomingEvents as $event)
                        <div class="bg-white rounded-2xl border border-slate-200 p-6 flex flex-col justify-between hover:shadow-md hover:border-slate-300 transition-all group">
                            <div class="space-y-4">
                                <div class="flex items-center justify-between text-xs gap-2">
                                    <span class="px-2.5 py-1 rounded bg-blue-50 text-blue-800 font-semibold border border-blue-200 truncate">
                                        {{ $event['organizer'] }}
                                    </span>
                                    @if($event['is_registration_open'])
                                        <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 shrink-0">
                                            Pendaftaran Dibuka
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-slate-100 text-slate-600 border border-slate-200 shrink-0">
                                            Khusus Delegasi
                                        </span>
                                    @endif
                                </div>

                                <h3 class="font-bold text-slate-900 text-base sm:text-lg group-hover:text-blue-900 transition-colors">
                                    <a href="{{ route('events.show', $event['slug']) }}">
                                        {{ $event['title'] }}
                                    </a>
                                </h3>

                                <div class="space-y-2 text-xs sm:text-sm text-slate-600 bg-slate-50 p-3 rounded-xl border border-slate-100">
                                    <p class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        <span class="font-semibold text-slate-800">{{ $event['date_display'] }}</span>
                                    </p>
                                    <p class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        <span>{{ $event['time_display'] }}</span>
                                    </p>
                                    <p class="flex items-start gap-2">
                                        <svg class="w-4 h-4 text-slate-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                        <span>{{ $event['location'] }}</span>
                                    </p>
                                </div>
                            </div>

                            <div class="pt-4 mt-4 border-t border-slate-100 flex items-center justify-between">
                                <a href="{{ route('events.show', $event['slug']) }}" class="text-xs font-semibold text-amber-700 hover:text-amber-800 flex items-center gap-1">
                                    <span>Lihat Rincian Rundown</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                </a>
                                @if(!($event instanceof \App\Models\Event))
                                    <span class="text-[11px] text-slate-400 italic">Data Demonstrasi</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <x-empty-state
                    title="Belum Ada Agenda Mendatang"
                    description="Seluruh jadwal kegiatan terdekat sedang dalam tahap sinkronisasi dengan kalender ormawa."
                />
            @endif
        </div>

        <!-- Section 2: Riwayat Agenda Terlaksana -->
        <div class="pt-8 border-t border-slate-200">
            <h2 class="text-xl sm:text-2xl font-bold text-slate-800 mb-6">Riwayat Agenda yang Telah Terlaksana</h2>

            @if(count($pastEvents) > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($pastEvents as $past)
                    <div class="bg-slate-50 rounded-xl border border-slate-200 p-5 flex flex-col justify-between opacity-80 hover:opacity-100 transition-opacity">
                        <div class="space-y-2">
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-medium text-slate-500">{{ $past['organizer'] }}</span>
                                <span class="px-2 py-0.5 rounded text-[11px] bg-slate-200 text-slate-700 font-semibold">Telah Selesai</span>
                            </div>
                            <h3 class="font-bold text-slate-800 text-base">
                                <a href="{{ route('events.show', $past['slug']) }}" class="hover:text-blue-900">
                                    {{ $past['title'] }}
                                </a>
                            </h3>
                            <p class="text-xs text-slate-500">
                                Dilaksanakan pada: {{ $past['date_display'] }} di {{ $past['location'] }}
                            </p>
                        </div>
                        <div class="pt-3 mt-3 border-t border-slate-200/80">
                            <a href="{{ route('events.show', $past['slug']) }}" class="text-xs font-semibold text-slate-700 hover:text-slate-900">
                                Lihat Arsip Kegiatan &rarr;
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
            @else
                <x-empty-state
                    title="Belum Ada Riwayat Agenda"
                    description="Belum ada kegiatan terdahulu yang tercatat dalam arsip kalender kemahasiswaan."
                />
            @endif
        </div>
    </div>
@endsection
