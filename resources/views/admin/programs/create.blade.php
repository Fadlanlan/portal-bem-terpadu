@extends('layouts.admin')

@section('title', 'Tambah Program Kerja Baru')
@section('header_title', 'Tambah Program Kerja')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.programs.index') }}"
           class="inline-flex items-center gap-2 text-xs sm:text-sm font-semibold text-slate-600 hover:text-slate-900 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            <span>Kembali ke Daftar Program</span>
        </a>
    </div>

    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-6">
        <div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900">Formulir Rencana Program Kerja</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Lengkapi rincian agenda program, kementerian penanggung jawab, sasaran, dan indikator keberhasilan.
            </p>
        </div>

        <form action="{{ route('admin.programs.store') }}" method="POST" class="space-y-6" novalidate>
            @csrf

            <!-- Nama Program -->
            <div>
                <label for="name" class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-1.5">
                    Nama Program Kerja <span class="text-rose-500">*</span>
                </label>
                <input type="text"
                       id="name"
                       name="name"
                       value="{{ old('name') }}"
                       required
                       maxlength="200"
                       placeholder="Contoh: Advokasi Transparansi & Bantuan Finansial Mahasiswa"
                       class="w-full rounded-xl border border-slate-300 py-3 px-4 text-sm font-semibold text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-500 @error('name') border-rose-500 ring-1 ring-rose-500 @enderror">
                @error('name')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Organisasi, Bidang, Status & Publikasi -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="organization" class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-1.5">
                        Organisasi Penyelenggara <span class="text-rose-500">*</span>
                    </label>
                    <input type="text"
                           id="organization"
                           name="organization"
                           value="{{ old('organization', 'Kementerian Sosial & Advokasi BEM') }}"
                           required
                           class="w-full rounded-xl border border-slate-300 py-2.5 px-3.5 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-500 @error('organization') border-rose-500 @enderror">
                    @error('organization')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="department" class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-1.5">
                        Bidang / Departemen
                    </label>
                    <input type="text"
                           id="department"
                           name="department"
                           value="{{ old('department', 'Sosial & Advokasi') }}"
                           placeholder="Contoh: Riset & Keilmuan"
                           class="w-full rounded-xl border border-slate-300 py-2.5 px-3.5 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-500">
                </div>

                <div>
                    <label for="status" class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-1.5">
                        Status Pelaksanaan <span class="text-rose-500">*</span>
                    </label>
                    <select id="status"
                            name="status"
                            required
                            class="w-full rounded-xl border border-slate-300 py-2.5 px-3.5 text-sm text-slate-900 bg-white focus:outline-none focus:ring-2 focus:ring-amber-500">
                        @foreach(['Berlangsung', 'Rencana', 'Selesai'] as $st)
                            <option value="{{ $st }}" {{ old('status', 'Rencana') === $st ? 'selected' : '' }}>{{ $st }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="publication_status" class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-1.5">
                        Status Publikasi <span class="text-rose-500">*</span>
                    </label>
                    <select id="publication_status"
                            name="publication_status"
                            required
                            class="w-full rounded-xl border border-slate-300 py-2.5 px-3.5 text-sm text-slate-900 bg-white focus:outline-none focus:ring-2 focus:ring-amber-500">
                        <option value="published" {{ old('publication_status', 'published') === 'published' ? 'selected' : '' }}>Terbitkan di Publik (Published)</option>
                        <option value="draft" {{ old('publication_status') === 'draft' ? 'selected' : '' }}>Simpan sebagai Draft</option>
                    </select>
                </div>
            </div>

            <!-- Periode & Timeline & PIC -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="period" class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-1.5">
                        Periode Program
                    </label>
                    <input type="text"
                           id="period"
                           name="period"
                           value="{{ old('period') }}"
                           placeholder="Semester Ganjil 2026/2027"
                           class="w-full rounded-xl border border-slate-300 py-2.5 px-3.5 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-500">
                </div>

                <div>
                    <label for="timeline" class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-1.5">
                        Rentang Waktu (Timeline)
                    </label>
                    <input type="text"
                           id="timeline"
                           name="timeline"
                           value="{{ old('timeline') }}"
                           placeholder="Agustus 2026 - Januari 2027"
                           class="w-full rounded-xl border border-slate-300 py-2.5 px-3.5 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-500">
                </div>

                <div>
                    <label for="pic" class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-1.5">
                        Penanggung Jawab (PIC)
                    </label>
                    <input type="text"
                           id="pic"
                           name="pic"
                           value="{{ old('pic') }}"
                           placeholder="Biro Bantuan Hukum & Advokasi"
                           class="w-full rounded-xl border border-slate-300 py-2.5 px-3.5 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-500">
                </div>
            </div>

            <!-- Deskripsi & Tujuan -->
            <div class="space-y-4 pt-2">
                <div>
                    <label for="description" class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-1.5">
                        Deskripsi Program <span class="text-rose-500">*</span>
                    </label>
                    <textarea id="description"
                              name="description"
                              rows="3"
                              required
                              placeholder="Uraikan gambaran umum pelaksanaan program kerja..."
                              class="w-full rounded-xl border border-slate-300 py-3 px-4 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-500 @error('description') border-rose-500 @enderror">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="objectives" class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-1.5">
                        Tujuan & Urgensi
                    </label>
                    <textarea id="objectives"
                              name="objectives"
                              rows="2"
                              placeholder="Apa target dampak positif yang ingin dicapai program ini bagi mahasiswa..."
                              class="w-full rounded-xl border border-slate-300 py-3 px-4 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-500">{{ old('objectives') }}</textarea>
                </div>
            </div>

            <!-- Sasaran / Targets & Indikator -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                <div>
                    <label for="targets" class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-1.5">
                        Poin Sasaran Target <span class="text-slate-400 font-normal">(Satu baris per poin target)</span>
                    </label>
                    <textarea id="targets"
                              name="targets"
                              rows="4"
                              placeholder="Posko pendampingan berkas&#10;Rekomendasi faktual ke biro rektorat&#10;Sosialisasi beasiswa kemitraan"
                              class="w-full rounded-xl border border-slate-300 py-2.5 px-3.5 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-500">{{ old('targets') }}</textarea>
                </div>

                <div>
                    <label for="indicators" class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-1.5">
                        Indikator Keberhasilan (KPI)
                    </label>
                    <textarea id="indicators"
                              name="indicators"
                              rows="4"
                              placeholder="Contoh: Minimal 80% permohonan tertangani dan mendapat klarifikasi resmi sebelum tenggat..."
                              class="w-full rounded-xl border border-slate-300 py-2.5 px-3.5 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-500">{{ old('indicators') }}</textarea>
                </div>
            </div>

            <!-- Tombol Aksi -->
            <div class="pt-6 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('admin.programs.index') }}"
                   class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs sm:text-sm transition-colors">
                    Batal
                </a>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs sm:text-sm shadow-md transition-all">
                    <span>Simpan Program Kerja</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
