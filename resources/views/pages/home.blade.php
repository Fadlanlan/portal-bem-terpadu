@extends('layouts.app')

@section('title', 'Beranda')
@section('meta_description', 'Portal BEM Terpadu - Platform resmi informasi kemahasiswaan, transparansi program kerja, agenda bersama, dan kanal aspirasi mahasiswa.')

@section('content')
    <!-- 1. HERO SECTION -->
    <section class="relative text-white overflow-hidden py-16 sm:py-24 lg:py-28 border-b"
             style="background: linear-gradient(180deg, var(--theme-primary) 0%, var(--theme-primary) 65%, var(--theme-secondary) 100%); border-bottom-color: var(--theme-secondary);">
        <!-- Subtle Background Glow -->
        <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#d4a843_1px,transparent_1px)] [background-size:20px_20px] pointer-events-none" aria-hidden="true"></div>
        <div class="absolute -top-32 -right-32 w-96 h-96 rounded-full blur-3xl pointer-events-none opacity-20" style="background-color: var(--theme-secondary);" aria-hidden="true"></div>
        <div class="absolute -bottom-32 -left-32 w-96 h-96 rounded-full blur-3xl pointer-events-none opacity-10" style="background-color: var(--theme-accent);" aria-hidden="true"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                <!-- Hero Left: Text & Actions -->
                <div class="lg:col-span-7 space-y-6 text-center lg:text-left">
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full border text-xs sm:text-sm font-medium"
                         style="background-color: var(--theme-secondary); border-color: var(--theme-secondary); color: var(--theme-accent);">
                        <span class="w-2 h-2 rounded-full animate-pulse" style="background-color: var(--theme-accent);"></span>
                        <span>Portal Resmi Kemahasiswaan & Aspirasi</span>
                    </div>

                    <h1 class="text-3xl sm:text-5xl lg:text-5xl font-extrabold tracking-tight text-white leading-tight">
                        Satu Pintu Informasi, <br class="hidden sm:inline">
                        <span class="text-transparent bg-clip-text" style="background-image: linear-gradient(to right, var(--theme-accent), #fef08a); -webkit-background-clip: text;">
                            Wadah Aspirasi Mahasiswa
                        </span>
                    </h1>

                    <p class="text-base sm:text-lg text-slate-200 leading-relaxed max-w-2xl mx-auto lg:mx-0">
                        Wujudkan tata kelola organisasi yang transparan, advokasi hak mahasiswa yang terukur, dan kemudahan akses seluruh publikasi kegiatan kampus dalam satu ekosistem terpadu.
                    </p>

                    <!-- CTAs -->
                    <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4 pt-2">
                        <a href="{{ route('aspirations.info') }}"
                           class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl text-slate-950 font-bold text-base shadow-lg transition-all hover:opacity-90"
                           style="background-color: var(--theme-accent); color: #020617;">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            <span>Sampaikan Suara Anda</span>
                        </a>

                        <a href="{{ route('programs.index') }}"
                           class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl text-white font-semibold text-base border transition-all hover:opacity-90"
                           style="background-color: var(--theme-secondary); border-color: var(--theme-secondary);">
                            <span>Jelajahi Program Kerja</span>
                            <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>

                    <!-- Mini Value Points -->
                    <div class="pt-6 border-t grid grid-cols-3 gap-4 text-center lg:text-left" style="border-top-color: var(--theme-secondary);">
                        <div>
                            <div class="text-xl sm:text-2xl font-bold text-white tracking-tight">100%</div>
                            <div class="text-xs text-slate-300 font-medium">Tiket Terlacak</div>
                        </div>
                        <div>
                            <div class="text-xl sm:text-2xl font-bold text-white tracking-tight">6 Rumpun</div>
                            <div class="text-xs text-slate-300 font-medium">Lembaga Ormawa</div>
                        </div>
                        <div>
                            <div class="text-xl sm:text-2xl font-bold text-white tracking-tight">Terbuka</div>
                            <div class="text-xs text-slate-300 font-medium">Untuk Semua Mahasiswa</div>
                        </div>
                    </div>
                </div>

                <!-- Hero Right: Feature Highlight Card -->
                <div class="lg:col-span-5">
                    <div class="relative rounded-2xl border p-6 sm:p-8 shadow-2xl backdrop-blur-sm"
                         style="background: linear-gradient(180deg, var(--theme-secondary) 0%, var(--theme-primary) 100%); border-color: var(--theme-secondary);">
                        <div class="flex items-center justify-between pb-4 border-b" style="border-bottom-color: var(--theme-secondary);">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-lg border flex items-center justify-center"
                                     style="background-color: rgba(245, 158, 11, 0.1); border-color: var(--theme-accent); color: var(--theme-accent);">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h2 class="text-sm font-semibold text-white">Alur Aspirasi Terstruktur</h2>
                                    <p class="text-xs text-slate-300">Transparan, Terukur, & Bertanggung Jawab</p>
                                </div>
                            </div>
                            <span class="text-xs px-2.5 py-1 rounded border font-mono text-white"
                                  style="background-color: var(--theme-primary); border-color: var(--theme-secondary);">Tiket Resmi</span>
                        </div>

                        <!-- Process Steps -->
                        <div class="mt-6 space-y-4">
                            <div class="flex items-start gap-3.5">
                                <div class="w-7 h-7 rounded-full border font-bold text-xs flex items-center justify-center shrink-0"
                                     style="background-color: var(--theme-secondary); border-color: var(--theme-accent); color: var(--theme-accent);">1</div>
                                <div>
                                    <h3 class="text-sm font-semibold text-slate-100">Pengajuan Aspirasi</h3>
                                    <p class="text-xs text-slate-300 mt-0.5">Isi kategori, uraian permasalahan atau usulan, lampiran berkas, serta opsi privasi pelapor.</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-3.5">
                                <div class="w-7 h-7 rounded-full border text-slate-300 font-bold text-xs flex items-center justify-center shrink-0"
                                     style="background-color: var(--theme-secondary); border-color: var(--theme-secondary);">2</div>
                                <div>
                                    <h3 class="text-sm font-semibold text-slate-100">Verifikasi & Penomoran Tiket</h3>
                                    <p class="text-xs text-slate-300 mt-0.5">Sistem menerbitkan nomor tiket unik pelacakan dan tim advokasi memvalidasi relevansi laporan.</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-3.5">
                                <div class="w-7 h-7 rounded-full border text-slate-300 font-bold text-xs flex items-center justify-center shrink-0"
                                     style="background-color: var(--theme-secondary); border-color: var(--theme-secondary);">3</div>
                                <div>
                                    <h3 class="text-sm font-semibold text-slate-100">Tindak Lanjut & Audiensi</h3>
                                    <p class="text-xs text-slate-300 mt-0.5">Penyaluran ke kementerian terkait atau rekomendasi resmi ke pihak pimpinan kampus.</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-3.5">
                                <div class="w-7 h-7 rounded-full border text-slate-300 font-bold text-xs flex items-center justify-center shrink-0"
                                     style="background-color: var(--theme-secondary); border-color: var(--theme-secondary);">4</div>
                                <div>
                                    <h3 class="text-sm font-semibold text-slate-100">Pembaruan Status & Tanggapan Resmi</h3>
                                    <p class="text-xs text-slate-300 mt-0.5">Pantau status perkembangan laporan kapan pun melalui nomor tiket Anda secara mandiri.</p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 pt-4 border-t" style="border-top-color: var(--theme-secondary);">
                            <a href="{{ route('aspirations.info') }}"
                               class="w-full flex items-center justify-center gap-2 py-2.5 px-4 rounded-lg text-xs font-semibold transition-colors border hover:opacity-90"
                               style="background-color: var(--theme-secondary); border-color: var(--theme-secondary); color: var(--theme-accent);">
                                <span>Pelajari Panduan Lengkap Aspirasi</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 2. RINGKASAN FITUR UTAMA -->
    <section class="py-12 sm:py-16 bg-white border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-10 sm:mb-12">
                <span class="text-xs font-bold uppercase tracking-wider text-amber-600">Layanan Terintegrasi</span>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-1">Pusat Layanan Mahasiswa Terpadu</h2>
                <p class="text-sm sm:text-base text-slate-600 mt-2">Seluruh kebutuhan komunikasi, advokasi, dan informasi keorganisasian dalam genggaman Anda.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6">
                <!-- Fitur 1 -->
                <div class="p-6 rounded-xl bg-slate-50 border border-slate-200/80 hover:border-amber-400 hover:shadow-md transition-all group">
                    <div class="w-12 h-12 rounded-lg bg-amber-500/10 border border-amber-500/20 text-amber-600 flex items-center justify-center mb-4 group-hover:bg-amber-500 group-hover:text-slate-950 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                    </div>
                    <h3 class="font-bold text-slate-900 text-base mb-1">Kanal Aspirasi</h3>
                    <p class="text-xs text-slate-600 leading-relaxed mb-3">Sampaikan saran, keluhan, dan kritik konstruktif melalui formulir resmi terlacak tiket.</p>
                    <a href="{{ route('aspirations.info') }}" class="text-xs font-semibold text-amber-700 hover:text-amber-800 flex items-center gap-1">
                        <span>Buka kanal</span>
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </a>
                </div>

                <!-- Fitur 2 -->
                <div class="p-6 rounded-xl bg-slate-50 border border-slate-200/80 hover:border-blue-500 hover:shadow-md transition-all group">
                    <div class="w-12 h-12 rounded-lg bg-blue-500/10 border border-blue-500/20 text-blue-700 flex items-center justify-center mb-4 group-hover:bg-blue-900 group-hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
                    </div>
                    <h3 class="font-bold text-slate-900 text-base mb-1">Warta & Berita</h3>
                    <p class="text-xs text-slate-600 leading-relaxed mb-3">Publikasi berkala seputar dinamika kampus, pengumuman ormawa, dan capaian mahasiswa.</p>
                    <a href="{{ route('news.index') }}" class="text-xs font-semibold text-blue-800 hover:text-blue-900 flex items-center gap-1">
                        <span>Baca berita</span>
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </a>
                </div>

                <!-- Fitur 3 -->
                <div class="p-6 rounded-xl bg-slate-50 border border-slate-200/80 hover:border-indigo-500 hover:shadow-md transition-all group">
                    <div class="w-12 h-12 rounded-lg bg-indigo-500/10 border border-indigo-500/20 text-indigo-700 flex items-center justify-center mb-4 group-hover:bg-indigo-900 group-hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                    </div>
                    <h3 class="font-bold text-slate-900 text-base mb-1">Program Kerja</h3>
                    <p class="text-xs text-slate-600 leading-relaxed mb-3">Transparansi status, tujuan, penanggung jawab, dan jadwal program tiap kementerian.</p>
                    <a href="{{ route('programs.index') }}" class="text-xs font-semibold text-indigo-800 hover:text-indigo-900 flex items-center gap-1">
                        <span>Lihat program</span>
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </a>
                </div>

                <!-- Fitur 4 -->
                <div class="p-6 rounded-xl bg-slate-50 border border-slate-200/80 hover:border-emerald-500 hover:shadow-md transition-all group">
                    <div class="w-12 h-12 rounded-lg bg-emerald-500/10 border border-emerald-500/20 text-emerald-700 flex items-center justify-center mb-4 group-hover:bg-emerald-800 group-hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <h3 class="font-bold text-slate-900 text-base mb-1">Agenda Kampus</h3>
                    <p class="text-xs text-slate-600 leading-relaxed mb-3">Kalender bersama kegiatan, seminar, musyawarah, dan festival organisasi mahasiswa.</p>
                    <a href="{{ route('events.index') }}" class="text-xs font-semibold text-emerald-800 hover:text-emerald-900 flex items-center gap-1">
                        <span>Cek jadwal</span>
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </a>
                </div>

                <!-- Fitur 5 -->
                <div class="p-6 rounded-xl bg-slate-50 border border-slate-200/80 hover:border-purple-500 hover:shadow-md transition-all group sm:col-span-2 lg:col-span-1">
                    <div class="w-12 h-12 rounded-lg bg-purple-500/10 border border-purple-500/20 text-purple-700 flex items-center justify-center mb-4 group-hover:bg-purple-900 group-hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                    <h3 class="font-bold text-slate-900 text-base mb-1">Struktur Ormawa</h3>
                    <p class="text-xs text-slate-600 leading-relaxed mb-3">Informasi peran dan fungsi lembaga: BEM, MAM, DPM, HIMA, HMPS, hingga UKM.</p>
                    <a href="{{ route('organizations.index') }}" class="text-xs font-semibold text-purple-800 hover:text-purple-900 flex items-center gap-1">
                        <span>Kenali ormawa</span>
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- 3. SOROTAN ASPIRASI (FITUR PRIORITAS) -->
    <section class="py-14 sm:py-20 text-white relative overflow-hidden" style="background-color: var(--theme-primary);">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
            <div class="rounded-3xl border p-8 sm:p-12 lg:p-16 shadow-2xl"
                 style="background: linear-gradient(135deg, var(--theme-secondary) 0%, var(--theme-primary) 100%); border-color: var(--theme-secondary);">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-center">
                    <div class="lg:col-span-7 space-y-4">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-md border text-xs font-bold tracking-wide uppercase"
                             style="background-color: rgba(245, 158, 11, 0.1); border-color: var(--theme-accent); color: var(--theme-accent);">
                            Prioritas Mahasiswa
                        </div>
                        <h2 class="text-2xl sm:text-4xl font-extrabold tracking-tight text-white">
                            Suara Anda Membawa Perubahan Faktual
                        </h2>
                        <p class="text-sm sm:text-base text-slate-300 leading-relaxed">
                            Jangan biarkan keluhan fasilitas kampus, kendala penyesuaian biaya kuliah, atau gagasan inovasi akademik berhenti di ruang obrolan. Salurkan secara resmi agar mendapatkan tanggapan tertulis dan advokasi nyata dari pengurus perwakilan Anda.
                        </p>

                        <div class="pt-4 grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs sm:text-sm text-slate-300">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 shrink-0" style="color: var(--theme-accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                <span>Pilihan Identitas Terjaga / Anonim</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 shrink-0" style="color: var(--theme-accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                <span>Nomor Tiket Otomatis untuk Pelacakan</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 shrink-0" style="color: var(--theme-accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                <span>Pendampingan Langsung Divisi Advokasi</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 shrink-0" style="color: var(--theme-accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                <span>Tanggapan Resmi Tanpa Kebocoran Privasi</span>
                            </div>
                        </div>

                        <div class="pt-6 flex flex-wrap gap-4">
                            <a href="{{ route('aspirations.info') }}"
                               class="inline-flex items-center gap-2 px-6 py-3 rounded-xl text-slate-950 font-bold text-sm shadow-md transition-all hover:opacity-90"
                               style="background-color: var(--theme-accent); color: #020617;">
                                <span>Ketahui Alur & Kirim Aspirasi</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </a>
                            <a href="{{ route('faq') }}"
                               class="inline-flex items-center gap-2 px-5 py-3 rounded-xl text-white font-semibold text-sm border transition-all hover:opacity-90"
                               style="background-color: var(--theme-secondary); border-color: var(--theme-secondary);">
                                <span>Baca FAQ Aspirasi</span>
                            </a>
                        </div>
                    </div>

                    <!-- Mini Visual Ticket Representation -->
                    <div class="lg:col-span-5">
                        <div class="p-6 rounded-2xl bg-slate-800/80 border border-slate-700 text-slate-200 space-y-4">
                            <div class="flex items-center justify-between border-b border-slate-700 pb-3">
                                <span class="text-xs font-mono text-amber-400 font-bold">CONTOH TIKET PELACAKAN</span>
                                <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">DIPROSES</span>
                            </div>
                            <div>
                                <div class="text-xs text-slate-400">Nomor Registrasi:</div>
                                <div class="font-mono text-base font-bold text-white tracking-wider">BEM-2026-X7812</div>
                            </div>
                            <div>
                                <div class="text-xs text-slate-400">Kategori Permasalahan:</div>
                                <div class="text-sm font-semibold text-slate-200">Fasilitas & Pendingin Udara Lab Terpadu</div>
                            </div>
                            <div class="text-xs text-slate-400 bg-slate-900/80 p-3 rounded-lg border border-slate-800">
                                <strong class="text-slate-300 block mb-0.5">Catatan Tim Advokasi:</strong>
                                Laporan telah diteruskan ke bagian sarana prasarana kampus. Jadwal inspeksi teknisi: Kamis, 05 Nov 2026.
                            </div>
                            <div class="text-[11px] text-slate-500 text-right">
                                Pembaruan terakhir: 24 menit lalu
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 4. WARTA & BERITA TERBARU -->
    <section class="py-14 sm:py-20 bg-slate-50 border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-10 gap-4">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-amber-600">Kabar Kampus</span>
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-1">Warta & Berita Terkini</h2>
                    <p class="text-sm text-slate-600 mt-1">Publikasi berkala seputar dinamika, advokasi, dan pengumuman ormawa.</p>
                </div>
                <a href="{{ route('news.index') }}"
                   class="inline-flex items-center gap-1.5 text-sm font-semibold text-blue-900 hover:text-amber-600 transition-colors">
                    <span>Lihat Seluruh Berita</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8">
                @foreach($featuredNews as $article)
                    @php
                        $homeArticleImg = ($article instanceof \App\Models\Article)
                            ? $article->image_url
                            : ($article['image_url'] ?? (!empty($article['image']) ? asset('storage/' . $article['image']) : asset('images/news-placeholder.svg')));
                    @endphp
                    <article class="bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition-all flex flex-col overflow-hidden group">
                        <!-- Article Image Thumbnail with Badges -->
                        <div class="relative h-48 w-full bg-slate-900 overflow-hidden">
                            <img src="{{ $homeArticleImg }}"
                                 alt="{{ $article['title'] }}"
                                 loading="lazy"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            <!-- Overlay Gradient for Readability -->
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-950/70 via-transparent to-black/30 pointer-events-none"></div>

                            <!-- Badges -->
                            <div class="absolute top-3.5 left-3.5 right-3.5 flex items-center justify-between pointer-events-none">
                                <span class="px-2.5 py-1 rounded-md text-xs font-semibold bg-slate-900/80 text-amber-300 border border-amber-500/30 backdrop-blur-md shadow-xs">
                                    {{ $article['category'] }}
                                </span>
                                <span class="px-2 py-0.5 rounded text-[11px] font-mono text-slate-200 bg-slate-900/70 backdrop-blur-md border border-white/10">
                                    {{ $article['read_time'] }}
                                </span>
                            </div>
                        </div>

                        <div class="p-6 flex-grow flex flex-col justify-between space-y-4">
                            <div class="space-y-2">
                                <div class="text-xs text-slate-500 flex items-center gap-2">
                                    <span>{{ $article['date'] }}</span>
                                    <span>&bull;</span>
                                    <span>{{ $article['author'] }}</span>
                                </div>
                                <h3 class="font-bold text-slate-900 text-lg group-hover:text-blue-900 transition-colors line-clamp-2">
                                    <a href="{{ route('news.show', $article['slug']) }}" class="focus:outline-none">
                                        {{ $article['title'] }}
                                    </a>
                                </h3>
                                <p class="text-xs sm:text-sm text-slate-600 line-clamp-3 leading-relaxed">
                                    {{ $article['excerpt'] }}
                                </p>
                            </div>

                            <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                                <a href="{{ route('news.show', $article['slug']) }}"
                                   class="text-xs font-semibold text-amber-700 hover:text-amber-800 flex items-center gap-1">
                                    <span>Selengkapnya</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                </a>
                                @if(!($article instanceof \App\Models\Article))
                                    <span class="text-[11px] text-slate-400 italic">Data Demonstrasi</span>
                                @endif
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <!-- 5. PROGRAM KERJA UNGGULAN -->
    <section class="py-14 sm:py-20 bg-white border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-10 gap-4">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-amber-600">Transparansi Kinerja</span>
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-1">Program Kerja Unggulan</h2>
                    <p class="text-sm text-slate-600 mt-1">Rencana dan implementasi kegiatan kementerian periode kepengurusan aktif.</p>
                </div>
                <a href="{{ route('programs.index') }}"
                   class="inline-flex items-center gap-1.5 text-sm font-semibold text-blue-900 hover:text-amber-600 transition-colors">
                    <span>Lihat Seluruh Program</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8">
                @foreach($featuredPrograms as $program)
                    <div class="rounded-2xl border border-slate-200 bg-slate-50/50 p-6 flex flex-col justify-between hover:border-slate-300 hover:shadow-sm transition-all group">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wide">
                                    {{ $program['organization'] }}
                                </span>
                                <x-badge :type="$program['status']">{{ $program['status'] }}</x-badge>
                            </div>

                            <h3 class="text-lg font-bold text-slate-900 group-hover:text-blue-900 transition-colors">
                                <a href="{{ route('programs.show', $program['slug']) }}">
                                    {{ $program['name'] }}
                                </a>
                            </h3>

                            <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                                {{ $program['description'] }}
                            </p>
                        </div>

                        <div class="pt-4 mt-4 border-t border-slate-200/80 flex items-center justify-between text-xs text-slate-500">
                            <span>{{ $program['period'] }}</span>
                            <a href="{{ route('programs.show', $program['slug']) }}" class="font-semibold text-amber-700 hover:text-amber-800 flex items-center gap-1">
                                <span>Detail</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- 6. AGENDA MENDATANG -->
    <section class="py-14 sm:py-20 bg-slate-50 border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-10 gap-4">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-amber-600">Kalender Bersama</span>
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-1">Agenda & Kegiatan Mendatang</h2>
                    <p class="text-sm text-slate-600 mt-1">Jadwal resmi musyawarah, dialog kampus, dan festival mahasiswa (Asia/Jakarta).</p>
                </div>
                <a href="{{ route('events.index') }}"
                   class="inline-flex items-center gap-1.5 text-sm font-semibold text-blue-900 hover:text-amber-600 transition-colors">
                    <span>Lihat Kalender Lengkap</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($upcomingEvents as $event)
                    <div class="bg-white rounded-2xl border border-slate-200 p-6 flex flex-col justify-between hover:border-slate-300 hover:shadow-md transition-all">
                        <div class="space-y-3">
                            <div class="flex items-center justify-between text-xs">
                                <span class="px-2.5 py-1 rounded bg-blue-50 text-blue-800 font-semibold border border-blue-200">
                                    {{ $event['organizer'] }}
                                </span>
                                @if($event['is_registration_open'])
                                    <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">Pendaftaran Buka</span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-slate-100 text-slate-600 border border-slate-200">Terbuka / Delegasi</span>
                                @endif
                            </div>

                            <h3 class="font-bold text-slate-900 text-base hover:text-blue-900 transition-colors">
                                <a href="{{ route('events.show', $event['slug']) }}">
                                    {{ $event['title'] }}
                                </a>
                            </h3>

                            <div class="space-y-1.5 text-xs text-slate-600 pt-1">
                                <p class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    <span class="font-medium text-slate-800">{{ $event['date'] }}</span>
                                    <span>&bull;</span>
                                    <span>{{ $event['time'] }}</span>
                                </p>
                                <p class="flex items-start gap-2">
                                    <svg class="w-4 h-4 text-slate-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    <span>{{ $event['location'] }}</span>
                                </p>
                            </div>
                        </div>

                        <div class="pt-4 mt-4 border-t border-slate-100 flex items-center justify-between">
                            <a href="{{ route('events.show', $event['slug']) }}" class="text-xs font-semibold text-amber-700 hover:text-amber-800 flex items-center gap-1">
                                <span>Detail Acara</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </a>
                            @if(!($event instanceof \App\Models\Event))
                                <span class="text-[11px] text-slate-400 italic">Data Demonstrasi</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- 7. STRUKTUR ORGANISASI KEMAHASISWAAN -->
    <section class="py-14 sm:py-20 bg-white border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-10">
                <span class="text-xs font-bold uppercase tracking-wider text-amber-600">Sinergi Kelembagaan</span>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mt-1">Rumpun Organisasi Mahasiswa</h2>
                <p class="text-sm text-slate-600 mt-2">Mengenal struktur perwakilan, eksekutif, dan unit kegiatan dalam satu tata kelola kampus yang harmonis.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($organizationsOverview as $org)
                    <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200/80 hover:border-slate-300 transition-all">
                        <div class="inline-block px-2.5 py-1 rounded text-xs font-semibold bg-slate-200 text-slate-800 mb-3">
                            {{ $org['badge'] }}
                        </div>
                        <h3 class="font-bold text-slate-900 text-base mb-2">{{ $org['name'] }}</h3>
                        <p class="text-xs text-slate-600 leading-relaxed">{{ $org['desc'] }}</p>
                    </div>
                @endforeach
            </div>

            <div class="text-center mt-8">
                <a href="{{ route('organizations.index') }}"
                   class="inline-flex items-center gap-2 px-6 py-2.5 rounded-lg text-white text-sm font-semibold transition-all hover:opacity-90"
                   style="background-color: var(--theme-secondary);">
                    <span>Lihat Struktur & Tupoksi Lengkap</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </a>
            </div>
        </div>
    </section>

    <!-- 8. CTA PENUTUP -->
    <section class="py-16 text-white text-center" style="background: linear-gradient(135deg, var(--theme-secondary) 0%, var(--theme-primary) 100%);">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <h2 class="text-2xl sm:text-4xl font-extrabold tracking-tight">
                Bersama Membangun Kampus yang Kredibel & Partisipatif
            </h2>
            <p class="text-slate-300 text-sm sm:text-base leading-relaxed max-w-2xl mx-auto">
                Keterbukaan informasi dan penyaluran aspirasi yang sehat adalah hak setiap mahasiswa. Berpartisipasilah secara konstruktif demi kemajuan almamater tercinta.
            </p>
            <div class="pt-2 flex flex-wrap items-center justify-center gap-4">
                <a href="{{ route('aspirations.info') }}"
                   class="px-6 py-3.5 rounded-xl text-slate-950 font-bold text-sm shadow-lg transition-all hover:opacity-90"
                   style="background-color: var(--theme-accent); color: #020617;">
                    Sampaikan Aspirasi Sekarang
                </a>
                <a href="{{ route('about') }}"
                   class="px-6 py-3.5 rounded-xl text-white font-semibold text-sm border transition-all hover:opacity-90"
                   style="background-color: var(--theme-secondary); border-color: var(--theme-secondary);">
                    Tentang Portal BEM
                </a>
            </div>
        </div>
    </section>
@endsection
