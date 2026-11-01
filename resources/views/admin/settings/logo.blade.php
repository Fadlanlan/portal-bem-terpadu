@extends('layouts.admin')

@section('title', 'Pengelolaan Logo Website')
@section('header_title', 'Identitas Visual & Otomatisasi Logo Portal BEM')

@section('content')
<div class="space-y-8">
    <!-- Header Information & Breadcrumbs -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-200">
        <div>
            <div class="inline-flex items-center gap-2 text-xs text-slate-500 mb-1">
                <span>Pengaturan Sistem</span>
                <span>/</span>
                <span class="text-slate-900 font-semibold">Pengelolaan Logo Website</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Pengelolaan Logo Website</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Unggah lambang resmi BEM dengan segmentasi penghapusan background otomatis, perapian tepi, dan pemangkasan ruang kosong.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.settings.theme') }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold text-xs shadow-sm transition-all">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
                </svg>
                <span>Kustomisasi Tema Warna</span>
            </a>
        </div>
    </div>

    <!-- Alert Notifications -->
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center gap-3">
            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('info'))
        <div class="p-4 rounded-2xl bg-blue-50 border border-blue-200 text-blue-800 text-sm flex items-center gap-3">
            <svg class="w-5 h-5 text-blue-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
            </svg>
            <span>{{ session('info') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-sm space-y-1">
            <p class="font-bold">Terjadi kesalahan pada berkas logo:</p>
            <ul class="list-disc list-inside text-xs">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- ========================================================================= -->
    <!-- PANEL PRATINJAU HASIL PEMROSESAN (SEBELUM DITERAPKAN)                      -->
    <!-- Tampil hanya jika ada sesi pratinjau yang siap ditinjau Super Admin       -->
    <!-- ========================================================================= -->
    @if(!empty($preview) && !empty($preview['token']))
        <div class="bg-gradient-to-br from-slate-900 to-blue-950 rounded-3xl p-6 sm:p-8 border border-amber-500/40 text-white shadow-2xl space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-800 pb-4">
                <div class="space-y-1">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-500/20 text-amber-300 font-bold text-xs border border-amber-500/30">
                        <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                        Menunggu Persetujuan Super Admin
                    </span>
                    <h2 class="text-xl sm:text-2xl font-black tracking-tight text-white mt-1">
                        Hasil Pemrosesan Otomatis Siap Ditinjau
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-300">
                        Periksa hasil penghapusan background dan perapian tepi pada berbagai latar sebelum menerapkannya secara permanen ke website.
                    </p>
                </div>

                <div class="flex items-center gap-2.5 shrink-0">
                    <!-- Form Batalkan -->
                    <form action="{{ route('admin.settings.logo.cancel') }}" method="POST">
                        @csrf
                        <button type="submit"
                                class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white font-bold text-xs border border-slate-700 transition-colors">
                            Batalkan
                        </button>
                    </form>

                    <!-- Form Terapkan -->
                    <form action="{{ route('admin.settings.logo.apply') }}" method="POST">
                        @csrf
                        <button type="submit"
                                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-black text-xs shadow-lg shadow-emerald-500/20 transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>Terapkan Sebagai Logo Resmi</span>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Kartu Metrik & Statistik Pemrosesan -->
            @php $meta = $preview['metadata'] ?? []; @endphp
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                <div class="bg-slate-800/80 p-3.5 rounded-2xl border border-slate-700/60">
                    <span class="text-slate-400 block text-[11px]">Dimensi Gambar</span>
                    <span class="font-extrabold text-white text-sm block font-mono">
                        {{ $meta['original_width'] ?? '-' }}x{{ $meta['original_height'] ?? '-' }} &rarr; {{ $meta['trimmed_width'] ?? '-' }}x{{ $meta['trimmed_height'] ?? '-' }} px
                    </span>
                    <span class="text-emerald-400 text-[10px] block mt-0.5">Proporsi aspek terjaga</span>
                </div>

                <div class="bg-slate-800/80 p-3.5 rounded-2xl border border-slate-700/60">
                    <span class="text-slate-400 block text-[11px]">Ruang Kosong Dirapikan</span>
                    <span class="font-extrabold text-amber-400 text-sm block font-mono">
                        {{ $meta['whitespace_trimmed_percent'] ?? 0 }}%
                    </span>
                    <span class="text-slate-400 text-[10px] block mt-0.5">Piksel kosong dipangkas</span>
                </div>

                <div class="bg-slate-800/80 p-3.5 rounded-2xl border border-slate-700/60">
                    <span class="text-slate-400 block text-[11px]">Warna Background Terdeteksi</span>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="w-4 h-4 rounded-full border border-white/20 shrink-0" style="background-color: {{ $meta['bg_color'] ?? '#FFFFFF' }}"></span>
                        <span class="font-extrabold text-white text-xs font-mono">{{ $meta['bg_color'] ?? '#FFFFFF' }}</span>
                    </div>
                    <span class="text-slate-400 text-[10px] block mt-0.5">Dihapus secara contiguous</span>
                </div>

                <div class="bg-slate-800/80 p-3.5 rounded-2xl border border-slate-700/60">
                    <span class="text-slate-400 block text-[11px]">Ukuran Berkas Output</span>
                    <span class="font-extrabold text-white text-sm block font-mono">
                        {{ round(($meta['file_size'] ?? 0) / 1024, 1) }} KB
                    </span>
                    <span class="text-slate-400 text-[10px] block mt-0.5">Format PNG 32-bit Alpha</span>
                </div>
            </div>

            <!-- Uji Pratinjau Multi-Latar (Side-by-Side) -->
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-300 block mb-3">
                    Uji Tampilan Transparansi Multi-Latar:
                </span>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <!-- 1. Gambar Asli Sebelum Olah -->
                    <div class="bg-slate-800/60 rounded-2xl p-4 border border-slate-700 text-center space-y-2">
                        <span class="text-[11px] font-bold text-slate-400 block uppercase">1. Berkas Asli (Input)</span>
                        <div class="h-32 rounded-xl bg-slate-900 flex items-center justify-center p-2 overflow-hidden">
                            <img src="{{ route('admin.settings.logo.temp_original', $preview['token']) }}"
                                 alt="Berkas Asli"
                                 class="max-h-28 max-w-full object-contain">
                        </div>
                        <span class="text-[10px] text-slate-400 block truncate">{{ $preview['original_filename'] ?? 'Gambar Asli' }}</span>
                    </div>

                    <!-- 2. Hasil Pada Latar Terang (White) -->
                    <div class="bg-slate-800/60 rounded-2xl p-4 border border-slate-700 text-center space-y-2">
                        <span class="text-[11px] font-bold text-emerald-400 block uppercase">2. Uji Latar Terang</span>
                        <div class="h-32 rounded-xl bg-white border border-slate-200 flex items-center justify-center p-2 overflow-hidden shadow-inner">
                            <img src="{{ route('admin.settings.logo.temp_preview', $preview['token']) }}"
                                 alt="Hasil Pada Latar Terang"
                                 class="max-h-28 max-w-full object-contain">
                        </div>
                        <span class="text-[10px] text-slate-300 block">Simulasi header / kertas terang</span>
                    </div>

                    <!-- 3. Hasil Pada Latar Gelap (Navy / Dark) -->
                    <div class="bg-slate-800/60 rounded-2xl p-4 border border-slate-700 text-center space-y-2">
                        <span class="text-[11px] font-bold text-amber-400 block uppercase">3. Uji Latar Gelap</span>
                        <div class="h-32 rounded-xl bg-slate-950 border border-slate-800 flex items-center justify-center p-2 overflow-hidden shadow-inner">
                            <img src="{{ route('admin.settings.logo.temp_preview', $preview['token']) }}"
                                 alt="Hasil Pada Latar Gelap"
                                 class="max-h-28 max-w-full object-contain">
                        </div>
                        <span class="text-[10px] text-slate-300 block">Simulasi navbar & footer gelap</span>
                    </div>

                    <!-- 4. Hasil Pada Pola Catur Transparansi (Checkerboard) -->
                    <div class="bg-slate-800/60 rounded-2xl p-4 border border-slate-700 text-center space-y-2">
                        <span class="text-[11px] font-bold text-purple-400 block uppercase">4. Kanal Alpha Transparan</span>
                        <div class="h-32 rounded-xl flex items-center justify-center p-2 overflow-hidden shadow-inner"
                             style="background-image: repeating-conic-gradient(#cbd5e1 0% 25%, #f8fafc 0% 50%); background-size: 16px 16px;">
                            <img src="{{ route('admin.settings.logo.temp_preview', $preview['token']) }}"
                                 alt="Hasil Transparan"
                                 class="max-h-28 max-w-full object-contain">
                        </div>
                        <span class="text-[10px] text-slate-300 block">Kanal alpha transparan terverifikasi</span>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        <!-- Panel Pratinjau Logo Aktif Saat Ini (5 Kolom) -->
        <div class="lg:col-span-5 bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm space-y-6">
            <div>
                <h2 class="text-sm font-bold uppercase tracking-wider text-slate-800">Logo Website Aktif Saat Ini</h2>
                <p class="text-xs text-slate-500 mt-1">Logo resmi yang sedang digunakan langsung di navbar, footer, dan publikasi portal.</p>
            </div>

            <!-- Box Tampilan Logo Aktif -->
            <div class="p-8 rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50 flex flex-col items-center justify-center text-center space-y-4 min-h-[220px]">
                @if($logoUrl)
                    <div class="relative group p-4 rounded-xl bg-slate-900 border border-slate-800 shadow-md">
                        <img src="{{ $logoUrl }}"
                             alt="Logo Portal BEM"
                             class="max-h-28 max-w-[200px] object-contain drop-shadow-sm transition-transform group-hover:scale-105">
                    </div>
                    <div>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 font-bold text-xs">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            Logo Kustom Aktif
                        </span>
                        <span class="block text-[11px] font-mono text-slate-400 mt-1">{{ $rawLogoPath }}</span>
                    </div>
                @else
                    <div class="w-20 h-20 rounded-2xl bg-gradient-to-tr from-blue-900 to-amber-500 flex items-center justify-center text-white font-black text-2xl shadow-md">
                        BEM
                    </div>
                    <div>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-200 text-slate-700 font-semibold text-xs">
                            Logo Bawaan Standar (Inisial BEM)
                        </span>
                        <p class="text-[11px] text-slate-400 mt-1">Belum ada logo kustom yang diunggah.</p>
                    </div>
                @endif
            </div>

            <!-- Aksi Hapus Logo Kustom jika ada -->
            @if($rawLogoPath)
                <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                    <div>
                        <span class="block text-xs font-bold text-slate-700">Hapus Logo Kustom</span>
                        <span class="block text-[11px] text-slate-400">Kembalikan ke lambang inisial standar.</span>
                    </div>

                    <form action="{{ route('admin.settings.logo.destroy') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus logo ini dan mengembalikannya ke logo standar?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-3.5 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold border border-rose-200 transition-colors">
                            Hapus Logo
                        </button>
                    </form>
                </div>
            @endif

            <!-- Informasi Integritas & Keamanan -->
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-xs text-slate-600 space-y-2">
                <span class="font-bold text-slate-800 block">Jaminan Integritas File:</span>
                <ul class="list-disc list-inside space-y-1 text-[11px] text-slate-500">
                    <li>Logo aktif tetap terlindungi dan <strong>tidak akan diganti</strong> sebelum Anda menyetujui hasil pratinjau.</li>
                    <li>Detail warna internal lambang (seperti tulisan atau bintang putih) <strong>tidak akan terhapus</strong> secara membabi buta.</li>
                    <li>Proporsi aspek horizontal maupun vertikal dipertahankan tanpa distorsi.</li>
                </ul>
            </div>
        </div>

        <!-- Formulir Unggah Logo Baru (7 Kolom) -->
        <div class="lg:col-span-7 bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm space-y-6">
            <div>
                <h2 class="text-lg font-bold text-slate-900">Unggah Logo Baru</h2>
                <p class="text-xs text-slate-500 mt-1">
                    Sistem akan mendeteksi background, menghapusnya menjadi transparan, merapikan tepi, dan memangkas ruang kosong di sekeliling lambang.
                </p>
            </div>

            <form action="{{ route('admin.settings.logo.process') }}" method="POST" enctype="multipart/form-data" id="logoUploadForm" class="space-y-6">
                @csrf

                <!-- File Input Area -->
                <div class="space-y-3">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide">Pilih Berkas Logo</label>

                    <div class="border-2 border-dashed border-slate-300 hover:border-blue-500 rounded-2xl p-6 text-center cursor-pointer transition-colors bg-slate-50/50 hover:bg-white"
                         onclick="document.getElementById('logo_input').click()">
                        <input type="file"
                               name="logo"
                               id="logo_input"
                               accept="image/png,image/jpeg,image/jpg,image/webp"
                               class="hidden"
                               required
                               onchange="handleFileSelection(this)">

                        <div id="dropzone_content" class="space-y-2">
                            <svg class="w-10 h-10 text-slate-400 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <p class="text-xs font-semibold text-slate-700">
                                Klik untuk memilih berkas atau seret gambar ke sini
                            </p>
                            <p class="text-[11px] text-slate-400">
                                Format: PNG, JPG, JPEG, WebP | Maksimal: 2MB | Resolusi: 16x16 hingga 4000x4000 px
                            </p>
                        </div>

                        <!-- Client-Side Instant Preview Card -->
                        <div id="new_preview_container" class="hidden space-y-3">
                            <div class="max-h-32 max-w-[220px] mx-auto p-2 rounded-xl bg-slate-100 border border-slate-200 flex items-center justify-center">
                                <img id="new_logo_preview" src="#" alt="Pratinjau Berkas Terpilih" class="max-h-28 max-w-full object-contain">
                            </div>
                            <span id="file_name_label" class="inline-block text-xs font-mono text-slate-700 bg-slate-100 px-3 py-1 rounded-lg"></span>
                        </div>
                    </div>
                </div>

                <!-- Opsi Konfigurasi Pemrosesan (Collapsible / Advanced) -->
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-800 uppercase tracking-wide">Pengaturan Optimasi Otomatis</span>
                        <span class="text-[11px] text-slate-400">Konfigurasi algoritma segmentasi</span>
                    </div>

                    <!-- Toggle Hapus Background -->
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox"
                               name="remove_bg"
                               value="1"
                               checked
                               class="w-4 h-4 rounded text-blue-900 focus:ring-blue-500 border-slate-300">
                        <span class="text-xs font-semibold text-slate-700">
                            Hapus background otomatis (Boundary-Connected Flood Fill)
                        </span>
                    </label>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2 border-t border-slate-200/60 text-xs">
                        <!-- Toleransi Warna -->
                        <div class="space-y-1">
                            <div class="flex items-center justify-between">
                                <label for="tolerance" class="font-bold text-slate-700 text-[11px]">Toleransi Warna</label>
                                <span id="tolerance_val" class="font-mono text-slate-500 text-[11px]">28</span>
                            </div>
                            <input type="range"
                                   name="tolerance"
                                   id="tolerance"
                                   min="5"
                                   max="80"
                                   value="28"
                                   oninput="document.getElementById('tolerance_val').textContent = this.value"
                                   class="w-full h-1.5 bg-slate-200 rounded-lg appearance-none cursor-pointer">
                            <span class="text-[10px] text-slate-400 block">Jarak Euclidean RGB</span>
                        </div>

                        <!-- Padding Transparan -->
                        <div class="space-y-1">
                            <div class="flex items-center justify-between">
                                <label for="padding" class="font-bold text-slate-700 text-[11px]">Padding Transparan</label>
                                <span id="padding_val" class="font-mono text-slate-500 text-[11px]">16 px</span>
                            </div>
                            <input type="range"
                                   name="padding"
                                   id="padding"
                                   min="0"
                                   max="48"
                                   value="16"
                                   oninput="document.getElementById('padding_val').textContent = this.value + ' px'"
                                   class="w-full h-1.5 bg-slate-200 rounded-lg appearance-none cursor-pointer">
                            <span class="text-[10px] text-slate-400 block">Bantalan tepi lambang</span>
                        </div>

                        <!-- Feathering (Kehalusan Tepi) -->
                        <div class="space-y-1">
                            <div class="flex items-center justify-between">
                                <label for="feather" class="font-bold text-slate-700 text-[11px]">Kehalusan Tepi</label>
                                <span id="feather_val" class="font-mono text-slate-500 text-[11px]">2</span>
                            </div>
                            <input type="range"
                                   name="feather"
                                   id="feather"
                                   min="0"
                                   max="4"
                                   value="2"
                                   oninput="document.getElementById('feather_val').textContent = this.value"
                                   class="w-full h-1.5 bg-slate-200 rounded-lg appearance-none cursor-pointer">
                            <span class="text-[10px] text-slate-400 block">Anti-aliasing blending</span>
                        </div>
                    </div>
                </div>

                <!-- Tombol Aksi Formulir -->
                <div class="pt-4 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-end gap-3">
                    <button type="submit"
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-blue-900 hover:bg-blue-800 text-white font-bold text-xs sm:text-sm shadow-lg shadow-blue-950/20 transition-all">
                        <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                        </svg>
                        <span>Proses & Lihat Pratinjau Server</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function handleFileSelection(input) {
        if (input.files && input.files[0]) {
            const file = input.files[0];
            const reader = new FileReader();

            reader.onload = function(e) {
                document.getElementById('new_logo_preview').src = e.target.result;
                document.getElementById('file_name_label').textContent = file.name + ' (' + (file.size / 1024).toFixed(1) + ' KB)';
                document.getElementById('dropzone_content').classList.add('hidden');
                document.getElementById('new_preview_container').classList.remove('hidden');
            };

            reader.readAsDataURL(file);
        }
    }
</script>
@endsection
