@extends('layouts.app')

@section('title', $article['title'])
@section('meta_description', $article['excerpt'])
@section('og_title', $article['title'])
@section('og_description', $article['excerpt'])
@section('og_type', 'article')
@push('og_meta')
<meta property="og:image" content="{{ $article instanceof \App\Models\Article ? $article->image_url : (!empty($article['image']) ? asset('storage/' . $article['image']) : asset('images/news-placeholder.svg')) }}">
@endpush

@section('content')
    <div class="text-white py-10 border-b" style="background-color: var(--theme-primary); border-bottom-color: var(--theme-secondary);">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-breadcrumb :items="['Warta & Berita' => route('news.index'), $article['title'] => null]" />

            <div class="mt-4 space-y-4">
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="px-2.5 py-1 rounded-md text-xs font-semibold bg-amber-500/20 text-amber-300 border border-amber-500/30">
                        {{ $article['category'] }}
                    </span>
                    <span class="text-xs text-slate-400 font-mono">Waktu Baca: {{ $article['read_time'] }}</span>
                    @if(!($article instanceof \App\Models\Article))
                        <span class="text-xs text-amber-400/80 italic">&bull; Data Demonstrasi</span>
                    @endif
                </div>

                <h1 class="text-2xl sm:text-4xl font-extrabold tracking-tight text-white leading-tight">
                    {{ $article['title'] }}
                </h1>

                <div class="flex items-center gap-3 text-xs sm:text-sm text-slate-400 pt-2 border-t border-slate-800">
                    <div class="w-8 h-8 rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center text-amber-400 font-bold text-xs">
                        BEM
                    </div>
                    <div>
                        <div class="text-slate-200 font-medium">{{ $article['author'] }}</div>
                        <div class="text-xs text-slate-400">{{ $article['date'] }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Article Content Body -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="grid grid-cols-1 gap-10">
            <article class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-10 shadow-sm space-y-6">
                @php
                    $detailImageUrl = ($article instanceof \App\Models\Article)
                        ? $article->image_url
                        : ($article['image_url'] ?? (!empty($article['image']) ? asset('storage/' . $article['image']) : asset('images/news-placeholder.svg')));
                @endphp

                <!-- Article Cover Image -->
                <div class="relative w-full h-64 sm:h-80 md:h-96 rounded-2xl overflow-hidden border border-slate-200 bg-slate-900 shadow-sm">
                    <img src="{{ $detailImageUrl }}"
                         alt="{{ $article['title'] }}"
                         class="w-full h-full object-cover">
                    <!-- Subtle Bottom Gradient Bar for Caption/Credit -->
                    <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-slate-950/80 via-slate-950/30 to-transparent p-4 sm:p-5 flex items-end justify-between pointer-events-none">
                        <p class="text-xs sm:text-sm text-slate-200 font-medium truncate max-w-md">
                            Dokumentasi Warta Resmi &bull; {{ $article['title'] }}
                        </p>
                        <span class="text-[11px] font-mono text-amber-300 bg-slate-900/80 px-2 py-1 rounded border border-amber-500/30 backdrop-blur-sm">
                            {{ $article['category'] }}
                        </span>
                    </div>
                </div>

                <!-- Paragraphs -->
                <div class="prose prose-slate max-w-none text-slate-700 leading-relaxed space-y-4 text-base sm:text-lg">
                    @foreach(explode("\n\n", $article['content']) as $paragraph)
                        <p>{{ $paragraph }}</p>
                    @endforeach
                </div>

                <!-- Tags -->
                @if(isset($article['tags']) && count($article['tags']) > 0)
                    <div class="pt-6 border-t border-slate-200 flex items-center gap-2 flex-wrap">
                        <span class="text-xs font-semibold text-slate-500">Topik Terkait:</span>
                        @foreach($article['tags'] as $tag)
                            <span class="px-2.5 py-1 rounded-md text-xs bg-slate-100 text-slate-700 border border-slate-200">
                                #{{ $tag }}
                            </span>
                        @endforeach
                    </div>
                @endif

                <!-- Navigation Controls -->
                <div class="pt-6 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <a href="{{ route('news.index') }}"
                       class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs sm:text-sm transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        <span>Kembali ke Daftar Berita</span>
                    </a>

                    <a href="{{ route('aspirations.info') }}"
                       class="inline-flex items-center gap-2 text-xs sm:text-sm font-semibold text-amber-700 hover:text-amber-800">
                        <span>Punya tanggapan terkait isu ini? Sampaikan aspirasi &rarr;</span>
                    </a>
                </div>
            </article>

            <!-- Related Articles -->
            @if(count($relatedArticles) > 0)
                <div class="space-y-4">
                    <h2 class="text-xl font-bold text-slate-900">Warta Terkait Kategori {{ $article['category'] }}</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        @foreach($relatedArticles as $rel)
                            @php
                                $relImg = ($rel instanceof \App\Models\Article)
                                    ? $rel->image_url
                                    : ($rel['image_url'] ?? (!empty($rel['image']) ? asset('storage/' . $rel['image']) : asset('images/news-placeholder.svg')));
                            @endphp
                            <div class="bg-white rounded-xl border border-slate-200 p-5 flex flex-col justify-between hover:shadow-sm transition-shadow">
                                <div class="flex gap-4">
                                    <img src="{{ $relImg }}" alt="{{ $rel['title'] }}" class="w-20 h-16 object-cover rounded-lg shrink-0 border border-slate-200 bg-slate-900">
                                    <div class="space-y-1 min-w-0">
                                        <span class="text-xs font-mono text-slate-400">{{ $rel['date'] }}</span>
                                        <h3 class="font-bold text-slate-900 text-sm sm:text-base line-clamp-2">
                                            <a href="{{ route('news.show', $rel['slug']) }}" class="hover:text-blue-900">
                                                {{ $rel['title'] }}
                                            </a>
                                        </h3>
                                    </div>
                                </div>
                                <div class="pt-3 mt-3 border-t border-slate-100 flex items-center justify-between">
                                    <span class="text-[11px] text-slate-400 font-mono">{{ $rel['read_time'] ?? '3 menit' }}</span>
                                    <a href="{{ route('news.show', $rel['slug']) }}" class="text-xs font-semibold text-amber-700 hover:text-amber-800 flex items-center gap-1">
                                        <span>Baca selengkapnya</span>
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
