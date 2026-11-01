@extends('layouts.app')

@section('title', 'Pertanyaan yang Sering Diajukan (FAQ)')
@section('meta_description', 'Pertanyaan umum seputar penggunaan Portal BEM Terpadu, alur penyampaian aspirasi, dan kalender kegiatan ormawa.')

@section('content')
    <div class="text-white py-12 border-b" style="background-color: var(--theme-primary); border-bottom-color: var(--theme-secondary);">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-breadcrumb :items="['FAQ' => null]" />
            <div class="mt-4">
                <span class="text-xs font-bold uppercase tracking-wider" style="color: var(--theme-accent);">Pusat Bantuan & Klarifikasi</span>
                <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight mt-1">Pertanyaan yang Sering Diajukan</h1>
                <p class="text-sm sm:text-base text-slate-300 mt-2">
                    Jawaban atas pertanyaan umum mengenai tata kelola portal, mekanisme pelacakan tiket aspirasi, dan akses publikasi kemahasiswaan.
                </p>
            </div>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-8">
        <!-- FAQ Accordion List -->
        <div class="space-y-4">
            @foreach($faqs as $faq)
                <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm transition-all hover:border-slate-300">
                    <button type="button"
                            data-faq-button
                            aria-expanded="false"
                            aria-controls="{{ $faq['id'] }}"
                            class="w-full p-5 sm:p-6 text-left flex items-center justify-between gap-4 focus:outline-none focus:bg-slate-50 transition-colors">
                        <span class="text-base sm:text-lg font-bold text-slate-900 leading-snug">
                            {{ $faq['question'] }}
                        </span>
                        <div data-faq-icon class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 shrink-0 transition-transform duration-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </button>
                    <div id="{{ $faq['id'] }}" class="hidden px-5 pb-5 sm:px-6 sm:pb-6 pt-0 text-sm sm:text-base text-slate-600 leading-relaxed border-t border-slate-100">
                        <p class="pt-3">{{ $faq['answer'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Help box -->
        <div class="p-6 sm:p-8 rounded-2xl text-white flex flex-col sm:flex-row items-center justify-between gap-6 border"
             style="background: linear-gradient(135deg, var(--theme-secondary) 0%, var(--theme-primary) 100%); border-color: var(--theme-secondary);">
            <div class="space-y-1 text-center sm:text-left">
                <h3 class="text-lg font-bold text-white">Pertanyaan Anda belum terjawab?</h3>
                <p class="text-xs sm:text-sm text-slate-300">
                    Sampaikan pertanyaan langsung ke sekretariat BEM atau kirimkan melalui kanal aspirasi.
                </p>
            </div>
            <div class="flex items-center gap-3 shrink-0">
                <a href="{{ route('aspirations.info') }}"
                   class="px-4 py-2.5 rounded-lg text-slate-950 font-bold text-xs sm:text-sm shadow-md transition-all hover:opacity-90"
                   style="background-color: var(--theme-accent); color: #020617;">
                    Sampaikan Aspirasi
                </a>
                <a href="{{ route('contact') }}"
                   class="px-4 py-2.5 rounded-lg text-white font-semibold text-xs sm:text-sm border transition-all hover:opacity-90"
                   style="background-color: var(--theme-secondary); border-color: var(--theme-secondary);">
                    Hubungi Kami
                </a>
            </div>
        </div>
    </div>
@endsection
