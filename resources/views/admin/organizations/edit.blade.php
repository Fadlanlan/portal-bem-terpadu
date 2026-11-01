@extends('layouts.admin')

@section('title', 'Sunting Lembaga: ' . $organization->name)
@section('header_title', 'Sunting Lembaga Ormawa')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.organizations.index') }}"
           class="inline-flex items-center gap-2 text-xs sm:text-sm font-semibold text-slate-600 hover:text-slate-900 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            <span>Kembali ke Daftar Ormawa</span>
        </a>

        @if($organization->status === 'published')
            <a href="{{ route('organizations.index') }}" target="_blank"
               class="text-xs text-amber-700 font-semibold hover:underline inline-flex items-center gap-1">
                <span>Lihat Tampilan Publik</span>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
            </a>
        @endif
    </div>

    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-6">
        <div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900">Sunting Lembaga Ormawa</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Perbarui profil lembaga kemahasiswaan, nama pimpinan, hierarki, tugas pokok, dan struktur.
            </p>
        </div>

        <form action="{{ route('admin.organizations.update', $organization) }}" method="POST" class="space-y-6" novalidate>
            @csrf
            @method('PUT')

            <!-- Kode & Nama Lembaga -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="code" class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-1.5">
                        Kode / Singkatan <span class="text-rose-500">*</span>
                    </label>
                    <input type="text"
                           id="code"
                           name="code"
                           value="{{ old('code', $organization->code) }}"
                           required
                           maxlength="32"
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
                           value="{{ old('name', $organization->name) }}"
                           required
                           maxlength="200"
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
                           value="{{ old('level', $organization->level) }}"
                           class="w-full rounded-xl border border-slate-300 py-2.5 px-3.5 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-500">
                </div>

                <div>
                    <label for="role_type" class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-1.5">
                        Jenis Peran / Fungsi
                    </label>
                    <input type="text"
                           id="role_type"
                           name="role_type"
                           value="{{ old('role_type', $organization->role_type) }}"
                           class="w-full rounded-xl border border-slate-300 py-2.5 px-3.5 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-500">
                </div>

                <div>
                    <label for="sort_order" class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-1.5">
                        Nomor Urutan Tampilan <span class="text-rose-500">*</span>
                    </label>
                    <input type="number"
                           id="sort_order"
                           name="sort_order"
                           value="{{ old('sort_order', $organization->sort_order) }}"
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
                           value="{{ old('leader_name', $organization->leader_name) }}"
                           class="w-full rounded-xl border border-slate-300 py-2.5 px-3.5 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-500">
                </div>

                <div>
                    <label for="period" class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-1.5">
                        Periode Masa Bakti
                    </label>
                    <input type="text"
                           id="period"
                           name="period"
                           value="{{ old('period', $organization->period) }}"
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
                          class="w-full rounded-xl border border-slate-300 py-3 px-4 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-500 @error('description') border-rose-500 @enderror">{{ old('description', $organization->description) }}</textarea>
                @error('description')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Fungsi Pokok & Struktur Divisi -->
            @php
                $functionsVal = is_array($organization->functions) ? implode("\n", $organization->functions) : (string) $organization->functions;
                $structureVal = is_array($organization->structure_overview) ? implode("\n", $organization->structure_overview) : (string) $organization->structure_overview;
            @endphp
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="functions" class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-1.5">
                        Tugas & Fungsi Pokok
                    </label>
                    <textarea id="functions"
                              name="functions"
                              rows="4"
                              placeholder="1 tugas pokok per baris..."
                              class="w-full rounded-xl border border-slate-300 py-2.5 px-3.5 text-xs text-slate-900 font-mono focus:outline-none focus:ring-2 focus:ring-amber-500">{{ old('functions', $functionsVal) }}</textarea>
                </div>

                <div>
                    <label for="structure_overview" class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-1.5">
                        Bagan / Ringkasan Divisi
                    </label>
                    <textarea id="structure_overview"
                              name="structure_overview"
                              rows="4"
                              placeholder="1 nama divisi per baris..."
                              class="w-full rounded-xl border border-slate-300 py-2.5 px-3.5 text-xs text-slate-900 font-mono focus:outline-none focus:ring-2 focus:ring-amber-500">{{ old('structure_overview', $structureVal) }}</textarea>
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
                    <option value="published" {{ old('status', $organization->status) === 'published' ? 'selected' : '' }}>Terbitkan di Publik (Published)</option>
                    <option value="draft" {{ old('status', $organization->status) === 'draft' ? 'selected' : '' }}>Simpan sebagai Draft</option>
                </select>
            </div>

            <!-- Tombol Aksi -->
            <div class="flex items-center justify-between pt-4 border-t border-slate-200">
                <div>
                    <!-- Tombol Hapus -->
                    <button type="button"
                            onclick="if(confirm('Apakah Anda yakin ingin menghapus lembaga \'{{ addslashes($organization->name) }}\'? Tindakan ini tidak dapat dibatalkan.')) { document.getElementById('delete-org-form').submit(); }"
                            class="px-4 py-2.5 rounded-xl border border-rose-200 text-rose-600 hover:bg-rose-50 text-xs sm:text-sm font-semibold transition-colors">
                        Hapus Lembaga
                    </button>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.organizations.index') }}"
                       class="px-5 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
                        Batal
                    </a>
                    <button type="submit"
                            class="px-6 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs sm:text-sm shadow-md transition-colors">
                        Simpan Perubahan
                    </button>
                </div>
            </div>
        </form>

        <form id="delete-org-form" action="{{ route('admin.organizations.destroy', $organization) }}" method="POST" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    </div>
</div>
@endsection
