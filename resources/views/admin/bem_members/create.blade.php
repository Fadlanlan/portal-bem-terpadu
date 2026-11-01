@extends('layouts.admin')

@section('title', 'Tambah Pengurus BEM Baru')
@section('header_title', 'Tambah Data Pengurus & Kabinet')

@section('content')
<div class="max-w-4xl space-y-6">
    <!-- Breadcrumb & Header -->
    <div class="flex items-center justify-between gap-4 pb-4 border-b border-slate-200">
        <div>
            <div class="inline-flex items-center gap-2 text-xs text-slate-500 mb-1">
                <a href="{{ route('admin.bem_members.index') }}" class="hover:text-blue-900">Pengurus BEM</a>
                <span>/</span>
                <span class="text-slate-900 font-semibold">Tambah Baru</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Tambah Pengurus Kabinet BEM</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Lengkapi biodata dan tingkatan hierarki struktural pejabat eksekutif BEM.
            </p>
        </div>

        <a href="{{ route('admin.bem_members.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            <span>Kembali ke Daftar</span>
        </a>
    </div>

    <!-- Alert Errors -->
    @if($errors->any())
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-sm space-y-1">
            <p class="font-bold">Mohon perbaiki isian formulir berikut:</p>
            <ul class="list-disc list-inside text-xs">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Form Body -->
    <form action="{{ route('admin.bem_members.store') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm space-y-6">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Nama Lengkap -->
            <div class="space-y-1.5 md:col-span-2">
                <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                    Nama Lengkap & Gelar / NIM <span class="text-rose-500">*</span>
                </label>
                <input type="text"
                       name="name"
                       id="name"
                       required
                       value="{{ old('name') }}"
                       placeholder="Contoh: Muhammad Raihan Pratama"
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 text-slate-900">
            </div>

            <!-- Jabatan -->
            <div class="space-y-1.5">
                <label for="position" class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                    Jabatan Struktural <span class="text-rose-500">*</span>
                </label>
                <input type="text"
                       name="position"
                       id="position"
                       required
                       value="{{ old('position') }}"
                       placeholder="Contoh: Presiden Mahasiswa / Menteri Advokasi"
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 text-slate-900">
            </div>

            <!-- Tingkatan Hierarki (Level) -->
            <div class="space-y-1.5">
                <label for="level" class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                    Tingkatan Hierarki <span class="text-rose-500">*</span>
                </label>
                <select name="level" id="level" required class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 text-slate-900">
                    @foreach($levels as $key => $label)
                        <option value="{{ $key }}" {{ old('level', 'staff') === $key ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Departemen / Kementerian -->
            <div class="space-y-1.5">
                <label for="department" class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                    Departemen / Kementerian / Biro
                </label>
                <input type="text"
                       name="department"
                       id="department"
                       value="{{ old('department') }}"
                       placeholder="Contoh: Kementerian Kebijakan Publik & Advokasi"
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 text-slate-900">
                <span class="text-[11px] text-slate-400">Kosongkan jika Presma, Wapresma, atau BPH Utama.</span>
            </div>

            <!-- Periode Kepengurusan -->
            <div class="space-y-1.5">
                <label for="period" class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                    Periode Kepengurusan
                </label>
                <input type="text"
                       name="period"
                       id="period"
                       value="{{ old('period', '2025/2026') }}"
                       placeholder="Contoh: 2025/2026"
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 text-slate-900">
            </div>

            <!-- Urutan Tampil (Sort Order) -->
            <div class="space-y-1.5">
                <label for="sort_order" class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                    Urutan Tampil <span class="text-rose-500">*</span>
                </label>
                <input type="number"
                       name="sort_order"
                       id="sort_order"
                       min="0"
                       required
                       value="{{ old('sort_order', $defaultSortOrder) }}"
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 text-slate-900">
                <span class="text-[11px] text-slate-400">Angka lebih kecil tampil lebih awal (contoh: 1 untuk Presma, 2 untuk Wapresma).</span>
            </div>

            <!-- Status Publikasi -->
            <div class="space-y-1.5 flex flex-col justify-end">
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                    Status Publikasi
                </label>
                <label class="inline-flex items-center gap-3 p-2.5 rounded-xl border border-slate-200 bg-slate-50 cursor-pointer hover:bg-slate-100 transition-colors">
                    <input type="checkbox"
                           name="is_published"
                           value="1"
                           {{ old('is_published', '1') ? 'checked' : '' }}
                           class="w-4 h-4 rounded text-blue-900 focus:ring-blue-500 border-slate-300">
                    <span class="text-xs font-bold text-slate-800">Publikasikan ke Halaman Publik</span>
                </label>
            </div>

            <!-- Foto Profil Pengurus -->
            <div class="space-y-1.5 md:col-span-2">
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                    Foto Profil Pengurus
                </label>
                <div class="flex items-center gap-5 p-4 rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50">
                    <div class="w-16 h-16 rounded-2xl overflow-hidden bg-slate-200 border border-slate-300 shrink-0 flex items-center justify-center">
                        <img id="photo_preview" src="{{ asset('images/member-placeholder.svg') }}" alt="Pratinjau Foto" class="w-full h-full object-cover">
                    </div>
                    <div class="space-y-1">
                        <input type="file"
                               name="photo"
                               id="photo"
                               accept="image/png,image/jpeg,image/jpg,image/webp"
                               onchange="previewPhoto(this)"
                               class="text-xs text-slate-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-900 file:text-white hover:file:bg-blue-800 cursor-pointer">
                        <p class="text-[11px] text-slate-400">Format: PNG, JPG, JPEG, WebP. Maksimal 2MB. Rasio 1:1 atau potret disarankan.</p>
                    </div>
                </div>
            </div>

            <!-- Deskripsi Singkat / Profil Pengurus -->
            <div class="space-y-1.5 md:col-span-2">
                <label for="description" class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                    Fokus Tugas / Deskripsi Singkat
                </label>
                <textarea name="description"
                          id="description"
                          rows="3"
                          placeholder="Ringkasan tanggung jawab atau kutipan visi personal..."
                          class="w-full px-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 text-slate-900">{{ old('description') }}</textarea>
            </div>
        </div>

        <div class="pt-6 border-t border-slate-200 flex items-center justify-end gap-3">
            <a href="{{ route('admin.bem_members.index') }}"
               class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-700 font-bold text-xs hover:bg-slate-50 transition-colors">
                Batal
            </a>
            <button type="submit"
                    class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-blue-900 hover:bg-blue-800 text-white font-bold text-xs shadow-md shadow-blue-950/20 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <span>Simpan Pengurus</span>
            </button>
        </div>
    </form>
</div>

<script>
    function previewPhoto(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('photo_preview').src = e.target.result;
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection
