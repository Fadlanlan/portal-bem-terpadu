@extends('layouts.app')

@section('title', 'Kesalahan Server (500)')
@section('meta_description', 'Terjadi kesalahan tak terduga di sisi server. Tim Portal BEM Terpadu akan segera memverifikasi kendala teknis ini.')
@section('robots_meta', 'noindex, follow')

@section('content')
    <div class="min-h-[70vh] flex items-center justify-center py-16 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full text-center space-y-6">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-2xl bg-rose-500/10 border border-rose-500/30 text-rose-500 font-extrabold text-3xl font-mono shadow-sm">
                500
            </div>

            <div class="space-y-2">
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                    Terjadi Kesalahan Sistem
                </h1>
                <p class="text-sm text-slate-600 leading-relaxed">
                    Maaf, terjadi kendala teknis tak terduga di sisi server. Tim pengelola telah menerima notifikasi dan akan segera menindaklanjuti. Silakan coba kembali beberapa saat lagi.
                </p>
            </div>

            <div class="pt-2 flex flex-col sm:flex-row items-center justify-center gap-3">
                <a href="{{ route('home') }}"
                   class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-semibold text-sm transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    <span>Kembali ke Beranda</span>
                </a>
                <a href="{{ route('contact') }}"
                   class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-sm transition-colors">
                    <span>Hubungi Sekretariat</span>
                </a>
            </div>

            <div class="pt-4 border-t border-slate-200 text-xs text-slate-400">
                Apabila kendala berlanjut, silakan sampaikan laporan melalui <a href="{{ route('aspirations.create') }}" class="text-amber-700 hover:underline">Kanal Aspirasi</a>.
            </div>
        </div>
    </div>
@endsection
