@extends('layouts.admin')

@section('title', 'Kustomisasi Tema Website')
@section('header_title', 'Pengaturan Tema Visual & Skema Warna')

@section('content')
<div class="space-y-8">
    <!-- Breadcrumb & Header Information -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-200">
        <div>
            <div class="inline-flex items-center gap-2 text-xs text-slate-500 mb-1">
                <span>Pengaturan Sistem</span>
                <span>/</span>
                <span class="text-slate-900 font-semibold">Tema Visual</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Kustomisasi Tema & Warna Portal</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Sesuaikan skema warna identitas visual BEM untuk antarmuka publik secara fleksibel dan langsung.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.settings.logo') }}"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold text-xs shadow-sm transition-all">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <span>Kelola Logo Website</span>
            </a>

            <form action="{{ route('admin.settings.theme.reset') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin mengembalikan semua warna tema ke pengaturan standar bawaan?');">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs border border-rose-200 shadow-sm transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    <span>Reset ke Tema Standar</span>
                </button>
            </form>
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

    @if($errors->any())
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-sm space-y-1">
            <p class="font-bold">Terjadi kesalahan validasi warna:</p>
            <ul class="list-disc list-inside text-xs">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Preset Tema Siap Pakai -->
    <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm space-y-4">
        <div>
            <h2 class="text-sm font-bold uppercase tracking-wider text-slate-800">Pilihan Skema Warna Preset</h2>
            <p class="text-xs text-slate-500">Pilih salah satu preset di bawah untuk menerapkan kombinasi warna selaras secara otomatis.</p>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
            @foreach($presets as $key => $preset)
                <button type="button"
                        onclick="applyPreset('{{ $key }}')"
                        class="p-3.5 rounded-2xl border border-slate-200 hover:border-blue-500 hover:shadow-md transition-all text-left group bg-slate-50/50 hover:bg-white">
                    <div class="flex items-center gap-1.5 mb-2">
                        <span class="w-4 h-4 rounded-full border border-black/10 shadow-xs" style="background-color: {{ $preset['colors']['primary_color'] }}"></span>
                        <span class="w-4 h-4 rounded-full border border-black/10 shadow-xs" style="background-color: {{ $preset['colors']['secondary_color'] }}"></span>
                        <span class="w-4 h-4 rounded-full border border-black/10 shadow-xs" style="background-color: {{ $preset['colors']['accent_color'] }}"></span>
                        <span class="w-4 h-4 rounded-full border border-black/10 shadow-xs" style="background-color: {{ $preset['colors']['background_color'] }}"></span>
                    </div>
                    <span class="block text-xs font-bold text-slate-900 group-hover:text-blue-600 truncate">{{ $preset['name'] }}</span>
                    <span class="block text-[11px] text-slate-400 truncate">{{ $preset['colors']['primary_color'] }}</span>
                </button>
            @endforeach
        </div>
    </div>

    <!-- Main Customization Form & Live Preview Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        <!-- Kolom Form Pemilihan Warna (7 Kolom) -->
        <div class="lg:col-span-7 bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm space-y-6">
            <div class="border-b border-slate-100 pb-4">
                <h2 class="text-lg font-bold text-slate-900">Konfigurasi Variabel Warna HEX</h2>
                <p class="text-xs text-slate-500 mt-1">Masukkan format kode warna HEX 6 digit (contoh: #0F172A) atau klik tombol palet untuk memilih.</p>
            </div>

            <form action="{{ route('admin.settings.theme.update') }}" method="POST" id="theme-form" class="space-y-5">
                @csrf
                @method('PUT')

                @php
                    $colorFields = [
                        'primary_color' => ['label' => 'Warna Utama (Primary)', 'desc' => 'Digunakan pada header, identitas utama, dan elemen prioritas.'],
                        'secondary_color' => ['label' => 'Warna Sekunder (Secondary)', 'desc' => 'Digunakan pada subheader, aksen pendukung, dan tombol netral.'],
                        'accent_color' => ['label' => 'Warna Aksen (Accent)', 'desc' => 'Digunakan untuk highlight, badge penting, dan interaksi sorotan.'],
                        'background_color' => ['label' => 'Warna Latar Belakang (Background)', 'desc' => 'Latar belakang keseluruhan kanvas halaman portal publik.'],
                        'surface_color' => ['label' => 'Warna Permukaan (Surface)', 'desc' => 'Warna kartu kontainer konten, navbar, dan panel utama.'],
                        'text_primary_color' => ['label' => 'Warna Teks Utama', 'desc' => 'Teks judul, heading, dan paragraf paling kontras.'],
                        'text_secondary_color' => ['label' => 'Warna Teks Sekunder', 'desc' => 'Teks keterangan tambahan, subjudul, dan metadata.'],
                    ];
                @endphp

                <div class="space-y-4">
                    @foreach($colorFields as $name => $meta)
                        <div class="p-3.5 rounded-2xl bg-slate-50/70 border border-slate-200/70 hover:bg-white transition-all space-y-2">
                            <div class="flex items-center justify-between">
                                <label for="input_{{ $name }}" class="text-xs font-bold text-slate-800">
                                    {{ $meta['label'] }}
                                </label>
                                <span class="text-[11px] font-mono text-slate-400">--{{ str_replace('_', '-', $name) }}</span>
                            </div>

                            <div class="flex items-center gap-3">
                                <!-- Color Picker Visual -->
                                <input type="color"
                                       id="picker_{{ $name }}"
                                       value="{{ old($name, $theme[$name] ?? '#0F172A') }}"
                                       oninput="syncFromPicker('{{ $name }}', this.value)"
                                       class="w-11 h-11 rounded-xl cursor-pointer border border-slate-300 p-1 bg-white shrink-0">

                                <!-- Text HEX Input -->
                                <div class="relative flex-1">
                                    <input type="text"
                                           name="{{ $name }}"
                                           id="input_{{ $name }}"
                                           value="{{ old($name, $theme[$name] ?? '#0F172A') }}"
                                           maxlength="7"
                                           placeholder="#000000"
                                           oninput="syncFromText('{{ $name }}', this.value)"
                                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 font-mono text-xs uppercase font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                </div>
                            </div>
                            <p class="text-[11px] text-slate-500 leading-tight">{{ $meta['desc'] }}</p>
                        </div>
                    @endforeach
                </div>

                <div class="pt-4 border-t border-slate-200 flex items-center justify-end gap-3">
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-blue-900 hover:bg-blue-800 text-white font-bold text-xs sm:text-sm shadow-lg shadow-blue-950/20 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span>Simpan Perubahan Tema</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Kolom Pratinjau Interaktif Langsung (5 Kolom) -->
        <div class="lg:col-span-5 space-y-4 sticky top-6">
            <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm space-y-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-sm font-bold uppercase tracking-wider text-slate-800">Pratinjau Langsung (Live Preview)</h2>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-blue-50 text-blue-700 text-[11px] font-bold">
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-600 animate-pulse"></span>
                        Interaktif
                    </span>
                </div>
                <p class="text-xs text-slate-500">Pratinjau ini bereaksi langsung terhadap setiap perubahan warna di panel kiri secara real-time.</p>

                <!-- Mockup Container Box -->
                <div id="preview-container" class="rounded-2xl p-5 border border-slate-200 transition-colors shadow-inner space-y-4">
                    <!-- Mockup Header Navbar -->
                    <div id="preview-navbar" class="rounded-xl p-3 border shadow-xs flex items-center justify-between transition-colors">
                        <div class="flex items-center gap-2">
                            <div id="preview-logo-badge" class="w-7 h-7 rounded-lg flex items-center justify-center font-black text-xs text-white">
                                B
                            </div>
                            <span id="preview-brand-title" class="font-extrabold text-xs">PORTAL BEM</span>
                        </div>
                        <div class="flex items-center gap-2 text-[10px] font-semibold">
                            <span class="px-2 py-1 rounded-md opacity-80">Berita</span>
                            <span class="px-2 py-1 rounded-md opacity-80">Ormawa</span>
                            <span id="preview-nav-cta" class="px-2.5 py-1 rounded-md font-bold text-white shadow-xs">Aspirasi</span>
                        </div>
                    </div>

                    <!-- Mockup Hero Banner -->
                    <div id="preview-hero" class="rounded-xl p-4 border shadow-xs space-y-2.5 transition-colors">
                        <div class="inline-block px-2 py-0.5 rounded-full text-[10px] font-bold" id="preview-badge">
                            KABINET EKSEKUTIF 2025/2026
                        </div>
                        <h4 id="preview-heading" class="text-base font-black leading-tight">
                            Portal Resmi Badan Eksekutif Mahasiswa
                        </h4>
                        <p id="preview-text" class="text-xs leading-relaxed">
                            Pusat keterbukaan informasi, warta kampus, dan kanal aspirasi mahasiswa terintegrasi perguruan tinggi.
                        </p>

                        <div class="pt-2 flex items-center gap-2">
                            <button type="button" id="preview-btn-primary" class="px-3 py-1.5 rounded-lg text-xs font-bold text-white shadow-xs">
                                Tombol Utama
                            </button>
                            <button type="button" id="preview-btn-secondary" class="px-3 py-1.5 rounded-lg text-xs font-bold border">
                                Tombol Sekunder
                            </button>
                        </div>
                    </div>

                    <!-- Mockup Card Container -->
                    <div id="preview-card" class="rounded-xl p-3.5 border shadow-xs space-y-1.5 transition-colors">
                        <div class="flex items-center justify-between">
                            <span id="preview-card-tag" class="text-[10px] font-bold uppercase tracking-wider">Kategori Program</span>
                            <span class="text-[10px] opacity-60">Baru Saja</span>
                        </div>
                        <h5 id="preview-card-title" class="font-bold text-xs">Advokasi Bantuan Uang Kuliah Tunggal</h5>
                        <p id="preview-card-body" class="text-[11px] leading-relaxed">
                            Simulasi kartu artikel dan program kerja BEM dengan variabel warna yang Anda atur.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Script Sinkronisasi Warna & Preset JavaScript -->
<script>
    const presetsData = @json($presets);

    function syncFromPicker(name, hexValue) {
        const textInput = document.getElementById('input_' + name);
        if (textInput) {
            textInput.value = hexValue.toUpperCase();
        }
        updateLivePreview();
    }

    function syncFromText(name, hexValue) {
        if (/^#[0-9A-Fa-f]{6}$/.test(hexValue)) {
            const picker = document.getElementById('picker_' + name);
            if (picker) {
                picker.value = hexValue;
            }
            updateLivePreview();
        }
    }

    function applyPreset(presetKey) {
        const preset = presetsData[presetKey];
        if (!preset || !preset.colors) return;

        for (const [key, color] of Object.entries(preset.colors)) {
            const textInput = document.getElementById('input_' + key);
            const picker = document.getElementById('picker_' + key);
            if (textInput) textInput.value = color.toUpperCase();
            if (picker) picker.value = color;
        }

        updateLivePreview();
    }

    function updateLivePreview() {
        const primary = document.getElementById('input_primary_color')?.value || '#0F172A';
        const secondary = document.getElementById('input_secondary_color')?.value || '#1E293B';
        const accent = document.getElementById('input_accent_color')?.value || '#F59E0B';
        const bg = document.getElementById('input_background_color')?.value || '#F8FAFC';
        const surface = document.getElementById('input_surface_color')?.value || '#FFFFFF';
        const textPrimary = document.getElementById('input_text_primary_color')?.value || '#0F172A';
        const textSecondary = document.getElementById('input_text_secondary_color')?.value || '#475569';

        // Update container
        const container = document.getElementById('preview-container');
        if (container) {
            container.style.backgroundColor = bg;
        }

        // Update navbar
        const navbar = document.getElementById('preview-navbar');
        if (navbar) {
            navbar.style.backgroundColor = surface;
            navbar.style.color = textPrimary;
            navbar.style.borderColor = primary + '20';
        }

        const logoBadge = document.getElementById('preview-logo-badge');
        if (logoBadge) logoBadge.style.backgroundColor = primary;

        const navCta = document.getElementById('preview-nav-cta');
        if (navCta) navCta.style.backgroundColor = accent;

        // Update hero banner
        const hero = document.getElementById('preview-hero');
        if (hero) {
            hero.style.backgroundColor = surface;
            hero.style.borderColor = primary + '25';
        }

        const badge = document.getElementById('preview-badge');
        if (badge) {
            badge.style.backgroundColor = accent + '20';
            badge.style.color = primary;
        }

        const heading = document.getElementById('preview-heading');
        if (heading) heading.style.color = textPrimary;

        const text = document.getElementById('preview-text');
        if (text) text.style.color = textSecondary;

        const btnPrimary = document.getElementById('preview-btn-primary');
        if (btnPrimary) btnPrimary.style.backgroundColor = primary;

        const btnSecondary = document.getElementById('preview-btn-secondary');
        if (btnSecondary) {
            btnSecondary.style.borderColor = secondary;
            btnSecondary.style.color = secondary;
            btnSecondary.style.backgroundColor = 'transparent';
        }

        // Update card
        const card = document.getElementById('preview-card');
        if (card) {
            card.style.backgroundColor = surface;
            card.style.borderColor = primary + '20';
        }

        const cardTag = document.getElementById('preview-card-tag');
        if (cardTag) cardTag.style.color = accent;

        const cardTitle = document.getElementById('preview-card-title');
        if (cardTitle) cardTitle.style.color = textPrimary;

        const cardBody = document.getElementById('preview-card-body');
        if (cardBody) cardBody.style.color = textSecondary;
    }

    // Jalankan kalkulasi pertama kali saat dokumen dimuat
    document.addEventListener('DOMContentLoaded', updateLivePreview);
</script>
@endsection
