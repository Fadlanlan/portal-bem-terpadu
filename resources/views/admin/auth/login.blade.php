<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Masuk Pengelola &mdash; Dashboard Admin Portal BEM</title>

    @php
        $siteLogoUrl = \App\Models\SiteSetting::getLogoUrl();
    @endphp
    @if($siteLogoUrl)
        <link rel="icon" type="image/png" href="{{ $siteLogoUrl }}">
    @endif

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full flex flex-col justify-center py-12 sm:px-6 lg:px-8 font-sans antialiased bg-slate-900 text-slate-800">
    <div class="sm:mx-auto sm:w-full sm:max-w-md text-center px-4">
        <!-- Logo Brand -->
        <a href="{{ route('home') }}" class="inline-flex items-center gap-3 group focus:outline-none focus:ring-2 focus:ring-amber-400 rounded-xl p-1 mb-3">
            @php
                $customLogoUrl = \App\Models\SiteSetting::getLogoUrl();
            @endphp
            @if($customLogoUrl)
                <img src="{{ $customLogoUrl }}" alt="Logo Portal BEM" class="h-12 w-auto max-w-[150px] object-contain shrink-0">
            @else
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-700 via-indigo-900 to-slate-950 border border-amber-400/50 flex items-center justify-center shadow-lg shadow-blue-950/80 group-hover:border-amber-400 transition-colors shrink-0">
                    <span class="font-extrabold text-amber-400 text-xl tracking-wider">BEM</span>
                </div>
            @endif
            <div class="text-left">
                <span class="font-bold text-white text-lg tracking-tight block">Portal BEM Terpadu</span>
                <span class="text-xs text-slate-400 font-medium block">Dashboard Manajemen Resmi</span>
            </div>
        </a>
        <h1 class="text-xl sm:text-2xl font-extrabold text-white tracking-tight mt-2">
            Autentikasi Masuk Pengelola
        </h1>
        <p class="text-xs sm:text-sm text-slate-400 mt-1">
            Gunakan kredensial resmi terdaftar untuk mengelola modul portal.
        </p>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md px-4">
        <div class="bg-white py-8 px-6 sm:px-10 shadow-2xl rounded-3xl border border-slate-200/80 space-y-6">
            <!-- Flash Notice -->
            @if(session('login_error'))
                <div class="p-4 rounded-xl bg-amber-50 border border-amber-300 text-amber-900 text-xs sm:text-sm flex items-start gap-2.5">
                    <svg class="w-5 h-5 text-amber-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    <span>{{ session('login_error') }}</span>
                </div>
            @endif

            @if(session('status_notice'))
                <div class="p-4 rounded-xl bg-blue-50 border border-blue-200 text-blue-900 text-xs sm:text-sm flex items-start gap-2.5">
                    <svg class="w-5 h-5 text-blue-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>{{ session('status_notice') }}</span>
                </div>
            @endif

            <!-- Global / Authentication Errors -->
            @if($errors->any())
                <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-900 text-xs sm:text-sm space-y-1" role="alert">
                    <strong class="font-bold block">Gagal Masuk:</strong>
                    <ul class="list-disc list-inside space-y-0.5">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.login.attempt') }}" method="POST" class="space-y-5" novalidate>
                @csrf

                <!-- Email Input -->
                <div>
                    <label for="email" class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-1.5">
                        Alamat Surel / Email Resmi <span class="text-rose-500">*</span>
                    </label>
                    <input type="email"
                           id="email"
                           name="email"
                           value="{{ old('email') }}"
                           required
                           autofocus
                           autocomplete="email"
                           placeholder="nama@kampus.ac.id"
                           class="w-full rounded-xl border border-slate-300 py-3 px-4 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 @error('email') border-rose-500 ring-1 ring-rose-500 @enderror">
                </div>

                <!-- Password Input -->
                <div>
                    <label for="password" class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-1.5">
                        Kata Sandi <span class="text-rose-500">*</span>
                    </label>
                    <input type="password"
                           id="password"
                           name="password"
                           required
                           autocomplete="current-password"
                           placeholder="&bull;&bull;&bull;&bull;&bull;&bull;&bull;&bull;"
                           class="w-full rounded-xl border border-slate-300 py-3 px-4 text-sm text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 @error('password') border-rose-500 ring-1 ring-rose-500 @enderror">
                </div>

                <!-- Remember Me -->
                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox"
                               name="remember"
                               value="1"
                               {{ old('remember') ? 'checked' : '' }}
                               class="w-4 h-4 text-amber-600 rounded border-slate-300 focus:ring-amber-500">
                        <span class="text-xs text-slate-600">Ingat sesi saya di peramban ini</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit"
                        class="w-full flex items-center justify-center gap-2 py-3.5 px-6 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-sm shadow-md shadow-slate-900/30 transition-all focus:outline-none focus:ring-2 focus:ring-amber-400 focus:ring-offset-2">
                    <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                    </svg>
                    <span>Masuk ke Dashboard</span>
                </button>
            </form>

            <div class="pt-4 border-t border-slate-100 text-center">
                <a href="{{ route('home') }}" class="text-xs font-semibold text-slate-500 hover:text-amber-600 transition-colors inline-flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    <span>Kembali ke Beranda Publik</span>
                </a>
            </div>
        </div>

        <p class="text-center text-xs text-slate-500 mt-6">
            &copy; 2026 Portal BEM Terpadu &bull; Akses Terbatas Khusus Fungsionaris
        </p>
    </div>
</body>
</html>
