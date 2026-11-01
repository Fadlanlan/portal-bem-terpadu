@extends('layouts.admin')

@section('title', 'Tulis Warta Berita Baru')
@section('header_title', 'Tulis Warta Berita')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.articles.index') }}"
           class="inline-flex items-center gap-2 text-xs sm:text-sm font-semibold text-slate-600 hover:text-slate-900 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            <span>Kembali ke Daftar Berita</span>
        </a>
    </div>

    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-6">
        <div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900">Formulir Publikasi Warta Berita</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Lengkapi rincian judul, ringkasan pengantar, kategori, dan narasi berita organisasi.
            </p>
        </div>

        <form action="{{ route('admin.articles.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6" novalidate>
            @csrf

            <!-- Judul Berita -->
            <div>
                <label for="title" class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-1.5">
                    Judul Warta Berita <span class="text-rose-500">*</span>
                </label>
                <input type="text"
                       id="title"
                       name="title"
                       value="{{ old('title') }}"
                       required
                       maxlength="200"
                       placeholder="Contoh: BEM Resmikan Posko Bersama Bantuan Advokasi Finansial Mahasiswa"
                       class="w-full rounded-xl border border-slate-300 py-3 px-4 text-sm font-semibold text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-500 @error('title') border-rose-500 ring-1 ring-rose-500 @enderror">
                @error('title')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Kategori & Penulis -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="category" class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-1.5">
                        Kategori Berita <span class="text-rose-500">*</span>
                    </label>
                    <select id="category"
                            name="category"
                            required
                            class="w-full rounded-xl border border-slate-300 py-2.5 px-3.5 text-sm text-slate-900 bg-white focus:outline-none focus:ring-2 focus:ring-amber-500 @error('category') border-rose-500 @enderror">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach(['Advokasi & Aspirasi', 'Pengumuman', 'Pelatihan', 'Kegiatan', 'Akademik', 'Lainnya'] as $cat)
                            <option value="{{ $cat }}" {{ old('category') === $cat ? 'selected' : '' }}>
                                {{ $cat }}
                            </option>
                        @endforeach
                    </select>
                    @error('category')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="author_name" class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-1.5">
                        Penulis / Redaksi <span class="text-slate-400 font-normal">(Opsional)</span>
                    </label>
                    <input type="text"
                           id="author_name"
                           name="author_name"
                           value="{{ old('author_name', Auth::user()->name) }}"
                           placeholder="Biro Komunikasi & Informasi BEM"
                           class="w-full rounded-xl border border-slate-300 py-2.5 px-3.5 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-500">
                </div>
            </div>

            <!-- Ringkasan / Excerpt -->
            <div>
                <label for="excerpt" class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-1.5">
                    Ringkasan Berita (Lead / Excerpt) <span class="text-rose-500">*</span>
                </label>
                <textarea id="excerpt"
                          name="excerpt"
                          rows="2"
                          required
                          maxlength="1000"
                          placeholder="Ringkasan 1-2 kalimat pengantar yang tampil di kartu berita publik..."
                          class="w-full rounded-xl border border-slate-300 py-3 px-4 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-500 @error('excerpt') border-rose-500 @enderror">{{ old('excerpt') }}</textarea>
                @error('excerpt')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Konten Lengkap -->
            <div>
                <label for="content" class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-1.5">
                    Isi Lengkap Berita <span class="text-rose-500">*</span>
                </label>
                <textarea id="content"
                          name="content"
                          rows="8"
                          required
                          placeholder="Tuliskan isi berita secara lengkap. Pisahkan paragraf dengan baris kosong ganda (Enter dua kali)..."
                          class="w-full rounded-xl border border-slate-300 py-3 px-4 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-500 @error('content') border-rose-500 @enderror">{{ old('content') }}</textarea>
                <p class="mt-1 text-[11px] text-slate-400">Gunakan spasi dua baris antar paragraf untuk pemisahan otomatis di tampilan artikel.</p>
                @error('content')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Pengaturan Status & Gambar -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-4 border-t border-slate-100">
                <div>
                    <label for="status" class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-1.5">
                        Status Publikasi <span class="text-rose-500">*</span>
                    </label>
                    <select id="status"
                            name="status"
                            required
                            class="w-full rounded-xl border border-slate-300 py-2.5 px-3.5 text-sm text-slate-900 bg-white focus:outline-none focus:ring-2 focus:ring-amber-500 @error('status') border-rose-500 @enderror">
                        <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>Konsep (Draft - Tidak Tampil di Publik)</option>
                        <option value="published" {{ old('status', 'published') === 'published' ? 'selected' : '' }}>Terbitkan Langsung (Published)</option>
                    </select>
                </div>

                <div>
                    <label for="image" class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-1.5">
                        Unggah Gambar Sampul <span class="text-slate-400 font-normal">(Opsional, Maks 2MB)</span>
                    </label>
                    <input type="file"
                           id="image"
                           name="image"
                           accept="image/png,image/jpeg,image/webp"
                           class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-slate-900 file:text-white hover:file:bg-slate-800 cursor-pointer">
                    <p class="mt-1 text-[11px] text-slate-400">Format yang didukung: JPG, JPEG, PNG, WebP (maksimal 2MB).</p>
                    @error('image')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Tombol Aksi -->
            <div class="pt-6 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('admin.articles.index') }}"
                   class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs sm:text-sm transition-colors">
                    Batal
                </a>
                <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs sm:text-sm shadow-md transition-all">
                    <span>Simpan & Terbitkan</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
