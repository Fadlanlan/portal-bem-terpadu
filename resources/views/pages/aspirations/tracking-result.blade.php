@extends('layouts.app')

@section('title', 'Status Tiket ' . $aspiration->ticket_number)
@section('meta_description', 'Rincian perkembangan status dan riwayat tindak lanjut tiket aspirasi mahasiswa ' . $aspiration->ticket_number)

@section('content')
    <div class="text-white py-10 border-b" style="background-color: var(--theme-primary); border-bottom-color: var(--theme-secondary);">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-breadcrumb :items="['Kanal Aspirasi' => route('aspirations.info'), 'Pelacakan' => route('aspirations.tracking.form'), $aspiration->ticket_number => null]" />

            <div class="mt-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <span class="text-xs font-mono font-bold uppercase tracking-wider" style="color: var(--theme-accent);">Hasil Pelacakan Resmi</span>
                    <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight mt-0.5">
                        Tiket {{ $aspiration->ticket_number }}
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-400 mt-1">
                        Dikirim pada {{ $aspiration->submitted_at?->translatedFormat('l, d F Y - H:i') ?? $aspiration->created_at->format('d/m/Y H:i') }} WIB
                    </p>
                </div>

                <div>
                    <x-badge :type="$aspiration->status_badge_type" class="text-sm px-3.5 py-1">
                        Status: {{ $aspiration->status_label }}
                    </x-badge>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8">
        <!-- Rincian Aspirasi Card -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3 text-xs sm:text-sm">
                <span class="text-slate-500">Kategori:</span>
                <span class="font-bold text-slate-800">{{ $aspiration->category_label }}</span>
            </div>

            <div>
                <span class="text-xs text-slate-400 block mb-1 font-semibold uppercase tracking-wider">Subjek Laporan:</span>
                <h2 class="text-lg sm:text-xl font-bold text-slate-900">
                    {{ $aspiration->subject }}
                </h2>
            </div>

            <div>
                <span class="text-xs text-slate-400 block mb-1 font-semibold uppercase tracking-wider">Isi Laporan:</span>
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 text-sm sm:text-base text-slate-700 leading-relaxed whitespace-pre-line">
                    {{ $aspiration->message }}
                </div>
            </div>
        </div>

        <!-- Tanggapan Resmi Pengelola (Bila Tersedia) -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-4">
            <div class="flex items-center gap-2.5 border-b border-slate-100 pb-3">
                <div class="w-6 h-6 rounded-md bg-amber-500/20 text-amber-700 flex items-center justify-center font-bold text-xs">
                    &bull;
                </div>
                <h2 class="text-base sm:text-lg font-bold text-slate-900">
                    Tanggapan Resmi Tim Advokasi BEM
                </h2>
            </div>

            @if($aspiration->admin_response)
                <div class="p-5 rounded-xl bg-amber-50/60 border border-amber-200 text-sm sm:text-base text-amber-950 leading-relaxed whitespace-pre-line">
                    {{ $aspiration->admin_response }}
                </div>
                @if($aspiration->resolved_at)
                    <p class="text-xs text-slate-400">
                        Diselesaikan pada: {{ $aspiration->resolved_at->translatedFormat('d F Y H:i') }} WIB
                    </p>
                @endif
            @else
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 text-xs sm:text-sm text-slate-500 flex items-center gap-3">
                    <svg class="w-5 h-5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Belum ada tanggapan resmi khusus. Laporan Anda sedang dalam proses penelaahan oleh tim advokasi.</span>
                </div>
            @endif
        </div>

        <!-- Riwayat Perkembangan Status (Audit Trail) -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-6">
            <h2 class="text-base sm:text-lg font-bold text-slate-900 border-b border-slate-100 pb-3">
                Riwayat Jejak Penanganan Laporan
            </h2>

            @if($aspiration->statusHistories->isNotEmpty())
                <div class="relative pl-6 space-y-6 border-l-2 border-slate-200 ml-2">
                    @foreach($aspiration->statusHistories as $history)
                        <div class="relative">
                            <!-- Bullet Marker -->
                            <div class="absolute -left-[31px] top-1 w-4 h-4 rounded-full bg-slate-900 border-2 border-white shadow"></div>

                            <div class="space-y-1">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="font-bold text-sm text-slate-900">
                                        {{ $history->new_status_label }}
                                    </span>
                                    @if($history->old_status)
                                        <span class="text-xs text-slate-400">
                                            (sebelumnya: {{ $history->old_status_label }})
                                        </span>
                                    @endif
                                    <span class="text-xs text-slate-400 ml-auto">
                                        {{ $history->created_at->translatedFormat('d M Y, H:i') }} WIB
                                    </span>
                                </div>

                                @if($history->note)
                                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed pt-0.5">
                                        {{ $history->note }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <x-empty-state
                    title="Belum Ada Riwayat Status"
                    description="Aspirasi ini baru didaftarkan dan belum memiliki riwayat pembaruan status."
                />
            @endif
        </div>

        <!-- Privacy Assurance Notice -->
        <div class="p-4 rounded-xl bg-slate-100 border border-slate-200 text-xs text-slate-500 leading-relaxed">
            <strong class="text-slate-700 block font-semibold mb-0.5">Jaminan Perlindungan Data Pelapor:</strong>
            Untuk menjaga keamanan dan asas kerahasiaan pelapor, data nama lengkap dan alamat email tidak pernah dipublikasikan pada halaman hasil pelacakan ini.
        </div>

        <!-- Back Actions -->
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-2">
            <a href="{{ route('aspirations.tracking.form') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs sm:text-sm transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                <span>Lacak Tiket Lainnya</span>
            </a>

            <a href="{{ route('home') }}"
               class="text-xs sm:text-sm font-semibold text-amber-700 hover:text-amber-800">
                Kembali ke Beranda Utama &rarr;
            </a>
        </div>
    </div>
@endsection
