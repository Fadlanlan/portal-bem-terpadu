@extends('layouts.app')

@section('title', 'Warta & Berita')
@section('meta_description', 'Publikasi warta berita, dokumentasi kegiatan, dan pengumuman resmi organisasi kemahasiswaan.')

@section('content')
    <div class="text-white py-12 border-b" style="background-color: var(--theme-primary); border-bottom-color: var(--theme-secondary);">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-breadcrumb :items="['Warta & Berita' => null]" />
            <div class="mt-4 max-w-3xl">
                <span class="text-xs font-bold uppercase tracking-wider" style="color: var(--theme-accent);">Informasi & Publikasi</span>
                <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight mt-1">Warta & Berita Mahasiswa</h1>
                <p class="text-sm sm:text-base text-slate-300 mt-2">
                    Kumpulan rilis resmi, liputan kegiatan ormawa, panduan kebijakan akademik, dan catatan advokasi kampus terkini.
                </p>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <!-- Filter & Search Controls -->
        <div class="bg-white rounded-2xl border border-slate-200 p-4 sm:p-6 mb-8 shadow-sm">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                <!-- Category Pills -->
                <div class="flex items-center flex-wrap gap-2">
                    <span class="text-xs font-semibold text-slate-500 mr-1 hidden sm:inline">Kategori:</span>
                    @foreach($categories as $cat)
                        <a href="{{ route('news.index', ['kategori' => $cat, 'cari' => $searchQuery]) }}"
                           class="px-3 py-1.5 rounded-lg text-xs font-medium transition-colors {{ $currentCategory === $cat ? 'bg-slate-900 text-white font-semibold' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                            {{ $cat }}
                        </a>
                    @endforeach
                </div>

                <!-- Search Input -->
                <form action="{{ route('news.index') }}" method="GET" class="flex items-center gap-2 w-full lg:w-72">
                    @if($currentCategory !== 'Semua')
                        <input type="hidden" name="kategori" value="{{ $currentCategory }}">
                    @endif
                    <div class="relative w-full">
                        <input type="text"
                               name="cari"
                               value="{{ $searchQuery }}"
                               placeholder="Cari judul berita..."
                               aria-label="Cari artikel berita"
                               class="w-full pl-9 pr-3 py-2 text-xs sm:text-sm rounded-lg border border-slate-300 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-amber-500 bg-white text-slate-800">
                        <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <button type="submit" class="px-3.5 py-2 rounded-lg bg-slate-900 text-white hover:bg-slate-800 text-xs sm:text-sm font-semibold transition-colors shrink-0">
                        Cari
                    </button>
                    @if($searchQuery !== '' || $currentCategory !== 'Semua')
                        <a href="{{ route('news.index') }}" class="p-2 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors" title="Reset filter">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </a>
                    @endif
                </form>
            </div>
        </div>

        <!-- Articles Grid / Empty State -->
        @if(count($articles) > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
                @foreach($articles as $article)
                    @php
                        $imageUrl = ($article instanceof \App\Models\Article)
                            ? $article->image_url
                            : ($article['image_url'] ?? (!empty($article['image']) ? asset('storage/' . $article['image']) : asset('images/news-placeholder.svg')));
                    @endphp
                    <article class="bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition-all flex flex-col overflow-hidden group">
                        <!-- Article Image Thumbnail with Badges -->
                        <div class="relative h-48 w-full bg-slate-900 overflow-hidden">
                            <img src="{{ $imageUrl }}"
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
                                <h2 class="font-bold text-slate-900 text-lg group-hover:text-blue-900 transition-colors line-clamp-2">
                                    <a href="{{ route('news.show', $article['slug']) }}" class="focus:outline-none">
                                        {{ $article['title'] }}
                                    </a>
                                </h2>
                                <p class="text-xs sm:text-sm text-slate-600 line-clamp-3 leading-relaxed">
                                    {{ $article['excerpt'] }}
                                </p>
                            </div>

                            <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                                <a href="{{ route('news.show', $article['slug']) }}"
                                   class="text-xs font-semibold text-amber-700 hover:text-amber-800 flex items-center gap-1">
                                    <span>Baca Lengkap</span>
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

            {{-- Tautan paginasi hanya ditampilkan pada hasil basis data (paginator) --}}
            @if($articles instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator)
                <div class="mt-10">
                    {{ $articles->links() }}
                </div>
            @endif
        @else
            <x-empty-state
                title="Tidak Ada Berita yang Sesuai"
                description="Tidak ditemukan artikel untuk kata kunci atau kategori yang Anda pilih. Coba sesuaikan kata kunci pencarian Anda."
                :actionUrl="route('news.index')"
                actionText="Tampilkan Semua Berita"
            />
        @endif
    </div>
@endsection
