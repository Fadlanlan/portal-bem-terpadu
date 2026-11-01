<footer class="border-t text-slate-300" style="background-color: var(--theme-primary); border-top-color: var(--theme-secondary);">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 lg:gap-12">
            <!-- Col 1: Brand & Purpose -->
            <div class="space-y-4">
                <div class="flex items-center gap-3">
                    @php
                        $customLogoUrl = \App\Models\SiteSetting::getLogoUrl();
                    @endphp
                    @if($customLogoUrl)
                        <img src="{{ $customLogoUrl }}" alt="Logo Portal BEM" class="h-10 w-auto max-w-[140px] object-contain shrink-0">
                    @else
                        <div class="w-10 h-10 rounded-lg border flex items-center justify-center shadow shrink-0" style="background-color: var(--theme-secondary); border-color: var(--theme-accent);">
                            <span class="font-extrabold text-base" style="color: var(--theme-accent);">BEM</span>
                        </div>
                    @endif
                    <span class="font-bold text-white text-lg tracking-tight">Portal BEM Terpadu</span>
                </div>
                <p class="text-sm text-slate-300 leading-relaxed">
                    Sistem informasi resmi kemahasiswaan dan kanal aspirasi digital terpadu untuk mewujudkan keterbukaan informasi, sinergi keorganisasian, dan advokasi yang akuntabel.
                </p>
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full border text-xs" style="background-color: var(--theme-secondary); border-color: var(--theme-secondary); color: var(--theme-accent);">
                    <span class="w-2 h-2 rounded-full animate-pulse" style="background-color: var(--theme-accent);"></span>
                    <span>Versi Pengembangan (Fase 2 UI)</span>
                </div>
            </div>

            <!-- Col 2: Navigasi Utama -->
            <div class="space-y-4">
                <h3 class="text-sm font-semibold text-white uppercase tracking-wider">Navigasi Utama</h3>
                <ul class="space-y-2.5 text-sm">
                    <li>
                        <a href="{{ route('home') }}" class="hover:underline transition-colors">Beranda</a>
                    </li>
                    <li>
                        <a href="{{ route('news.index') }}" class="hover:underline transition-colors">Warta & Berita</a>
                    </li>
                    <li>
                        <a href="{{ route('programs.index') }}" class="hover:underline transition-colors">Program Kerja</a>
                    </li>
                    <li>
                        <a href="{{ route('events.index') }}" class="hover:underline transition-colors">Agenda Kampus</a>
                    </li>
                    <li>
                        <a href="{{ route('bem.structure') }}" class="hover:underline transition-colors">Pengurus Kabinet BEM</a>
                    </li>
                    <li>
                        <a href="{{ route('organizations.index') }}" class="hover:underline transition-colors">Struktur Ormawa Kampus</a>
                    </li>
                </ul>
            </div>

            <!-- Col 3: Layanan Mahasiswa & Informasi -->
            <div class="space-y-4">
                <h3 class="text-sm font-semibold text-white uppercase tracking-wider">Layanan & Bantuan</h3>
                <ul class="space-y-2.5 text-sm">
                    <li>
                        <a href="{{ route('aspirations.info') }}" class="font-medium transition-colors flex items-center gap-1.5" style="color: var(--theme-accent);">
                            <span>Kanal Aspirasi Mahasiswa</span>
                            <span class="text-xs px-1.5 py-0.5 rounded border" style="border-color: var(--theme-accent); color: var(--theme-accent);">Prioritas</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('faq') }}" class="hover:underline transition-colors">Pertanyaan Umum (FAQ)</a>
                    </li>
                    <li>
                        <a href="{{ route('about') }}" class="hover:underline transition-colors">Profil & Visi Misi</a>
                    </li>
                    <li>
                        <a href="{{ route('contact') }}" class="hover:underline transition-colors">Kontak Sekretariat</a>
                    </li>
                </ul>
            </div>

            <!-- Col 4: Informasi Jam Layanan & Disclaimer -->
            <div class="space-y-4">
                <h3 class="text-sm font-semibold text-white uppercase tracking-wider">Sekretariat BEM</h3>
                <div class="text-sm text-slate-300 space-y-2">
                    <p class="flex items-start gap-2">
                        <svg class="w-4 h-4 text-slate-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        <span>Gedung Pusat Kegiatan Mahasiswa (PKM) Lt. 1</span>
                    </p>
                    <p class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span>Senin - Jumat: 09:00 - 16:30 WIB</span>
                    </p>
                </div>
                <div class="p-3 rounded-lg border text-xs text-slate-300 leading-relaxed" style="background-color: var(--theme-secondary); border-color: var(--theme-secondary);">
                    <strong class="text-white font-semibold block mb-1">Pemberitahuan Data Demonstrasi:</strong>
                    Seluruh identitas acara, berita, dan program dalam portal ini merupakan data simulasi pengembangan sistem dan belum mengikat sebagai informasi resmi institusi.
                </div>
            </div>
        </div>

        <!-- Bottom Bar -->
        <div class="mt-12 pt-8 border-t flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-400" style="border-top-color: var(--theme-secondary);">
            <p>&copy; {{ date('Y') }} Portal BEM Terpadu. Seluruh hak cipta dilindungi.</p>
            <p class="text-center sm:text-right">
                Dikembangkan dengan Laravel 13, Tailwind CSS v4, dan prinsip aksesibilitas terbuka.
            </p>
        </div>
    </div>
</footer>
