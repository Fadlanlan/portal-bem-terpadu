<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow">

    <title>@yield('title', 'Dashboard') &mdash; Admin Portal BEM</title>

    @php
        $adminLogoUrl = \App\Models\SiteSetting::getLogoUrl();
    @endphp
    @if($adminLogoUrl)
        <link rel="icon" type="image/png" href="{{ $adminLogoUrl }}">
    @endif

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full flex flex-col font-sans antialiased text-slate-800 bg-slate-100">
    <div class="flex h-screen overflow-hidden">
        <!-- ======================= SIDEBAR DESKTOP ======================= -->
        <aside class="hidden lg:flex lg:flex-col lg:w-72 lg:shrink-0 bg-slate-900 text-white border-r border-slate-800 shadow-xl">
            <!-- Sidebar Header / Brand -->
            <div class="h-20 flex items-center px-6 gap-3 border-b border-slate-800/80 bg-slate-950/40">
                @php
                    $sidebarLogoUrl = \App\Models\SiteSetting::getLogoUrl();
                @endphp
                @if($sidebarLogoUrl)
                    <img src="{{ $sidebarLogoUrl }}" alt="Logo Portal BEM" class="h-10 w-auto max-w-[120px] object-contain shrink-0">
                @else
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-700 via-indigo-900 to-slate-950 border border-amber-400/40 flex items-center justify-center shrink-0 shadow">
                        <span class="font-extrabold text-amber-400 text-base tracking-wider">BEM</span>
                    </div>
                @endif
                <div class="flex flex-col truncate">
                    <span class="font-bold text-white text-base tracking-tight truncate">Portal BEM Terpadu</span>
                    <span class="text-[11px] text-amber-400/90 font-mono font-semibold uppercase tracking-wider">Panel Administrasi</span>
                </div>
            </div>

            <!-- Current User Badge Summary -->
            @php $currentUser = Auth::user(); @endphp
            <div class="p-4 mx-3 my-4 rounded-2xl bg-slate-800/60 border border-slate-700/60 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-amber-500 to-amber-300 text-slate-950 font-extrabold flex items-center justify-center text-sm shrink-0 shadow">
                    {{ strtoupper(substr($currentUser?->name ?? 'A', 0, 1)) }}
                </div>
                <div class="truncate">
                    <div class="text-sm font-bold text-white truncate">{{ $currentUser?->name }}</div>
                    <div class="flex items-center gap-1.5 mt-0.5">
                        <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold tracking-wide uppercase bg-amber-500/20 text-amber-300 border border-amber-500/30">
                            {{ $currentUser?->role_label }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 px-4 space-y-1.5 overflow-y-auto py-2 text-sm font-medium">
                <!-- 1. Dashboard -->
                <a href="{{ route('admin.dashboard') }}"
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-amber-500 text-slate-950 font-bold shadow-md shadow-amber-500/20' : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    <span>Dashboard Utama</span>
                </a>

                <!-- 2. Modul Aspirasi Mahasiswa -->
                @if($currentUser?->canAccessModule('aspirations'))
                    <a href="{{ route('admin.aspirations.index') }}"
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.aspirations.*') && !request()->routeIs('admin.reports.*') ? 'bg-amber-500 text-slate-950 font-bold shadow-md shadow-amber-500/20' : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                        </svg>
                        <span>Aspirasi Masuk</span>
                    </a>

                    <a href="{{ route('admin.reports.index') }}"
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.reports.*') ? 'bg-amber-500 text-slate-950 font-bold shadow-md shadow-amber-500/20' : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <span>Laporan & Ekspor CSV</span>
                    </a>
                @endif

                <!-- 3. Modul Warta & Berita -->
                @if($currentUser?->canAccessModule('articles'))
                    <a href="{{ route('admin.articles.index') }}"
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.articles.*') ? 'bg-amber-500 text-slate-950 font-bold shadow-md shadow-amber-500/20' : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                        </svg>
                        <span>Warta & Berita</span>
                    </a>
                @endif

                <!-- 4. Modul Program Kerja -->
                @if($currentUser?->canAccessModule('programs'))
                    <a href="{{ route('admin.programs.index') }}"
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.programs.*') ? 'bg-amber-500 text-slate-950 font-bold shadow-md shadow-amber-500/20' : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                        </svg>
                        <span>Program Kerja</span>
                    </a>
                @endif

                <!-- 5. Modul Agenda & Event -->
                @if($currentUser?->canAccessModule('events'))
                    <a href="{{ route('admin.events.index') }}"
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.events.*') ? 'bg-amber-500 text-slate-950 font-bold shadow-md shadow-amber-500/20' : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 7V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <span>Agenda Kegiatan</span>
                    </a>
                @endif

                <!-- 6. Modul Organisasi Mahasiswa -->
                @if($currentUser?->canAccessModule('organizations'))
                    <a href="{{ route('admin.organizations.index') }}"
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.organizations.*') ? 'bg-amber-500 text-slate-950 font-bold shadow-md shadow-amber-500/20' : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <span>Struktur Ormawa</span>
                    </a>
                @endif

                <!-- 7. Modul Pengurus BEM -->
                @if($currentUser?->canAccessModule('bem_members'))
                    <a href="{{ route('admin.bem_members.index') }}"
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.bem_members.*') ? 'bg-amber-500 text-slate-950 font-bold shadow-md shadow-amber-500/20' : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <span>Pengurus Kabinet BEM</span>
                    </a>
                @endif

                <!-- 8. Modul Pengguna & Pengaturan Sistem (Super Admin Only) -->
                @if($currentUser?->isSuperAdmin())
                    <div class="pt-4 pb-1">
                        <span class="px-3.5 text-[11px] font-bold uppercase tracking-wider text-slate-400">Administrasi Sistem</span>
                    </div>
                    <a href="{{ route('admin.users.index') }}"
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.users.*') ? 'bg-amber-500 text-slate-950 font-bold shadow-md shadow-amber-500/20' : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        <span>Pengguna / Admin</span>
                    </a>

                    <a href="{{ route('admin.settings.theme') }}"
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.settings.theme') ? 'bg-amber-500 text-slate-950 font-bold shadow-md shadow-amber-500/20' : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
                        </svg>
                        <span>Kustomisasi Tema</span>
                    </a>

                    <a href="{{ route('admin.settings.logo') }}"
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.settings.logo') ? 'bg-amber-500 text-slate-950 font-bold shadow-md shadow-amber-500/20' : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <span>Pengaturan Logo</span>
                    </a>

                    <a href="{{ route('admin.audit_logs.index') }}"
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.audit_logs.*') ? 'bg-amber-500 text-slate-950 font-bold shadow-md shadow-amber-500/20' : 'text-slate-300 hover:text-white hover:bg-slate-800/70' }}">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                        <span>Audit Log Sistem</span>
                    </a>
                @endif
            </nav>

            <!-- Sidebar Bottom: Public Link -->
            <div class="p-4 border-t border-slate-800 bg-slate-950/40">
                <a href="{{ route('home') }}" target="_blank"
                   class="flex items-center justify-between px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-400 hover:text-amber-300 hover:bg-slate-800/60 transition-colors">
                    <span class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                        <span>Lihat Portal Publik</span>
                    </span>
                    <span class="text-[10px] bg-slate-800 px-1.5 py-0.5 rounded">&rarr;</span>
                </a>
            </div>
        </aside>

        <!-- ======================= MAIN WRAPPER ======================= -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <!-- Top Navbar -->
            <header class="h-20 bg-white border-b border-slate-200/90 shadow-sm flex items-center justify-between px-4 sm:px-6 lg:px-8 shrink-0 z-10">
                <!-- Mobile Menu Button & Breadcrumb -->
                <div class="flex items-center gap-3">
                    <button type="button"
                            id="admin-mobile-toggle"
                            class="p-2 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-100 lg:hidden focus:outline-none focus:ring-2 focus:ring-amber-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>

                    <div>
                        <span class="text-xs text-slate-400 block font-medium">Dashboard Administrasi</span>
                        <h2 class="text-lg sm:text-xl font-extrabold text-slate-900 tracking-tight leading-none mt-0.5">
                            @yield('header_title', 'Dashboard')
                        </h2>
                    </div>
                </div>

                <!-- Right Header Actions (User info + Logout) -->
                <div class="flex items-center gap-3 sm:gap-4">
                    <div class="hidden sm:flex flex-col text-right">
                        <span class="text-xs font-bold text-slate-900">{{ $currentUser?->name }}</span>
                        <span class="text-[11px] text-slate-500">{{ $currentUser?->email }}</span>
                    </div>

                    <!-- Logout Button (POST with CSRF) -->
                    <form action="{{ route('admin.logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit"
                                title="Keluar dari akun pengelola"
                                class="inline-flex items-center gap-2 px-3 sm:px-4 py-2 rounded-xl bg-slate-100 hover:bg-rose-50 text-slate-700 hover:text-rose-700 border border-slate-200 text-xs sm:text-sm font-semibold transition-colors">
                            <svg class="w-4 h-4 text-slate-500 group-hover:text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                            <span class="hidden sm:inline">Keluar</span>
                        </button>
                    </form>
                </div>
            </header>

            <!-- Main Content Area -->
            <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
                <!-- Flash Messages -->
                @if(session('success'))
                    <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-300 text-emerald-950 text-xs sm:text-sm flex items-start justify-between gap-3 shadow-sm" role="alert">
                        <div class="flex items-start gap-2.5">
                            <svg class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>{{ session('success') }}</span>
                        </div>
                    </div>
                @endif

                @if(session('error') || session('delete_error') || $errors->has('delete_error'))
                    <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-300 text-rose-950 text-xs sm:text-sm flex items-start gap-2.5 shadow-sm" role="alert">
                        <svg class="w-5 h-5 text-rose-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span>{{ session('error') ?: session('delete_error') ?: $errors->first('delete_error') }}</span>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <!-- ======================= MOBILE SIDEBAR DRAWER ======================= -->
    <div id="admin-mobile-drawer" class="fixed inset-0 z-50 lg:hidden hidden">
        <div id="admin-mobile-backdrop" class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm transition-opacity"></div>
        <div class="fixed inset-y-0 left-0 max-w-xs w-full bg-slate-900 text-white shadow-2xl flex flex-col z-10">
            <div class="h-20 flex items-center justify-between px-6 border-b border-slate-800">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-blue-700 via-indigo-900 to-slate-950 border border-amber-400/40 flex items-center justify-center font-extrabold text-amber-400 text-sm">
                        BEM
                    </div>
                    <span class="font-bold text-white text-base">Dashboard Admin</span>
                </div>
                <button type="button" id="admin-mobile-close" class="p-2 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div class="p-4 mx-3 my-3 rounded-2xl bg-slate-800/80 border border-slate-700 text-xs">
                <div class="font-bold text-white">{{ $currentUser?->name }}</div>
                <div class="text-amber-400 mt-0.5">{{ $currentUser?->role_label }}</div>
            </div>

            <nav class="flex-1 px-4 space-y-1.5 overflow-y-auto text-sm">
                <a href="{{ route('admin.dashboard') }}" class="block px-3 py-2.5 rounded-xl {{ request()->routeIs('admin.dashboard') ? 'bg-amber-500 text-slate-950 font-bold' : 'text-slate-300 hover:bg-slate-800' }}">Dashboard Utama</a>
                @if($currentUser?->canAccessModule('aspirations'))
                    <a href="{{ route('admin.aspirations.index') }}" class="block px-3 py-2.5 rounded-xl {{ request()->routeIs('admin.aspirations.*') && !request()->routeIs('admin.reports.*') ? 'bg-amber-500 text-slate-950 font-bold' : 'text-slate-300 hover:bg-slate-800' }}">Aspirasi Masuk</a>
                    <a href="{{ route('admin.reports.index') }}" class="block px-3 py-2.5 rounded-xl {{ request()->routeIs('admin.reports.*') ? 'bg-amber-500 text-slate-950 font-bold' : 'text-slate-300 hover:bg-slate-800' }}">Laporan & Ekspor CSV</a>
                @endif
                @if($currentUser?->canAccessModule('articles'))
                    <a href="{{ route('admin.articles.index') }}" class="block px-3 py-2.5 rounded-xl {{ request()->routeIs('admin.articles.*') ? 'bg-amber-500 text-slate-950 font-bold' : 'text-slate-300 hover:bg-slate-800' }}">Warta & Berita</a>
                @endif
                @if($currentUser?->canAccessModule('programs'))
                    <a href="{{ route('admin.programs.index') }}" class="block px-3 py-2.5 rounded-xl {{ request()->routeIs('admin.programs.*') ? 'bg-amber-500 text-slate-950 font-bold' : 'text-slate-300 hover:bg-slate-800' }}">Program Kerja</a>
                @endif
                @if($currentUser?->canAccessModule('events'))
                    <a href="{{ route('admin.events.index') }}" class="block px-3 py-2.5 rounded-xl {{ request()->routeIs('admin.events.*') ? 'bg-amber-500 text-slate-950 font-bold' : 'text-slate-300 hover:bg-slate-800' }}">Agenda Kegiatan</a>
                @endif
                @if($currentUser?->canAccessModule('organizations'))
                    <a href="{{ route('admin.organizations.index') }}" class="block px-3 py-2.5 rounded-xl {{ request()->routeIs('admin.organizations.*') ? 'bg-amber-500 text-slate-950 font-bold' : 'text-slate-300 hover:bg-slate-800' }}">Struktur Organisasi</a>
                @endif
                @if($currentUser?->isSuperAdmin())
                    <a href="{{ route('admin.users.index') }}" class="block px-3 py-2.5 rounded-xl {{ request()->routeIs('admin.users.*') ? 'bg-amber-500 text-slate-950 font-bold' : 'text-slate-300 hover:bg-slate-800' }}">Pengguna / Admin</a>
                    <a href="{{ route('admin.audit_logs.index') }}" class="block px-3 py-2.5 rounded-xl {{ request()->routeIs('admin.audit_logs.*') ? 'bg-amber-500 text-slate-950 font-bold' : 'text-slate-300 hover:bg-slate-800' }}">Audit Log Sistem</a>
                @endif
            </nav>

            <div class="p-4 border-t border-slate-800">
                <form action="{{ route('admin.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl bg-slate-800 hover:bg-rose-900/60 text-white font-semibold text-xs">
                        <span>Keluar Sesi</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const toggleBtn = document.getElementById('admin-mobile-toggle');
            const drawer = document.getElementById('admin-mobile-drawer');
            const backdrop = document.getElementById('admin-mobile-backdrop');
            const closeBtn = document.getElementById('admin-mobile-close');

            function openDrawer() {
                if (drawer) drawer.classList.remove('hidden');
            }
            function closeDrawer() {
                if (drawer) drawer.classList.add('hidden');
            }

            if (toggleBtn) toggleBtn.addEventListener('click', openDrawer);
            if (backdrop) backdrop.addEventListener('click', closeDrawer);
            if (closeBtn) closeBtn.addEventListener('click', closeDrawer);

            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && drawer && !drawer.classList.contains('hidden')) {
                    closeDrawer();
                }
            });
        });
    </script>
</body>
</html>
