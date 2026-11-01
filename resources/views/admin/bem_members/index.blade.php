@extends('layouts.admin')

@section('title', 'Manajemen Pengurus Kabinet BEM')
@section('header_title', 'Struktur Pengurus & Pejabat BEM')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-200">
        <div>
            <div class="inline-flex items-center gap-2 text-xs text-slate-500 mb-1">
                <span>Manajemen Organisasi</span>
                <span>/</span>
                <span class="text-slate-900 font-semibold">Pengurus BEM</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Daftar Pengurus Kabinet BEM</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Kelola data struktural Presiden Mahasiswa, Wapresma, BPH, Menteri/Kepala Biro, hingga staf kabinet yang tampil di halaman publik.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('bem.structure') }}" target="_blank"
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-bold text-xs shadow-sm transition-all">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                </svg>
                <span>Lihat Halaman Publik</span>
            </a>

            <a href="{{ route('admin.bem_members.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-blue-900 hover:bg-blue-800 text-white font-bold text-xs shadow-md shadow-blue-950/20 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>Tambah Pengurus Baru</span>
            </a>
        </div>
    </div>

    <!-- Alert Success Notification -->
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center gap-3">
            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Search & Filter Bar -->
    <div class="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-sm">
        <form action="{{ route('admin.bem_members.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 items-center">
            <!-- Search Text -->
            <div class="lg:col-span-5 relative">
                <input type="text"
                       name="q"
                       value="{{ $filters['q'] ?? '' }}"
                       placeholder="Cari nama, jabatan, atau kementerian..."
                       class="w-full pl-10 pr-4 py-2 rounded-xl border border-slate-200 text-xs focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-900">
                <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>

            <!-- Level Tier Filter -->
            <div class="lg:col-span-3">
                <select name="level" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:outline-none focus:ring-2 focus:ring-blue-500 text-slate-800">
                    <option value="">-- Semua Tingkatan Hierarki --</option>
                    @foreach($levels as $key => $label)
                        <option value="{{ $key }}" {{ ($filters['level'] ?? '') === $key ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Published Status Filter -->
            <div class="lg:col-span-2">
                <select name="is_published" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:outline-none focus:ring-2 focus:ring-blue-500 text-slate-800">
                    <option value="">-- Status Publikasi --</option>
                    <option value="1" {{ ($filters['is_published'] ?? '') === '1' ? 'selected' : '' }}>Terbit</option>
                    <option value="0" {{ ($filters['is_published'] ?? '') === '0' ? 'selected' : '' }}>Draft</option>
                </select>
            </div>

            <!-- Action Buttons -->
            <div class="lg:col-span-2 flex items-center gap-2">
                <button type="submit" class="flex-1 px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow-sm transition-all text-center">
                    Filter
                </button>
                @if(!empty($filters['q']) || !empty($filters['level']) || !empty($filters['is_published']))
                    <a href="{{ route('admin.bem_members.index') }}" class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold" title="Reset Filter">
                        ✕
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Data Table Card -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50/70 text-slate-600 font-bold uppercase tracking-wider text-[11px]">
                        <th class="px-5 py-3.5">Urutan</th>
                        <th class="px-5 py-3.5">Foto & Nama Pengurus</th>
                        <th class="px-5 py-3.5">Jabatan & Departemen</th>
                        <th class="px-5 py-3.5">Tingkatan Hierarki</th>
                        <th class="px-5 py-3.5">Periode</th>
                        <th class="px-5 py-3.5">Status</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($members as $m)
                        <tr class="hover:bg-slate-50/60 transition-colors">
                            <td class="px-5 py-4 whitespace-nowrap font-mono text-slate-400 font-bold">
                                #{{ $m->sort_order }}
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl overflow-hidden bg-slate-100 border border-slate-200 shrink-0">
                                        <img src="{{ $m->photo_url }}" alt="{{ $m->name }}" class="w-full h-full object-cover">
                                    </div>
                                    <div>
                                        <a href="{{ route('admin.bem_members.edit', $m) }}" class="font-bold text-slate-900 hover:text-blue-900 block text-xs sm:text-sm">
                                            {{ $m->name }}
                                        </a>
                                        @if($m->description)
                                            <span class="text-[11px] text-slate-400 line-clamp-1 max-w-[220px]">{{ $m->description }}</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                <span class="font-bold text-slate-800 block">{{ $m->position }}</span>
                                <span class="text-[11px] text-slate-500 block">{{ $m->department ?: '-' }}</span>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                @php
                                    $badgeStyles = [
                                        'presma_wapresma' => 'bg-amber-100 text-amber-900 border-amber-200',
                                        'bph' => 'bg-blue-100 text-blue-900 border-blue-200',
                                        'minister' => 'bg-purple-100 text-purple-900 border-purple-200',
                                        'staff' => 'bg-slate-100 text-slate-800 border-slate-200',
                                    ];
                                @endphp
                                <span class="px-2.5 py-1 rounded-lg text-[11px] font-bold border {{ $badgeStyles[$m->level] ?? 'bg-slate-100 text-slate-700' }}">
                                    {{ $m->level_label }}
                                </span>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap font-mono text-slate-500">
                                {{ $m->period ?? '-' }}
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                @if($m->is_published)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Terbit
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-slate-200 text-slate-700">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                        Draft
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-right space-x-2">
                                <a href="{{ route('admin.bem_members.edit', $m) }}"
                                   class="inline-flex items-center px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs transition-colors">
                                    Edit
                                </a>

                                <form action="{{ route('admin.bem_members.destroy', $m) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengurus {{ addslashes($m->name) }} ({{ addslashes($m->position) }})?');">
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
                            <td colspan="7" class="px-5 py-12 text-center text-slate-400 text-xs">
                                <svg class="w-10 h-10 text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                <span class="font-bold block text-sm text-slate-600">Belum Ada Data Pengurus Kabinet</span>
                                <span class="text-slate-400 mt-1 block">Silakan tambahkan data pejabat dan staf BEM dengan menekan tombol di atas.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($members->hasPages())
            <div class="p-4 border-t border-slate-200 bg-slate-50/70">
                {{ $members->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
