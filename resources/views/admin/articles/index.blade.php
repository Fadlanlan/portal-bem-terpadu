@extends('layouts.admin')

@section('title', 'Manajemen Warta & Berita')
@section('header_title', 'Kelola Warta & Berita')

@section('content')
<div class="space-y-6">
    <!-- Header with Action Button -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">Daftar Warta & Berita</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                Kelola publikasi berita, rilis pers kelembagaan, dan liputan kegiatan kemahasiswaan.
            </p>
        </div>

        <a href="{{ route('admin.articles.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs sm:text-sm shadow-md transition-colors self-start sm:self-auto">
            <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            <span>Tulis Berita Baru</span>
        </a>
    </div>

    <!-- Search & Filter Controls -->
    <div class="bg-white rounded-2xl border border-slate-200 p-4 sm:p-5 shadow-sm">
        <form action="{{ route('admin.articles.index') }}" method="GET" class="flex flex-col sm:flex-row items-center gap-3">
            <div class="w-full sm:flex-1 relative">
                <input type="text"
                       name="q"
                       value="{{ $filters['q'] ?? '' }}"
                       placeholder="Cari judul berita atau ringkasan..."
                       class="w-full pl-9 pr-4 py-2 text-xs sm:text-sm rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-amber-500 bg-white">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>

            <div class="w-full sm:w-44">
                <select name="status" class="w-full rounded-xl border border-slate-300 py-2 px-3 text-xs sm:text-sm text-slate-900 bg-white focus:outline-none focus:ring-2 focus:ring-amber-500">
                    <option value="">-- Semua Status --</option>
                    <option value="published" {{ ($filters['status'] ?? '') === 'published' ? 'selected' : '' }}>Terpublikasi</option>
                    <option value="draft" {{ ($filters['status'] ?? '') === 'draft' ? 'selected' : '' }}>Konsep (Draft)</option>
                </select>
            </div>

            <div class="flex items-center gap-2 w-full sm:w-auto">
                <button type="submit" class="w-full sm:w-auto px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs sm:text-sm font-semibold transition-colors">
                    Filter
                </button>
                @if(!empty(array_filter($filters)))
                    <a href="{{ route('admin.articles.index') }}" class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs sm:text-sm font-semibold transition-colors">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table Articles -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs sm:text-sm text-slate-700 divide-y divide-slate-200">
                <thead class="bg-slate-50 text-slate-500 uppercase font-semibold text-[11px] tracking-wider">
                    <tr>
                        <th class="px-5 py-3.5">Judul Warta Berita</th>
                        <th class="px-5 py-3.5">Kategori</th>
                        <th class="px-5 py-3.5">Penulis</th>
                        <th class="px-5 py-3.5">Status</th>
                        <th class="px-5 py-3.5">Tanggal</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($articles as $art)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $art->image_url }}" alt="{{ $art->title }}" class="w-12 h-9 object-cover rounded-lg border border-slate-200 shrink-0 bg-slate-100">
                                    <div class="min-w-0">
                                        <a href="{{ route('admin.articles.edit', $art) }}" class="font-bold text-slate-900 hover:text-blue-900 line-clamp-1 max-w-sm block">
                                            {{ $art->title }}
                                        </a>
                                        <span class="text-[11px] text-slate-400 font-mono block">/berita/{{ $art->slug }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                <span class="px-2.5 py-0.5 rounded-lg text-xs bg-slate-100 text-slate-800 font-medium">
                                    {{ $art->category }}
                                </span>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-xs text-slate-600">
                                {{ $art->author_name }}
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold {{ $art->status === 'published' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-700' }}">
                                    {{ $art->status === 'published' ? 'Terbit' : 'Draft' }}
                                </span>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-xs text-slate-500">
                                {{ $art->published_at ? $art->published_at->format('d/m/Y') : ($art->created_at->format('d/m/Y') . ' (Dibuat)') }}
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-right space-x-2">
                                <a href="{{ route('admin.articles.edit', $art) }}"
                                   class="inline-flex items-center px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs transition-colors">
                                    Edit
                                </a>

                                <form action="{{ route('admin.articles.destroy', $art) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus warta berita ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center px-2.5 py-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 font-semibold text-xs transition-colors">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center text-slate-500">
                                <span class="font-semibold block text-sm">Belum Ada Warta Berita</span>
                                <span class="text-xs text-slate-400">Klik "Tulis Berita Baru" untuk menambahkan artikel warta kemahasiswaan.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($articles->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $articles->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
