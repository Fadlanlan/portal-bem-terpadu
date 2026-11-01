@extends('layouts.app')

@section('title', $event['title'])
@section('meta_description', $event['description'])
@section('og_title', $event['title'])
@section('og_description', $event['description'])
@section('og_type', 'article')

@section('content')
    <div class="text-white py-10 border-b" style="background-color: var(--theme-primary); border-bottom-color: var(--theme-secondary);">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-breadcrumb :items="['Agenda Kegiatan' => route('events.index'), $event['title'] => null]" />

            <div class="mt-4 space-y-4">
                <div class="flex items-center gap-3 flex-wrap">
                    <span class="px-2.5 py-1 rounded-md text-xs font-semibold bg-amber-500/20 text-amber-300 border border-amber-500/30">
                        {{ $event['category'] }}
                    </span>
                    @if($event['is_upcoming'])
                        <span class="px-2.5 py-1 rounded-md text-xs font-semibold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                            Agenda Mendatang
                        </span>
                    @else
                        <span class="px-2.5 py-1 rounded-md text-xs font-semibold bg-slate-700 text-slate-300 border border-slate-600">
                            Arsip Terlaksana
                        </span>
                    @endif
                    @if(!($event instanceof \App\Models\Event))
                        <span class="text-xs text-amber-400/80 italic">&bull; Data Demonstrasi</span>
                    @endif
                </div>

                <h1 class="text-2xl sm:text-4xl font-extrabold tracking-tight text-white leading-tight">
                    {{ $event['title'] }}
                </h1>

                <div class="text-xs sm:text-sm text-slate-300">
                    Penyelenggara Resmi: <strong class="text-white font-semibold">{{ $event['organizer'] }}</strong>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <!-- Event Main Description & Rundown -->
            <div class="lg:col-span-8 space-y-6">
                <!-- Deskripsi Kegiatan -->
                <div class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 shadow-sm space-y-4">
                    <h2 class="text-lg font-bold text-slate-900 border-b border-slate-100 pb-3">Tentang Kegiatan</h2>
                    <div class="text-sm sm:text-base text-slate-700 leading-relaxed space-y-3">
                        @foreach(explode("\n\n", $event['description']) as $p)
                            <p>{{ $p }}</p>
                        @endforeach
                    </div>
                </div>

                <!-- Poin Agenda / Rundown -->
                @if(isset($event['agenda_points']) && count($event['agenda_points']) > 0)
                    <div class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 shadow-sm space-y-4">
                        <h2 class="text-lg font-bold text-slate-900 border-b border-slate-100 pb-3">Pokok Pembahasan & Rundown</h2>
                        <ol class="space-y-3">
                            @foreach($event['agenda_points'] as $index => $point)
                                <li class="flex items-start gap-3 text-sm text-slate-700">
                                    <span class="w-6 h-6 rounded-full bg-slate-900 text-white flex items-center justify-center shrink-0 text-xs font-bold font-mono">
                                        {{ $index + 1 }}
                                    </span>
                                    <span class="pt-0.5">{{ $point }}</span>
                                </li>
                            @endforeach
                        </ol>
                    </div>
                @endif

                <!-- Pembicara / Narasumber -->
                @if(isset($event['speakers']) && count($event['speakers']) > 0)
                    <div class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 shadow-sm space-y-4">
                        <h2 class="text-lg font-bold text-slate-900 border-b border-slate-100 pb-3">Narasumber & Tokoh Terkait</h2>
                        <ul class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            @foreach($event['speakers'] as $speaker)
                                <li class="flex items-center gap-3 p-3 rounded-xl bg-slate-50 border border-slate-200 text-xs sm:text-sm text-slate-800 font-medium">
                                    <div class="w-7 h-7 rounded-full bg-blue-100 text-blue-900 flex items-center justify-center shrink-0 font-bold">
                                        &bull;
                                    </div>
                                    <span>{{ $speaker }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="flex items-center justify-between pt-2">
                    <a href="{{ route('events.index') }}"
                       class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs sm:text-sm transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        <span>Kembali ke Kalender Agenda</span>
                    </a>
                </div>
            </div>

            <!-- Sidebar Info Waktu & Tempat -->
            <div class="lg:col-span-4 space-y-6">
                <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm space-y-4">
                    <h2 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3">Informasi Pelaksanaan</h2>

                    <div class="space-y-4 text-xs sm:text-sm">
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-700 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                            <div>
                                <span class="text-slate-400 block text-xs">Hari / Tanggal:</span>
                                <span class="font-bold text-slate-900">{{ $event['date_display'] }}</span>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-700 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div>
                                <span class="text-slate-400 block text-xs">Waktu Kegiatan:</span>
                                <span class="font-bold text-slate-900">{{ $event['time_display'] }}</span>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            </div>
                            <div>
                                <span class="text-slate-400 block text-xs">Lokasi Ruang:</span>
                                <span class="font-bold text-slate-900">{{ $event['location'] }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Ketentuan Partisipasi / Pendaftaran -->
                    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-600 space-y-1">
                        <strong class="text-slate-800 font-semibold block">Catatan Kepesertaan:</strong>
                        <p>{{ $event['registration_note'] }}</p>
                    </div>
                </div>

                <!-- Banner Ormawa Info -->
                <div class="bg-slate-900 text-white rounded-2xl p-6 shadow-sm space-y-3">
                    <h2 class="text-sm font-bold text-amber-400">Kalender Ormawa Bersama</h2>
                    <p class="text-xs text-slate-300 leading-relaxed">
                        Masing-masing pimpinan ormawa dapat mengajukan kegiatan bersama ke dalam sistem kalender ini pada rilis panel admin mendatang.
                    </p>
                    <a href="{{ route('organizations.index') }}"
                       class="inline-block text-xs font-semibold text-amber-400 hover:text-amber-300">
                        Lihat Direktori Ormawa &rarr;
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
