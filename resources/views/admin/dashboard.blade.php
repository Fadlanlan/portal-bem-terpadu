@extends('layouts.admin')

@section('title', 'Dashboard Utama')
@section('header_title', 'Ringkasan & Statistik Sistem')

@section('content')
<div class="space-y-8">
    <!-- Welcome Greeting Banner -->
    <div class="bg-gradient-to-r from-slate-900 via-blue-950 to-slate-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl border border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
        <div class="space-y-2">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-500/20 text-amber-300 text-xs font-bold border border-amber-500/30">
                <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                <span>Sesi Aktif: {{ $user->role_label }}</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">
                Selamat Datang, {{ $user->name }}!
            </h1>
            <p class="text-xs sm:text-sm text-slate-300 max-w-2xl leading-relaxed">
                Kelola data aspirasi kemahasiswaan, rilis berita resmi, publikasi agenda kegiatan, serta informasi organisasi kampus melalui panel terintegrasi.
            </p>
        </div>

        <div class="flex items-center gap-3 shrink-0">
            @if($user->canAccessModule('aspirations'))
                <a href="{{ route('admin.aspirations.index') }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs sm:text-sm shadow-md transition-all">
                    <span>Lihat Aspirasi Masuk</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </a>
            @endif
        </div>
    </div>

    <!-- ==================== STATISTIK KARTU ==================== -->
    <div>
        <h3 class="text-sm font-bold uppercase tracking-wider text-slate-500 mb-4">
            {{ $user->isAdvocacy() ? 'Statistik Penanganan Aspirasi Mahasiswa' : 'Statistik Operasional Portal' }}
        </h3>

        <!-- Grid 1: Aspirasi Breakdown -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
            <!-- Total Aspirasi -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm space-y-1">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wide block">Total Aspirasi</span>
                <span class="text-2xl sm:text-3xl font-extrabold text-slate-900 block font-mono">{{ $stats['total_aspirations'] }}</span>
                <span class="text-[11px] text-slate-400 block">Seluruh laporan masuk</span>
            </div>

            <!-- Baru (Submitted) -->
            <div class="bg-white p-5 rounded-2xl border border-blue-200 shadow-sm space-y-1 bg-gradient-to-br from-white to-blue-50/50">
                <span class="text-xs font-bold text-blue-700 uppercase tracking-wide block">Baru Masuk</span>
                <span class="text-2xl sm:text-3xl font-extrabold text-blue-800 block font-mono">{{ $stats['aspirations_new'] }}</span>
                <span class="text-[11px] text-blue-600 block">Menunggu verifikasi</span>
            </div>

            <!-- Sedang Ditinjau -->
            <div class="bg-white p-5 rounded-2xl border border-indigo-200 shadow-sm space-y-1 bg-gradient-to-br from-white to-indigo-50/50">
                <span class="text-xs font-bold text-indigo-700 uppercase tracking-wide block">Ditinjau</span>
                <span class="text-2xl sm:text-3xl font-extrabold text-indigo-800 block font-mono">{{ $stats['aspirations_under_review'] }}</span>
                <span class="text-[11px] text-indigo-600 block">Kajian tim advokasi</span>
            </div>

            <!-- Sedang Diproses -->
            <div class="bg-white p-5 rounded-2xl border border-amber-200 shadow-sm space-y-1 bg-gradient-to-br from-white to-amber-50/50">
                <span class="text-xs font-bold text-amber-700 uppercase tracking-wide block">Ditindaklanjuti</span>
                <span class="text-2xl sm:text-3xl font-extrabold text-amber-800 block font-mono">{{ $stats['aspirations_in_progress'] }}</span>
                <span class="text-[11px] text-amber-600 block">Proses disposisi/aksi</span>
            </div>

            <!-- Selesai (Resolved) -->
            <div class="bg-white p-5 rounded-2xl border border-emerald-200 shadow-sm space-y-1 bg-gradient-to-br from-white to-emerald-50/50">
                <span class="text-xs font-bold text-emerald-700 uppercase tracking-wide block">Selesai</span>
                <span class="text-2xl sm:text-3xl font-extrabold text-emerald-800 block font-mono">{{ $stats['aspirations_resolved'] }}</span>
                <span class="text-[11px] text-emerald-600 block">Tuntas & direspons</span>
            </div>

            <!-- Ditolak (Rejected) -->
            <div class="bg-white p-5 rounded-2xl border border-rose-200 shadow-sm space-y-1 bg-gradient-to-br from-white to-rose-50/50">
                <span class="text-xs font-bold text-rose-700 uppercase tracking-wide block">Ditolak</span>
                <span class="text-2xl sm:text-3xl font-extrabold text-rose-800 block font-mono">{{ $stats['aspirations_rejected'] }}</span>
                <span class="text-[11px] text-rose-600 block">Tidak memenuhi syarat</span>
            </div>
        </div>

        <!-- Grid 2: Publikasi Konten (Hanya tampil jika user punya akses konten) -->
        @if(! $user->isAdvocacy())
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-4">
                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider block">Warta & Berita</span>
                        <span class="text-2xl font-extrabold text-slate-900 font-mono mt-0.5 block">{{ $stats['total_articles'] }}</span>
                        <span class="text-xs text-slate-400">Total artikel terbit & draft</span>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-800 flex items-center justify-center font-bold">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
                    </div>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider block">Program Kerja</span>
                        <span class="text-2xl font-extrabold text-slate-900 font-mono mt-0.5 block">{{ $stats['total_programs'] }}</span>
                        <span class="text-xs text-slate-400">Rencana & kegiatan kementerian</span>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-800 flex items-center justify-center font-bold">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                    </div>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-wider block">Agenda Mendatang</span>
                        <span class="text-2xl font-extrabold text-slate-900 font-mono mt-0.5 block">{{ $stats['total_upcoming_events'] }}</span>
                        <span class="text-xs text-slate-400">Kegiatan aktif terjadwal</span>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-800 flex items-center justify-center font-bold">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 7V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- ==================== AKTIVITAS TERKINI ==================== -->
    <div class="grid grid-cols-1 {{ $user->isAdvocacy() ? '' : 'lg:grid-cols-2' }} gap-8">
        <!-- Aktivitas 1: Aspirasi Terbaru Masuk -->
        @if($user->canAccessModule('aspirations'))
            <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-amber-500/20 text-amber-700 flex items-center justify-center font-bold text-xs">
                            &bull;
                        </div>
                        <h2 class="text-base font-bold text-slate-900">Aspirasi Terbaru Masuk</h2>
                    </div>
                    <a href="{{ route('admin.aspirations.index') }}" class="text-xs font-semibold text-amber-700 hover:text-amber-800">
                        Lihat Semua &rarr;
                    </a>
                </div>

                @if($recentAspirations->isNotEmpty())
                    <div class="divide-y divide-slate-100">
                        @foreach($recentAspirations as $asp)
                            <div class="py-3 flex items-start justify-between gap-4">
                                <div class="space-y-1">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="font-mono text-xs font-bold text-slate-800">{{ $asp->ticket_number }}</span>
                                        <span class="text-slate-300">&bull;</span>
                                        <span class="text-xs text-slate-500">{{ $asp->category_label }}</span>
                                        <span class="text-slate-300">&bull;</span>
                                        <span class="text-[11px] text-slate-400">{{ $asp->submitted_at?->diffForHumans() ?? '-' }}</span>
                                    </div>
                                    <a href="{{ route('admin.aspirations.show', $asp) }}" class="text-sm font-semibold text-slate-900 hover:text-blue-900 block line-clamp-1">
                                        {{ $asp->subject }}
                                    </a>
                                </div>
                                <div class="shrink-0">
                                    <x-badge :type="$asp->status_badge_type" class="text-[11px] px-2.5 py-0.5">
                                        {{ $asp->status_label }}
                                    </x-badge>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-8 text-center text-xs text-slate-500">
                        Belum ada aspirasi mahasiswa yang tercatat dalam sistem.
                    </div>
                @endif
            </div>
        @endif

        <!-- Aktivitas 2: Berita & Agenda Terkini (Non-Advocacy) -->
        @if(! $user->isAdvocacy())
            <div class="space-y-6">
                <!-- Berita Terbaru -->
                <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <h2 class="text-base font-bold text-slate-900">Warta Berita Terbaru</h2>
                        <a href="{{ route('admin.articles.index') }}" class="text-xs font-semibold text-amber-700 hover:text-amber-800">
                            Kelola Berita &rarr;
                        </a>
                    </div>

                    @if($recentArticles->isNotEmpty())
                        <div class="divide-y divide-slate-100">
                            @foreach($recentArticles as $art)
                                <div class="py-3 flex items-center justify-between gap-3">
                                    <div class="truncate">
                                        <a href="{{ route('admin.articles.edit', $art) }}" class="text-sm font-semibold text-slate-900 hover:text-blue-900 truncate block">
                                            {{ $art->title }}
                                        </a>
                                        <span class="text-xs text-slate-400">{{ $art->category }} &bull; {{ $art->created_at->format('d/m/Y') }}</span>
                                    </div>
                                    <span class="shrink-0 text-[10px] font-bold px-2 py-0.5 rounded {{ $art->status === 'published' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
                                        {{ strtoupper($art->status) }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="p-6 text-center text-xs text-slate-500">
                            Belum ada artikel warta berita yang dibuat.
                        </div>
                    @endif
                </div>

                <!-- Agenda Mendatang -->
                <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <h2 class="text-base font-bold text-slate-900">Agenda Kampus Mendatang</h2>
                        <a href="{{ route('admin.events.index') }}" class="text-xs font-semibold text-amber-700 hover:text-amber-800">
                            Kelola Agenda &rarr;
                        </a>
                    </div>

                    @if($upcomingEvents->isNotEmpty())
                        <div class="divide-y divide-slate-100">
                            @foreach($upcomingEvents as $ev)
                                <div class="py-3 flex items-center justify-between gap-3">
                                    <div class="truncate">
                                        <a href="{{ route('admin.events.edit', $ev) }}" class="text-sm font-semibold text-slate-900 hover:text-blue-900 truncate block">
                                            {{ $ev->title }}
                                        </a>
                                        <span class="text-xs text-slate-400">{{ $ev->date_display }} &bull; {{ $ev->location }}</span>
                                    </div>
                                    <span class="shrink-0 text-[10px] font-bold px-2 py-0.5 rounded bg-blue-100 text-blue-800">
                                        {{ $ev->organizer }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="p-6 text-center text-xs text-slate-500">
                            Tidak ada agenda kegiatan mendatang yang terjadwal.
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
