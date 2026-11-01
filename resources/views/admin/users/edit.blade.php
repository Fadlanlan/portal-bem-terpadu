@extends('layouts.admin')

@section('title', 'Kelola Pengguna: ' . $user->name)
@section('header_title', 'Kelola Pengguna Sistem')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.users.index') }}"
           class="inline-flex items-center gap-2 text-xs sm:text-sm font-semibold text-slate-600 hover:text-slate-900 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            <span>Kembali ke Daftar Pengelola</span>
        </a>
    </div>

    @error('delete_error')
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs sm:text-sm flex items-center gap-3">
            <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            <span>{{ $message }}</span>
        </div>
    @enderror

    <!-- Card Edit Profil & Role -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-6">
        <div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900">Perbarui Profil & Hak Akses</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Ubah informasi nama, alamat surel, hak wewenang peranan, atau status keaktifan akun.
            </p>
        </div>

        <form action="{{ route('admin.users.update', $user) }}" method="POST" class="space-y-6" novalidate>
            @csrf
            @method('PUT')

            <!-- Nama Pengelola -->
            <div>
                <label for="name" class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-1.5">
                    Nama Lengkap Pengurus <span class="text-rose-500">*</span>
                </label>
                <input type="text"
                       id="name"
                       name="name"
                       value="{{ old('name', $user->name) }}"
                       required
                       maxlength="150"
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
                           value="{{ old('email', $user->email) }}"
                           required
                           maxlength="150"
                           class="w-full rounded-xl border border-slate-300 py-2.5 px-3.5 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-500 @error('email') border-rose-500 @enderror">
                    @error('email')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="role" class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-1.5">
                        Peranan / Role Wewenang <span class="text-rose-500">*</span>
                    </label>
                    @if(auth()->id() === $user->id)
                        <input type="hidden" name="role" value="{{ $user->role?->value ?? $user->role }}">
                        <input type="text"
                               disabled
                               value="{{ is_object($user->role) && method_exists($user->role, 'label') ? $user->role->label() : $user->role }} (Tidak dapat mengubah peran sendiri)"
                               class="w-full rounded-xl border border-slate-200 py-2.5 px-3.5 text-sm text-slate-500 bg-slate-50 cursor-not-allowed">
                    @else
                        @php
                            $currentRoleVal = $user->role?->value ?? (string)$user->role;
                        @endphp
                        <select id="role"
                                name="role"
                                required
                                class="w-full rounded-xl border border-slate-300 py-2.5 px-3.5 text-sm text-slate-900 bg-white focus:outline-none focus:ring-2 focus:ring-amber-500 @error('role') border-rose-500 @enderror">
                            @foreach($roles as $val => $lbl)
                                <option value="{{ $val }}" {{ old('role', $currentRoleVal) === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                            @endforeach
                        </select>
                        @error('role')
                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    @endif
                </div>
            </div>

            <!-- Status Aktif -->
            @if(auth()->id() !== $user->id)
                <div>
                    <label class="inline-flex items-center gap-3 cursor-pointer">
                        <input type="checkbox"
                               name="is_active"
                               value="1"
                               {{ old('is_active', $user->is_active) ? 'checked' : '' }}
                               class="w-4 h-4 text-blue-900 rounded border-slate-300 focus:ring-amber-500">
                        <div>
                            <span class="text-xs font-bold text-slate-800 block">Akun Aktif</span>
                            <span class="text-[11px] text-slate-500 block">Jika tidak dicentang, akun tidak akan dapat masuk ke dalam dashboard.</span>
                        </div>
                    </label>
                </div>
            @endif

            <!-- Tombol Aksi Simpan -->
            <div class="flex items-center justify-between pt-4 border-t border-slate-200">
                @if(auth()->id() !== $user->id)
                    <button type="button"
                            onclick="if(confirm('Apakah Anda yakin ingin menghapus akun pengelola \'{{ addslashes($user->name) }}\'?')) { document.getElementById('delete-user-form').submit(); }"
                            class="px-4 py-2.5 rounded-xl border border-rose-200 text-rose-600 hover:bg-rose-50 text-xs sm:text-sm font-semibold transition-colors">
                        Hapus Akun Pengelola
                    </button>
                @else
                    <div></div>
                @endif

                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.users.index') }}"
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

        @if(auth()->id() !== $user->id)
            <form id="delete-user-form" action="{{ route('admin.users.destroy', $user) }}" method="POST" class="hidden">
                @csrf
                @method('DELETE')
            </form>
        @endif
    </div>

    <!-- Card Reset Kata Sandi -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-6">
        <div>
            <h2 class="text-lg sm:text-xl font-extrabold text-slate-900">Perbarui Kata Sandi Akun</h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Tetapkan kata sandi baru untuk akun pengelola ini. Pastikan menggunakan kombinasi yang aman.
            </p>
        </div>

        <form action="{{ route('admin.users.reset_password', $user) }}" method="POST" class="space-y-5" novalidate>
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="new_password" class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-1.5">
                        Kata Sandi Baru <span class="text-rose-500">*</span>
                    </label>
                    <input type="password"
                           id="new_password"
                           name="new_password"
                           required
                           minlength="8"
                           placeholder="Minimal 8 karakter..."
                           class="w-full rounded-xl border border-slate-300 py-2.5 px-3.5 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-500 @error('new_password') border-rose-500 @enderror">
                    @error('new_password')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="new_password_confirmation" class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-1.5">
                        Konfirmasi Kata Sandi Baru <span class="text-rose-500">*</span>
                    </label>
                    <input type="password"
                           id="new_password_confirmation"
                           name="new_password_confirmation"
                           required
                           minlength="8"
                           placeholder="Ketik ulang kata sandi..."
                           class="w-full rounded-xl border border-slate-300 py-2.5 px-3.5 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-500">
                </div>
            </div>

            <div class="flex items-center justify-end pt-3">
                <button type="submit"
                        class="px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs sm:text-sm font-bold shadow-md transition-colors">
                    Reset & Simpan Kata Sandi
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
