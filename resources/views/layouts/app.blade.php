<!DOCTYPE html>
<html lang="id" class="h-full scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Portal BEM Terpadu') &mdash; {{ config('app.name', 'Portal BEM Terpadu') }}</title>
    <meta name="description" content="@yield('meta_description', 'Pusat Informasi, Dokumentasi Program Kerja, Kalender Agenda, dan Kanal Aspirasi Mahasiswa Resmi.')">
    <meta name="robots" content="@yield('robots_meta', 'index, follow')">
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- Open Graph Metadata (untuk pratinjau tautan di media sosial) -->
    <meta property="og:site_name" content="{{ config('app.name', 'Portal BEM Terpadu') }}">
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:title" content="@yield('og_title', 'Portal BEM Terpadu')">
    <meta property="og:description" content="@yield('og_description', 'Pusat Informasi, Dokumentasi Program Kerja, Kalender Agenda, dan Kanal Aspirasi Mahasiswa Resmi.')">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:locale" content="id_ID">
    @stack('og_meta')

    <!-- Twitter Card Metadata -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('og_title', 'Portal BEM Terpadu')">
    <meta name="twitter:description" content="@yield('og_description', 'Pusat Informasi, Dokumentasi Program Kerja, Kalender Agenda, dan Kanal Aspirasi Mahasiswa Resmi.')">

    <!-- Fonts: Standard system UI font stack configured via Tailwind v4 -->

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Dynamic Site Theme Variables & Favicon -->
    @php
        $siteTheme = \App\Models\SiteSetting::getThemeSettings();
        $siteLogoUrl = \App\Models\SiteSetting::getLogoUrl();
    @endphp
    @if($siteLogoUrl)
        <link rel="icon" type="image/png" href="{{ $siteLogoUrl }}">
    @endif
    <style>
        :root {
            --theme-primary: {{ $siteTheme['primary_color'] }};
            --theme-secondary: {{ $siteTheme['secondary_color'] }};
            --theme-accent: {{ $siteTheme['accent_color'] }};
            --theme-bg: {{ $siteTheme['background_color'] }};
            --theme-surface: {{ $siteTheme['surface_color'] }};
            --theme-text-primary: {{ $siteTheme['text_primary_color'] }};
            --theme-text-secondary: {{ $siteTheme['text_secondary_color'] }};
        }
        body {
            background-color: var(--theme-bg, #f8fafc);
            color: var(--theme-text-primary, #0f172a);
        }
        ::selection {
            background-color: var(--theme-accent, #f59e0b);
            color: #020617;
        }
    </style>

    @stack('styles')
</head>
<body class="flex flex-col min-h-full font-sans antialiased" style="background-color: var(--theme-bg); color: var(--theme-text-primary);">
    <!-- Skip to Content Accessible Link -->
    <a href="#main-content"
       class="sr-only focus:not-sr-only focus:fixed focus:top-4 focus:left-4 z-50 px-4 py-2 text-slate-950 font-bold rounded-md shadow-lg outline-none ring-2 ring-slate-900"
       style="background-color: var(--theme-accent);">
        Lewati ke Konten Utama
    </a>

    <!-- Global Top Navbar -->
    <x-navbar />

    <!-- Main Content Slot -->
    <main id="main-content" class="flex-grow focus:outline-none" tabindex="-1">
        @yield('content')
    </main>

    <!-- Global Footer -->
    <x-footer />

    @include('components.chat-widget')

    @stack('scripts')
</body>
</html>
