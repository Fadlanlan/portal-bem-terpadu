@extends('layouts.admin')

@section('title', 'Tambah Lembaga Ormawa Baru')
@section('header_title', 'Tambah Lembaga Ormawa')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.organizations.index') }}"
           class="inline-flex items-center gap-2 text-xs sm:text-sm font-semibold text-slate-600 hover:text-slate-900 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            <span>Kembali ke Daftar Ormawa</span>
        </a>
    </div>

    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-6">
        <div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900">Formulir Lembaga Ormawa Baru</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Lengkapi profil lembaga kemahasiswaan, pimpinan, hierarki, fungsi pokok, dan bagan divisi struktur.
            </p>
        </div>

        <form action="{{ route('admin.organizations.store') }}" method="POST" class="space-y-6" novalidate>
            @csrf

            <!-- Kode & Nama Lembaga -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="code" class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-1.5">
                        Kode / Singkatan <span class="text-rose-500">*</span>
                    </label>
                    <input type="text"
                           id="code"
                           name="code"
                           value="{{ old('code') }}"
                           required
                           maxlength="32"
                           placeholder="Misal: BEM, MAM, HIMA-TI"
                           class="w-full rounded-xl border border-slate-300 py-3 px-4 text-sm font-mono font-bold uppercase text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-500 @error('code') border-rose-500 ring-1 ring-rose-500 @enderror">
                    @error('code')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="sm:col-span-2">
                    <label for="name" class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-1.5">
                        Nama Lengkap Organisasi <span class="text-rose-500">*</span>
                    </label>
                    <input type="text"
                           id="name"
                           name="name"
                           value="{{ old('name') }}"
                           required
                           maxlength="200"
                           placeholder="Contoh: Badan Eksekutif Mahasiswa Universitas"
                           class="w-full rounded-xl border border-slate-300 py-3 px-4 text-sm font-semibold text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-500 @error('name') border-rose-500 ring-1 ring-rose-500 @enderror">
                    @error('name')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Tingkat, Peran, Urutan & Status -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="level" class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-1.5">
                        Tingkat Organisasi
                    </label>
                    <input type="text"
                           id="level"
                           name="level"
                           value="{{ old('level', 'Tingkat Universitas') }}"
                           placeholder="Misal: Universitas, Fakultas"
                           class="w-full rounded-xl border border-slate-300 py-2.5 px-3.5 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-500">
                </div>

                <div>
                    <label for="role_type" class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-1.5">
                        Jenis Peran / Fungsi
                    </label>
                    <input type="text"
                           id="role_type"
                           name="role_type"
                           value="{{ old('role_type', 'Lembaga Eksekutif') }}"
                           placeholder="Misal: Lembaga Eksekutif / Legislatif"
                           class="w-full rounded-xl border border-slate-300 py-2.5 px-3.5 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-500">
                </div>

                <div>
                    <label for="sort_order" class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-1.5">
                        Nomor Urutan Tampilan <span class="text-rose-500">*</span>
                    </label>
                    <input type="number"
                           id="sort_order"
                           name="sort_order"
                           value="{{ old('sort_order', 1) }}"
                           required
                           min="0"
                           class="w-full rounded-xl border border-slate-300 py-2.5 px-3.5 text-sm font-mono text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-500 @error('sort_order') border-rose-500 @enderror">
                    @error('sort_order')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Ketua & Periode Kepengurusan -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="leader_name" class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-1.5">
                        Nama Ketua / Presiden
                    </label>
                    <input type="text"
                           id="leader_name"
                           name="leader_name"
                           value="{{ old('leader_name') }}"
                           placeholder="Contoh: Muhammad Rayhan Pratama"
                           class="w-full rounded-xl border border-slate-300 py-2.5 px-3.5 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-500">
                </div>

                <div>
                    <label for="period" class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-1.5">
                        Periode Masa Bakti
                    </label>
                    <input type="text"
                           id="period"
                           name="period"
                           value="{{ old('period', '2026/2027') }}"
                           placeholder="Contoh: 2026/2027"
                           class="w-full rounded-xl border border-slate-300 py-2.5 px-3.5 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-500">
                </div>
            </div>

            <!-- Deskripsi Lembaga -->
            <div>
                <label for="description" class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-1.5">
                    Deskripsi Lembaga Ormawa <span class="text-rose-500">*</span>
                </label>
                <textarea id="description"
                          name="description"
                          rows="5"
                          required
                          placeholder="Jelaskan visi, misi, dan peran lembaga ormawa dalam kehidupan kampus..."
                          class="w-full rounded-xl border border-slate-300 py-3 px-4 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-500 @error('description') border-rose-500 @enderror">{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Fungsi Pokok & Struktur Divisi -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="functions" class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-1.5">
                        Tugas & Fungsi Pokok
                    </label>
                    <textarea id="functions"
                              name="functions"
                              rows="4"
                              placeholder="Ketik 1 poin tugas pokok per baris...
Contoh:
Melaksanakan aspirasi dan advokasi mahasiswa
Mengkoordinasikan program kerja antar kementerian
Menjadi mitra kritis kebijakan rektorat"
                              class="w-full rounded-xl border border-slate-300 py-2.5 px-3.5 text-xs text-slate-900 font-mono focus:outline-none focus:ring-2 focus:ring-amber-500">{{ old('functions') }}</textarea>
                </div>

                <div>
                    <label for="structure_overview" class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-1.5">
                        Bagan / Ringkasan Divisi
                    </label>
                    <textarea id="structure_overview"
                              name="structure_overview"
                              rows="4"
                              placeholder="Ketik 1 nama divisi/biro per baris...
Contoh:
Presiden & Wakil Presiden Mahasiswa
Sekretariat & Kebendaharaan
Kementerian Sosial & Advokasi
Kementerian Komunikasi & Informasi"
                              class="w-full rounded-xl border border-slate-300 py-2.5 px-3.5 text-xs text-slate-900 font-mono focus:outline-none focus:ring-2 focus:ring-amber-500">{{ old('structure_overview') }}</textarea>
                </div>
            </div>

            <!-- Status Publikasi -->
            <div>
                <label for="status" class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-1.5">
                    Status Publikasi Lembaga <span class="text-rose-500">*</span>
                </label>
                <select id="status"
                        name="status"
                        required
                        class="w-full sm:w-64 rounded-xl border border-slate-300 py-2.5 px-3.5 text-sm text-slate-900 bg-white focus:outline-none focus:ring-2 focus:ring-amber-500">
                    <option value="published" {{ old('status', 'published') === 'published' ? 'selected' : '' }}>Terbitkan Sekarang (Published)</option>
                    <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>Simpan sebagai Draft</option>
                </select>
            </div>

            <!-- Tombol Aksi -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200">
                <a href="{{ route('admin.organizations.index') }}"
                   class="px-5 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
                    Batal
                </a>
                <button type="submit"
                        class="px-6 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs sm:text-sm font-bold shadow-md transition-colors">
                    Simpan Lembaga
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
