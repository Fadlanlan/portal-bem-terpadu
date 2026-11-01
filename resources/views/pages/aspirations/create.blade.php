@extends('layouts.app')

@section('title', 'Kirim Aspirasi Mahasiswa')
@section('meta_description', 'Formulir penyampaian aspirasi, kritik, saran, dan pengaduan mahasiswa terpadu dengan nomor tiket pelacakan.')

@section('content')
    <div class="text-white py-12 border-b" style="background-color: var(--theme-primary); border-bottom-color: var(--theme-secondary);">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-breadcrumb :items="['Kanal Aspirasi' => route('aspirations.info'), 'Kirim Aspirasi' => null]" />
            <div class="mt-4">
                <span class="text-xs font-bold uppercase tracking-wider" style="color: var(--theme-accent);">Formulir Partisipasi</span>
                <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight mt-1">Sampaikan Aspirasi Mahasiswa</h1>
                <p class="text-sm sm:text-base text-slate-300 mt-2">
                    Suara Anda bermakna bagi kemajuan almamater. Setiap aspirasi yang dikirimkan akan menghasilkan nomor tiket pelacakan dan kode rahasia satu kali.
                </p>
            </div>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-10">
            <!-- Alert Global Errors -->
            @if($errors->any())
                <div class="mb-8 p-4 sm:p-5 rounded-xl bg-rose-50 border border-rose-200 text-rose-900" role="alert">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-rose-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        <div class="space-y-1">
                            <strong class="font-bold block text-sm">Mohon periksa kembali isian formulir Anda:</strong>
                            <ul class="list-disc list-inside text-xs sm:text-sm space-y-0.5 text-rose-800">
                                @foreach($errors->all() as $err)
                                    <li>{{ $err }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <form action="{{ route('aspirations.store') }}" method="POST" class="space-y-6" novalidate>
                @csrf

                <!-- 1. Kategori Aspirasi -->
                <div>
                    <label for="category" class="block text-sm font-bold text-slate-900 mb-1.5">
                        Kategori Laporan / Usulan <span class="text-rose-500">*</span>
                    </label>
                    <select id="category"
                            name="category"
                            required
                            class="w-full rounded-xl border border-slate-300 py-3 px-4 text-sm text-slate-900 bg-white focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 @error('category') border-rose-500 ring-1 ring-rose-500 @enderror">
                        <option value="">-- Pilih Kategori yang Sesuai --</option>
                        @foreach($categories as $value => $label)
                            <option value="{{ $value }}" {{ old('category') === $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('category')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- 2. Subjek / Judul -->
                <div>
                    <label for="subject" class="block text-sm font-bold text-slate-900 mb-1.5">
                        Judul Ringkas Aspirasi <span class="text-rose-500">*</span>
                    </label>
                    <input type="text"
                           id="subject"
                           name="subject"
                           value="{{ old('subject') }}"
                           required
                           maxlength="150"
                           placeholder="Contoh: Usulan Perbaikan Pendingin Ruang Baca Perpustakaan Pusat"
                           class="w-full rounded-xl border border-slate-300 py-3 px-4 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 @error('subject') border-rose-500 ring-1 ring-rose-500 @enderror">
                    <p class="mt-1 text-xs text-slate-500 flex justify-between">
                        <span>Buat judul yang spesifik dan jelas (5 - 150 karakter).</span>
                    </p>
                    @error('subject')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- 3. Uraian Isi Aspirasi -->
                <div>
                    <label for="message" class="block text-sm font-bold text-slate-900 mb-1.5">
                        Uraian Lengkap Aspirasi / Pengaduan <span class="text-rose-500">*</span>
                    </label>
                    <textarea id="message"
                              name="message"
                              rows="6"
                              required
                              placeholder="Jelaskan secara kronologis, santun, dan objektif: fakta permasalahan, lokasi kejadian, dampak yang dirasakan, serta saran perbaikan yang diharapkan..."
                              class="w-full rounded-xl border border-slate-300 py-3 px-4 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 @error('message') border-rose-500 ring-1 ring-rose-500 @enderror">{{ old('message') }}</textarea>
                    <p class="mt-1 text-xs text-slate-500">
                        Minimal 15 karakter, maksimal 5000 karakter.
                    </p>
                    @error('message')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- 4. Pilihan Privasi & Identitas -->
                <div class="pt-4 border-t border-slate-200">
                    <h2 class="text-base font-bold text-slate-900 mb-3">Pengaturan Privasi Pelapor</h2>

                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 space-y-4">
                        <label class="flex items-start gap-3 cursor-pointer">
                            <input type="checkbox"
                                   id="is_anonymous"
                                   name="is_anonymous"
                                   value="1"
                                   {{ old('is_anonymous') ? 'checked' : '' }}
                                   class="mt-1 w-4 h-4 text-amber-600 rounded border-slate-300 focus:ring-amber-500">
                            <div>
                                <span class="text-sm font-bold text-slate-900 block">Kirim Sebagai Anonim (Rahasiakan Identitas)</span>
                                <span class="text-xs text-slate-600 block mt-0.5">
                                    Jika dicentang, sistem sama sekali <strong>tidak menyimpan nama, email, atau NIM Anda</strong> di basis data server. Tim advokasi hanya akan memproses fakta isi laporan.
                                </span>
                            </div>
                        </label>

                        <!-- Identity Fields Container -->
                        <div id="identity-fields" class="pt-4 border-t border-slate-200 space-y-4 {{ old('is_anonymous') ? 'hidden' : '' }}">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label for="reporter_name" class="block text-xs font-bold text-slate-700 mb-1">
                                        Nama Lengkap <span class="text-rose-500" id="name-required-star">*</span>
                                    </label>
                                    <input type="text"
                                           id="reporter_name"
                                           name="reporter_name"
                                           value="{{ old('reporter_name') }}"
                                           placeholder="Nama Anda"
                                           class="w-full rounded-xl border border-slate-300 py-2.5 px-3.5 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-500 @error('reporter_name') border-rose-500 ring-1 ring-rose-500 @enderror">
                                    @error('reporter_name')
                                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="reporter_email" class="block text-xs font-bold text-slate-700 mb-1">
                                        Alamat Surel / Email <span class="text-rose-500" id="email-required-star">*</span>
                                    </label>
                                    <input type="email"
                                           id="reporter_email"
                                           name="reporter_email"
                                           value="{{ old('reporter_email') }}"
                                           placeholder="email@kampus.ac.id"
                                           class="w-full rounded-xl border border-slate-300 py-2.5 px-3.5 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-500 @error('reporter_email') border-rose-500 ring-1 ring-rose-500 @enderror">
                                    <span class="text-[11px] text-slate-500">Digunakan untuk klarifikasi berkas bila diperlukan.</span>
                                    @error('reporter_email')
                                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div>
                                <label for="reporter_student_id" class="block text-xs font-bold text-slate-700 mb-1">
                                    NIM / Nomor Mahasiswa <span class="text-slate-400 font-normal">(Opsional)</span>
                                </label>
                                <input type="text"
                                       id="reporter_student_id"
                                       name="reporter_student_id"
                                       value="{{ old('reporter_student_id') }}"
                                       placeholder="Nomor Induk Mahasiswa Anda"
                                       class="w-full sm:w-1/2 rounded-xl border border-slate-300 py-2.5 px-3.5 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-500">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 5. Persetujuan Privasi -->
                <div class="pt-4 border-t border-slate-200">
                    <label class="flex items-start gap-3 cursor-pointer">
                        <input type="checkbox"
                               name="privacy_consent"
                               value="1"
                               {{ old('privacy_consent') ? 'checked' : '' }}
                               required
                               class="mt-1 w-4 h-4 text-amber-600 rounded border-slate-300 focus:ring-amber-500">
                        <span class="text-xs sm:text-sm text-slate-700 leading-relaxed">
                            Saya menyatakan bahwa aspirasi yang saya sampaikan adalah benar, tidak mengandung unsur fitnah atau ujaran kebencian, dan saya memahami bahwa sistem menerbitkan <strong>Nomor Tiket</strong> serta <strong>Kode Pelacakan Rahasia</strong> yang wajib saya simpan sendiri untuk memantau status tindak lanjut. <span class="text-rose-500 font-bold">*</span>
                        </span>
                    </label>
                    @error('privacy_consent')
                        <p class="mt-1.5 text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Action Submit -->
                <div class="pt-4 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <button type="submit"
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-3.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-sm shadow-md transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                        </svg>
                        <span>Kirim Aspirasi Sekarang</span>
                    </button>

                    <a href="{{ route('aspirations.tracking.form') }}"
                       class="text-xs sm:text-sm font-semibold text-slate-600 hover:text-amber-700 transition-colors">
                        Sudah punya tiket? Lacak status di sini &rarr;
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const anonymousCheckbox = document.getElementById('is_anonymous');
        const identityFields = document.getElementById('identity-fields');
        const nameInput = document.getElementById('reporter_name');
        const emailInput = document.getElementById('reporter_email');
        const nameStar = document.getElementById('name-required-star');
        const emailStar = document.getElementById('email-required-star');

        function updateIdentityVisibility() {
            if (anonymousCheckbox.checked) {
                identityFields.classList.add('hidden');
                if (nameStar) nameStar.classList.add('hidden');
                if (emailStar) emailStar.classList.add('hidden');
            } else {
                identityFields.classList.remove('hidden');
                if (nameStar) nameStar.classList.remove('hidden');
                if (emailStar) emailStar.classList.remove('hidden');
            }
        }

        if (anonymousCheckbox && identityFields) {
            anonymousCheckbox.addEventListener('change', updateIdentityVisibility);
            updateIdentityVisibility();
        }
    });
</script>
@endpush
