@extends('layouts.app')

@section('title', 'Tanda Terima Aspirasi Mahasiswa')
@section('meta_description', 'Tanda terima resmi pengiriman aspirasi mahasiswa dengan nomor tiket dan kode pelacakan rahasia.')

@section('content')
    <div class="min-h-[75vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-2xl w-full space-y-6">
            <!-- Header Card -->
            <div class="bg-white rounded-3xl border border-slate-200 shadow-xl overflow-hidden">
                <!-- Top Success Banner -->
                <div class="bg-gradient-to-r from-emerald-600 via-emerald-700 to-teal-800 p-6 sm:p-8 text-white text-center">
                    <div class="w-16 h-16 rounded-full bg-white/20 backdrop-blur-md flex items-center justify-center mx-auto mb-4 border border-white/30 shadow-inner">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <span class="inline-block px-3 py-1 rounded-full bg-white/20 text-white text-xs font-bold tracking-wide uppercase mb-2">
                        Pengiriman Berhasil
                    </span>
                    <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Aspirasi Anda Telah Diterima</h1>
                    <p class="text-xs sm:text-sm text-emerald-100 mt-2 max-w-md mx-auto">
                        Aspirasi Anda telah masuk ke dalam antrean sistem verifikasi tim advokasi kemahasiswaan BEM.
                    </p>
                    <p class="text-[11px] text-emerald-200 mt-1 max-w-md mx-auto italic">
                        *Catatan: Keberhasilan penyimpanan data tidak menjamin penerimaan surel jika layanan surel sedang dalam pemeliharaan atau surel tidak dicantumkan.
                    </p>
                </div>

                <!-- Credential Box -->
                <div class="p-6 sm:p-10 space-y-6">
                    <!-- Warning Banner -->
                    <div class="p-4 sm:p-5 rounded-2xl bg-amber-50 border border-amber-300 text-amber-950 flex items-start gap-3.5">
                        <svg class="w-6 h-6 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        <div class="space-y-1 text-xs sm:text-sm">
                            <strong class="font-bold text-amber-900 block text-sm sm:text-base">PERHATIAN PENTING: Simpan Informasi Ini Sekarang!</strong>
                            <p class="text-amber-800 leading-relaxed">
                                Kode Pelacakan Rahasia ini <strong>hanya ditampilkan SEKALI</strong> pada layar ini dan disimpan dalam bentuk terenkripsi (hash). Demi alasan privasi dan keamanan, kami tidak dapat memulihkan kode rahasia jika hilang.
                            </p>
                        </div>
                    </div>

                    <!-- Credential 1: Nomor Tiket -->
                    <div class="p-4 sm:p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-2">
                        <div class="flex items-center justify-between text-xs text-slate-500 font-semibold uppercase tracking-wider">
                            <span>1. Nomor Tiket Pelacakan</span>
                            <span class="text-slate-400 font-normal">Identitas Publik Laporan</span>
                        </div>
                        <div class="flex items-center justify-between gap-3">
                            <span id="ticket-number" class="text-xl sm:text-2xl font-mono font-extrabold text-slate-900 tracking-wider">
                                {{ $ticketNumber }}
                            </span>
                            <button type="button"
                                    onclick="copyToClipboard('{{ $ticketNumber }}', this)"
                                    class="px-3.5 py-1.5 rounded-lg bg-white border border-slate-300 hover:bg-slate-100 text-slate-700 text-xs font-semibold shadow-sm transition-all flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                <span>Salin</span>
                            </button>
                        </div>
                    </div>

                    <!-- Credential 2: Kode Rahasia -->
                    <div class="p-4 sm:p-5 rounded-2xl bg-slate-900 text-white border border-slate-800 space-y-2 shadow-inner">
                        <div class="flex items-center justify-between text-xs text-amber-400 font-semibold uppercase tracking-wider">
                            <span>2. Kode Pelacakan Rahasia</span>
                            <span class="text-rose-400 font-bold">Kunci Otentikasi Laporan</span>
                        </div>
                        <div class="flex items-center justify-between gap-3">
                            <span id="tracking-code" class="text-xl sm:text-2xl font-mono font-extrabold text-amber-300 tracking-wider select-all">
                                {{ $trackingCode }}
                            </span>
                            <button type="button"
                                    onclick="copyToClipboard('{{ $trackingCode }}', this)"
                                    class="px-3.5 py-1.5 rounded-lg bg-amber-500 hover:bg-amber-400 text-slate-950 text-xs font-bold shadow-sm transition-all flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                <span>Salin Kode</span>
                            </button>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="pt-4 flex flex-col sm:flex-row items-center justify-between gap-4">
                        <a href="{{ route('aspirations.tracking.form') }}"
                           class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-semibold text-sm transition-colors shadow">
                            <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            <span>Lacak Status Tiket Ini</span>
                        </a>

                        <a href="{{ route('home') }}"
                           class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-sm transition-colors">
                            <span>Kembali ke Beranda</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function copyToClipboard(text, btn) {
        navigator.clipboard.writeText(text).then(function() {
            const originalText = btn.innerHTML;
            btn.innerHTML = '<span>Tersalin!</span>';
            btn.classList.add('bg-emerald-500', 'text-white');
            setTimeout(function() {
                btn.innerHTML = originalText;
                btn.classList.remove('bg-emerald-500', 'text-white');
            }, 2000);
        });
    }
</script>
@endpush
