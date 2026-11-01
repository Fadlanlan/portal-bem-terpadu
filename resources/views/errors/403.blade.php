@extends('layouts.app')

@section('title', 'Akses Ditolak — 403 Forbidden')
@section('meta_description', 'Akses ke halaman ini dibatasi atau hak akses akun Anda tidak mencukupi.')
@section('robots_meta', 'noindex, follow')

@section('content')
    <div class="min-h-[70vh] flex items-center justify-center py-16 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full text-center space-y-6 bg-white p-8 sm:p-10 rounded-3xl border border-slate-200 shadow-xl">
            <div class="w-20 h-20 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center mx-auto border border-rose-200 shadow-inner">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m0 0v2m0-2h2m-2 0H10m4-8a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                </svg>
            </div>

            <div class="space-y-2">
                <span class="text-xs font-mono font-bold uppercase tracking-widest text-rose-600 bg-rose-100/60 px-3 py-1 rounded-full">
                    Galat 403 &bull; Akses Dibatasi
                </span>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                    Hak Akses Tidak Mencukupi
                </h1>
                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                    {{ $exception->getMessage() ?: 'Anda tidak memiliki hak otorisasi untuk mengakses halaman atau modul manajemen ini. Silakan hubungi Super Administrator jika ini adalah kekeliruan.' }}
                </p>
            </div>

            <div class="pt-4 flex flex-col sm:flex-row items-center justify-center gap-3">
                <a href="{{ url()->previous() ?: route('admin.dashboard') }}"
                   class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-semibold text-xs sm:text-sm transition-colors shadow">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    <span>Kembali</span>
                </a>
                <a href="{{ route('home') }}"
                   class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs sm:text-sm transition-colors">
                    <span>Halaman Utama</span>
                </a>
            </div>
        </div>
    </div>
@endsection
