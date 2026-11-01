@extends('layouts.admin')

@section('title', 'Tindak Lanjut Tiket ' . $aspiration->ticket_number)
@section('header_title', 'Tindak Lanjut Aspirasi')

@section('content')
<div class="space-y-6 max-w-5xl mx-auto">
    <!-- Breadcrumb & Back -->
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.aspirations.index') }}"
           class="inline-flex items-center gap-2 text-xs sm:text-sm font-semibold text-slate-600 hover:text-slate-900 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            <span>Kembali ke Daftar Aspirasi</span>
        </a>

        <div class="flex items-center gap-2">
            <span class="text-xs text-slate-500 font-medium">Status Terkini:</span>
            <x-badge :type="$aspiration->status_badge_type" class="text-xs px-3 py-1 font-bold">
                {{ $aspiration->status_label }}
            </x-badge>
        </div>
    </div>

    <!-- Main Detail Card -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden">
        <!-- Top Info Header -->
        <div class="bg-slate-900 text-white p-6 sm:p-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <span class="text-xs font-mono font-bold uppercase tracking-wider text-amber-400">Identitas Laporan</span>
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight mt-0.5">
                    Tiket {{ $aspiration->ticket_number }}
                </h1>
                <p class="text-xs sm:text-sm text-slate-300 mt-1">
                    Kategori: <strong class="text-white">{{ $aspiration->category_label }}</strong> &bull;
                    Diterima pada {{ $aspiration->submitted_at?->format('d/m/Y - H:i') ?? '-' }} WIB
                </p>
            </div>

            <!-- Identity Pill -->
            <div class="p-3 rounded-2xl bg-slate-800/80 border border-slate-700 text-xs shrink-0">
                <span class="text-slate-400 block mb-0.5 font-semibold">Mode Pelapor:</span>
                @if($aspiration->is_anonymous)
                    <span class="font-bold text-amber-400 flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path></svg>
                        <span>Anonim (Identitas Terlindungi)</span>
                    </span>
                @else
                    <span class="font-bold text-white block">{{ $aspiration->reporter_name }}</span>
                    <span class="text-slate-300 block text-[11px]">{{ $aspiration->reporter_email }}</span>
                    @if($aspiration->reporter_student_id)
                        <span class="text-amber-300/80 block text-[10px] font-mono">NIM: {{ $aspiration->reporter_student_id }}</span>
                    @endif
                @endif
            </div>
        </div>

        <!-- Subject & Content -->
        <div class="p-6 sm:p-8 space-y-6">
            <div>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1">Subjek / Judul Aspirasi:</span>
                <h2 class="text-lg sm:text-xl font-extrabold text-slate-900">
                    {{ $aspiration->subject }}
                </h2>
            </div>

            <div>
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1">Uraian Isi Aspirasi:</span>
                <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 text-sm sm:text-base text-slate-800 leading-relaxed whitespace-pre-line">
                    {{ $aspiration->message }}
                </div>
            </div>

            <!-- Current Official Response if any -->
            @if($aspiration->admin_response)
                <div class="p-5 rounded-2xl bg-amber-50/70 border border-amber-200 space-y-2">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                        <strong class="text-xs font-bold uppercase tracking-wider text-amber-900">Tanggapan Resmi Tim Advokasi Terpublikasi:</strong>
                    </div>
                    <p class="text-xs sm:text-sm text-amber-950 leading-relaxed whitespace-pre-line">
                        {{ $aspiration->admin_response }}
                    </p>
                </div>
            @endif
        </div>
    </div>

    <!-- ==================== FORM TINDAK LANJUT ==================== -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-6">
        <div class="border-b border-slate-100 pb-4">
            <h2 class="text-lg font-bold text-slate-900">Perbarui Status & Berikan Tindak Lanjut</h2>
            <p class="text-xs text-slate-500 mt-0.5">
                Setiap perubahan status akan dicatat dalam riwayat jejak audit (audit trail) dan dapat dipantau oleh mahasiswa pelapor.
            </p>
        </div>

        <form action="{{ route('admin.aspirations.update', $aspiration) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Status Selection -->
            <div>
                <label for="status" class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-2">
                    Status Penanganan Baru <span class="text-rose-500">*</span>
                </label>
                <select id="status"
                        name="status"
                        required
                        class="w-full sm:w-80 rounded-xl border border-slate-300 py-3 px-4 text-sm font-semibold text-slate-900 bg-white focus:outline-none focus:ring-2 focus:ring-amber-500 @error('status') border-rose-500 ring-1 ring-rose-500 @enderror">
                    @foreach($statuses as $value => $label)
                        <option value="{{ $value }}" {{ old('status', $aspiration->status->value ?? $aspiration->status) === $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                @error('status')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Catatan Internal Riwayat -->
            <div>
                <label for="note" class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-2">
                    Catatan Penanganan Riwayat <span class="text-slate-400 font-normal">(Muncul pada linimasa pelacakan)</span>
                </label>
                <textarea id="note"
                          name="note"
                          rows="3"
                          maxlength="1000"
                          placeholder="Contoh: Berkas telah diverifikasi dan diajukan dalam rapat dengar pendapat bersama Biro Keuangan..."
                          class="w-full rounded-xl border border-slate-300 py-3 px-4 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-500 @error('note') border-rose-500 @enderror">{{ old('note') }}</textarea>
                @error('note')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Tanggapan Resmi Advokasi BEM -->
            <div>
                <label for="admin_response" class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-2">
                    Tanggapan / Jawaban Resmi Pengelola <span class="text-slate-400 font-normal">(Ditampilkan di kotak respons resmi pelapor)</span>
                </label>
                <textarea id="admin_response"
                          name="admin_response"
                          rows="4"
                          maxlength="5000"
                          placeholder="Tuliskan jawaban resmi, solusi, atau instruksi langkah lanjut untuk mahasiswa..."
                          class="w-full rounded-xl border border-slate-300 py-3 px-4 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-500 @error('admin_response') border-rose-500 @enderror">{{ old('admin_response', $aspiration->admin_response) }}</textarea>
                @error('admin_response')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                <span class="text-xs text-slate-400 italic">
                    Perubahan status akan dicatat atas nama <strong>{{ Auth::user()->name }}</strong>.
                </span>

                <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs sm:text-sm shadow-md transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span>Simpan Perubahan Status</span>
                </button>
            </div>
        </form>
    </div>

    <!-- ==================== AUDIT TRAIL / RIWAYAT STATUS ==================== -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-6">
        <h2 class="text-base sm:text-lg font-bold text-slate-900 border-b border-slate-100 pb-3">
            Jejak Audit Riwayat Penanganan (Audit Trail)
        </h2>

        @if($aspiration->statusHistories->isNotEmpty())
            <div class="relative pl-6 space-y-6 border-l-2 border-slate-200 ml-3">
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
                                    {{ $history->created_at->format('d/m/Y - H:i') }} WIB
                                </span>
                            </div>

                            @if($history->note)
                                <p class="text-xs sm:text-sm text-slate-700 leading-relaxed bg-slate-50 p-3 rounded-xl border border-slate-200/60 mt-1">
                                    {{ $history->note }}
                                </p>
                            @endif

                            <div class="text-[11px] text-slate-400 flex items-center gap-1.5 pt-0.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                <span>Petugas: <strong>{{ $history->changedByUser?->name ?? 'Sistem / Pelapor' }}</strong></span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-xs text-slate-400 italic">
                Belum ada catatan riwayat perubahan status.
            </div>
        @endif
    </div>
</div>
@endsection
