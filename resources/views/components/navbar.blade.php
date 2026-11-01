<nav class="sticky top-0 z-50 backdrop-blur-md border-b text-white transition-all duration-200" style="background-color: var(--theme-primary); border-bottom-color: var(--theme-secondary);">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-20">
            <!-- Brand Logo & Name -->
            <div class="flex items-center gap-3">
                <a href="{{ route('home') }}" class="flex items-center gap-3 group focus:outline-none rounded-lg p-1">
                    @php
                        $customLogoUrl = \App\Models\SiteSetting::getLogoUrl();
                    @endphp
                    @if($customLogoUrl)
                        <img src="{{ $customLogoUrl }}" alt="Logo Portal BEM" class="h-10 w-auto max-w-[140px] object-contain shrink-0">
                    @else
                        <div class="w-11 h-11 rounded-lg border flex items-center justify-center shadow-md transition-colors shrink-0" style="background-color: var(--theme-secondary); border-color: var(--theme-accent);">
                            <span class="font-extrabold text-lg tracking-wider" style="color: var(--theme-accent);">BEM</span>
                        </div>
                    @endif
                    <div class="flex flex-col">
                        <span class="font-bold text-white text-base sm:text-lg tracking-tight group-hover:opacity-90 transition-opacity">Portal BEM Terpadu</span>
                        <span class="text-xs text-slate-300/80 tracking-wide font-medium">Pusat Informasi & Aspirasi Mahasiswa</span>
                    </div>
                </a>
            </div>

            <!-- Desktop Navigation Links -->
            <div class="hidden lg:flex items-center gap-1 xl:gap-2">
                @php
                    $navItems = [
                        ['route' => 'home', 'label' => 'Beranda', 'pattern' => 'home'],
                        ['route' => 'aspirations.info', 'label' => 'Aspirasi', 'pattern' => 'aspirations.*'],
                        ['route' => 'news.index', 'label' => 'Berita', 'pattern' => 'news.*'],
                        ['route' => 'programs.index', 'label' => 'Program Kerja', 'pattern' => 'programs.*'],
                        ['route' => 'events.index', 'label' => 'Agenda', 'pattern' => 'events.*'],
                        ['route' => 'bem.structure', 'label' => 'Pengurus BEM', 'pattern' => 'bem.structure'],
                        ['route' => 'organizations.index', 'label' => 'Ormawa', 'pattern' => 'organizations.*'],
                        ['route' => 'about', 'label' => 'Tentang', 'pattern' => 'about'],
                    ];
                @endphp

                @foreach($navItems as $item)
                    @php $isActive = request()->routeIs($item['pattern']); @endphp
                    <a href="{{ route($item['route']) }}"
                        class="px-3 py-2 rounded-md text-sm font-medium transition-all {{ $isActive ? 'font-semibold border border-white/10 shadow-sm' : 'text-slate-200 hover:text-white hover:opacity-90' }}"
                        style="{{ $isActive ? 'background-color: var(--theme-secondary); color: var(--theme-accent);' : '' }}">
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </div>

            <!-- CTA Aspirasi (Desktop) -->
            <div class="hidden lg:flex items-center gap-3">
                <a href="{{ route('aspirations.tracking.form') }}"
                   class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg text-slate-200 hover:text-white text-xs font-medium transition-colors"
                   style="hover:background-color: var(--theme-secondary);">
                    <svg class="w-4 h-4" style="color: var(--theme-accent);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <span>Lacak Tiket</span>
                </a>
                <a href="{{ route('aspirations.create') }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg text-slate-950 font-semibold text-sm shadow-md transition-all focus:outline-none hover:opacity-90"
                   style="background-color: var(--theme-accent); color: #020617;">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    <span>Sampaikan Aspirasi</span>
                </a>
            </div>

            <!-- Mobile Menu Button -->
            <div class="flex lg:hidden">
                <button type="button"
                        id="mobile-menu-button"
                        aria-expanded="false"
                        aria-controls="mobile-menu"
                        aria-label="Buka menu navigasi"
                        class="p-2.5 rounded-lg text-slate-200 hover:text-white focus:outline-none"
                        style="background-color: var(--theme-secondary);">
                    <!-- Hamburger Icon -->
                    <svg id="menu-icon-open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                    <!-- Close Icon -->
                    <svg id="menu-icon-close" class="w-6 h-6 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu Dropdown -->
    <div id="mobile-menu" class="hidden lg:hidden border-t px-4 pt-3 pb-6 space-y-1" style="background-color: var(--theme-primary); border-top-color: var(--theme-secondary);">
        @php
            $mobileNavItems = [
                ['route' => 'home', 'label' => 'Beranda', 'pattern' => 'home'],
                ['route' => 'aspirations.info', 'label' => 'Kanal Aspirasi Mahasiswa', 'pattern' => 'aspirations.*'],
                ['route' => 'news.index', 'label' => 'Berita', 'pattern' => 'news.*'],
                ['route' => 'programs.index', 'label' => 'Program Kerja', 'pattern' => 'programs.*'],
                ['route' => 'events.index', 'label' => 'Agenda Kegiatan', 'pattern' => 'events.*'],
                ['route' => 'bem.structure', 'label' => 'Pengurus BEM', 'pattern' => 'bem.structure'],
                ['route' => 'organizations.index', 'label' => 'Struktur Organisasi', 'pattern' => 'organizations.*'],
                ['route' => 'about', 'label' => 'Tentang Portal', 'pattern' => 'about'],
                ['route' => 'faq', 'label' => 'FAQ', 'pattern' => 'faq'],
                ['route' => 'contact', 'label' => 'Kontak & Sekretariat', 'pattern' => 'contact'],
            ];
        @endphp

        @foreach($mobileNavItems as $item)
            @php $isActive = request()->routeIs($item['pattern']); @endphp
            <a href="{{ route($item['route']) }}"
               class="block px-3 py-2.5 rounded-md text-base font-medium transition-colors {{ $isActive ? 'font-semibold' : 'text-slate-200 hover:text-white' }}"
               style="{{ $isActive ? 'background-color: var(--theme-secondary); color: var(--theme-accent);' : '' }}">
                {{ $item['label'] }}
            </a>
        @endforeach

        <a href="{{ route('aspirations.tracking.form') }}"
           class="block px-3 py-2.5 rounded-md text-base font-medium transition-colors hover:opacity-80"
           style="color: var(--theme-accent);">
            &rarr; Lacak Status Tiket
        </a>

        <div class="pt-4 mt-2 border-t" style="border-top-color: var(--theme-secondary);">
            <a href="{{ route('aspirations.create') }}"
               class="w-full flex items-center justify-center gap-2 px-4 py-3 rounded-lg text-slate-950 font-semibold text-base shadow-md transition-all hover:opacity-90"
               style="background-color: var(--theme-accent); color: #020617;">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                <span>Sampaikan Aspirasi Baru</span>
            </a>
        </div>
    </div>
</nav>
