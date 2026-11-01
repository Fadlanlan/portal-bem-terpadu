@extends('layouts.app')

@section('title', 'Lacak Status Aspirasi Mahasiswa')
@section('meta_description', 'Lacak status perkembangan tindak lanjut aspirasi mahasiswa menggunakan nomor tiket dan kode pelacakan rahasia.')

@section('content')
    <div class="text-white py-12 border-b" style="background-color: var(--theme-primary); border-bottom-color: var(--theme-secondary);">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-breadcrumb :items="['Kanal Aspirasi' => route('aspirations.info'), 'Lacak Status Tiket' => null]" />
            <div class="mt-4">
                <span class="text-xs font-bold uppercase tracking-wider" style="color: var(--theme-accent);">Transparansi Laporan</span>
                <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight mt-1">Lacak Status Aspirasi</h1>
                <p class="text-sm sm:text-base text-slate-300 mt-2">
                    Masukkan kombinasi Nomor Tiket dan Kode Pelacakan Rahasia Anda untuk melihat tahapan penanganan, disposisi, dan respons resmi tim advokasi.
                </p>
            </div>
        </div>
    </div>

    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-10 space-y-6">
            @if($errors->has('tracking_error'))
                <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-900 flex items-start gap-3" role="alert">
                    <svg class="w-5 h-5 text-rose-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    <div class="text-xs sm:text-sm">
                        <strong class="font-bold block text-sm">Verifikasi Tidak Berhasil:</strong>
                        <p class="mt-0.5">{{ $errors->first('tracking_error') }}</p>
                    </div>
                </div>
            @endif

            <form action="{{ route('aspirations.tracking.show') }}" method="POST" class="space-y-5" novalidate>
                @csrf

                <!-- Nomor Tiket -->
                <div>
                    <label for="ticket_number" class="block text-sm font-bold text-slate-900 mb-1.5">
                        Nomor Tiket Pelacakan <span class="text-rose-500">*</span>
                    </label>
                    <input type="text"
                           id="ticket_number"
                           name="ticket_number"
                           value="{{ old('ticket_number') }}"
                           required
                           autofocus
                           placeholder="Contoh: BEM-2026-AB12CD"
                           class="w-full font-mono rounded-xl border border-slate-300 py-3 px-4 text-sm text-slate-900 uppercase placeholder:normal-case placeholder:font-sans placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 @error('ticket_number') border-rose-500 ring-1 ring-rose-500 @enderror">
                    @error('ticket_number')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Kode Pelacakan Rahasia -->
                <div>
                    <label for="tracking_code" class="block text-sm font-bold text-slate-900 mb-1.5">
                        Kode Pelacakan Rahasia <span class="text-rose-500">*</span>
                    </label>
                    <input type="text"
                           id="tracking_code"
                           name="tracking_code"
                           required
                           placeholder="Contoh: TRK-XXXX-XXXX-XXXX"
                           class="w-full font-mono rounded-xl border border-slate-300 py-3 px-4 text-sm text-slate-900 uppercase placeholder:normal-case placeholder:font-sans placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 @error('tracking_code') border-rose-500 ring-1 ring-rose-500 @enderror">
                    <p class="mt-1 text-xs text-slate-500">
                        Kode rahasia yang Anda dapatkan saat pertama kali mengirimkan aspirasi.
                    </p>
                    @error('tracking_code')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Button -->
                <div class="pt-2">
                    <button type="submit"
                            class="w-full flex items-center justify-center gap-2 py-3.5 px-6 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-sm shadow-md transition-colors">
                        <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <span>Periksa Status Laporan</span>
                    </button>
                </div>
            </form>

            <!-- Keamanan Note -->
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-600 space-y-1">
                <strong class="text-slate-800 font-semibold block">Mengapa Memerlukan Kode Rahasia?</strong>
                <p>
                    Kombinasi Nomor Tiket dan Kode Rahasia melindungi privasi mahasiswa agar orang lain yang menebak nomor tiket Anda tidak dapat melihat isi laporan atau catatan internal advokasi Anda.
                </p>
            </div>

            <div class="pt-2 text-center text-xs sm:text-sm text-slate-500">
                Belum pernah menyampaikan aspirasi?
                <a href="{{ route('aspirations.create') }}" class="font-bold text-amber-700 hover:underline">
                    Kirim aspirasi baru di sini &rarr;
                </a>
            </div>
        </div>
    </div>
@endsection
