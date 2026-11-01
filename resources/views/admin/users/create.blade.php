@extends('layouts.admin')

@section('title', 'Tambah Akun Pengelola Baru')
@section('header_title', 'Tambah Pengguna Sistem')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.users.index') }}"
           class="inline-flex items-center gap-2 text-xs sm:text-sm font-semibold text-slate-600 hover:text-slate-900 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            <span>Kembali ke Daftar Pengelola</span>
        </a>
    </div>

    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-6">
        <div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900">Formulir Tambah Pengelola</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Tentukan identitas pengurus, alamat email resmi, peranan wewenang, dan kata sandi awal akun.
            </p>
        </div>

        <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-6" novalidate>
            @csrf

            <!-- Nama Pengelola -->
            <div>
                <label for="name" class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-1.5">
                    Nama Lengkap Pengurus <span class="text-rose-500">*</span>
                </label>
                <input type="text"
                       id="name"
                       name="name"
                       value="{{ old('name') }}"
                       required
                       maxlength="150"
                       placeholder="Contoh: Muhammad Fauzan"
                       class="w-full rounded-xl border border-slate-300 py-3 px-4 text-sm font-semibold text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-500 @error('name') border-rose-500 ring-1 ring-rose-500 @enderror">
                @error('name')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email & Peran -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="email" class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-1.5">
                        Alamat Surel (Email) <span class="text-rose-500">*</span>
                    </label>
                    <input type="email"
                           id="email"
                           name="email"
                           value="{{ old('email') }}"
                           required
                           maxlength="150"
                           placeholder="nama@bem.kampus.ac.id"
                           class="w-full rounded-xl border border-slate-300 py-2.5 px-3.5 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-500 @error('email') border-rose-500 @enderror">
                    @error('email')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="role" class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-1.5">
                        Peranan / Role Wewenang <span class="text-rose-500">*</span>
                    </label>
                    <select id="role"
                            name="role"
                            required
                            class="w-full rounded-xl border border-slate-300 py-2.5 px-3.5 text-sm text-slate-900 bg-white focus:outline-none focus:ring-2 focus:ring-amber-500 @error('role') border-rose-500 @enderror">
                        <option value="">-- Pilih Peran Pengguna --</option>
                        @foreach($roles as $val => $lbl)
                            <option value="{{ $val }}" {{ old('role') === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                        @endforeach
                    </select>
                    @error('role')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Role Permissions Guidance Box -->
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-xs text-slate-600 space-y-1">
                <span class="font-bold text-slate-900 block mb-1">Panduan Hak Akses Peranan:</span>
                <p>• <strong>Super Admin:</strong> Hak akses tak terbatas ke seluruh modul dan manajemen akun pengelola.</p>
                <p>• <strong>Admin:</strong> Mengelola modul operasional (aspirasi, warta, program kerja, agenda, ormawa).</p>
                <p>• <strong>Advokasi:</strong> Khusus merespons dan menindaklanjuti modul aspirasi mahasiswa.</p>
                <p>• <strong>Editor:</strong> Khusus menyunting warta berita, program kerja, agenda, dan ormawa.</p>
            </div>

            <!-- Kata Sandi & Konfirmasi -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="password" class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-1.5">
                        Kata Sandi Awal <span class="text-rose-500">*</span>
                    </label>
                    <input type="password"
                           id="password"
                           name="password"
                           required
                           minlength="8"
                           placeholder="Minimal 8 karakter..."
                           class="w-full rounded-xl border border-slate-300 py-2.5 px-3.5 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-500 @error('password') border-rose-500 @enderror">
                    @error('password')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-1.5">
                        Konfirmasi Kata Sandi <span class="text-rose-500">*</span>
                    </label>
                    <input type="password"
                           id="password_confirmation"
                           name="password_confirmation"
                           required
                           minlength="8"
                           placeholder="Ulangi kata sandi..."
                           class="w-full rounded-xl border border-slate-300 py-2.5 px-3.5 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-500">
                </div>
            </div>

            <!-- Status Aktif -->
            <div>
                <label class="inline-flex items-center gap-3 cursor-pointer">
                    <input type="checkbox"
                           name="is_active"
                           value="1"
                           {{ old('is_active', '1') ? 'checked' : '' }}
                           class="w-4 h-4 text-blue-900 rounded border-slate-300 focus:ring-amber-500">
                    <div>
                        <span class="text-xs font-bold text-slate-800 block">Akun Aktif Langsung</span>
                        <span class="text-[11px] text-slate-500 block">Pengguna dapat langsung masuk ke dashboard setelah akun dibuat.</span>
                    </div>
                </label>
            </div>

            <!-- Tombol Aksi -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200">
                <a href="{{ route('admin.users.index') }}"
                   class="px-5 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
                    Batal
                </a>
                <button type="submit"
                        class="px-6 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs sm:text-sm font-bold shadow-md transition-colors">
                    Buat Akun Pengelola
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
