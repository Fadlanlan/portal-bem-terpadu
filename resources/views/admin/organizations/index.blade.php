@extends('layouts.admin')

@section('title', 'Manajemen Struktur Lembaga Ormawa')
@section('header_title', 'Kelola Organisasi Mahasiswa')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">Daftar Lembaga & Organisasi Mahasiswa</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                Kelola data BEM, MAM, Himpunan Mahasiswa Jurusan, serta Unit Kegiatan Mahasiswa (UKM).
            </p>
        </div>

        <a href="{{ route('admin.organizations.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs sm:text-sm shadow-md transition-colors self-start sm:self-auto">
            <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            <span>Tambah Lembaga Baru</span>
        </a>
    </div>

    <!-- Filter Controls -->
    <div class="bg-white rounded-2xl border border-slate-200 p-4 sm:p-5 shadow-sm">
        <form action="{{ route('admin.organizations.index') }}" method="GET" class="flex flex-col sm:flex-row items-center gap-3">
            <div class="w-full sm:flex-1 relative">
                <input type="text"
                       name="q"
                       value="{{ $filters['q'] ?? '' }}"
                       placeholder="Cari kode ormawa, nama organisasi, atau nama ketua..."
                       class="w-full pl-9 pr-4 py-2 text-xs sm:text-sm rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-amber-500 bg-white">
                <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>

            <div class="flex items-center gap-2 w-full sm:w-auto">
                <button type="submit" class="w-full sm:w-auto px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs sm:text-sm font-semibold transition-colors">
                    Filter
                </button>
                @if(!empty(array_filter($filters)))
                    <a href="{{ route('admin.organizations.index') }}" class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs sm:text-sm font-semibold transition-colors">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table Organizations -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs sm:text-sm text-slate-700 divide-y divide-slate-200">
                <thead class="bg-slate-50 text-slate-500 uppercase font-semibold text-[11px] tracking-wider">
                    <tr>
                        <th class="px-5 py-3.5">Urutan</th>
                        <th class="px-5 py-3.5">Kode & Nama Lembaga</th>
                        <th class="px-5 py-3.5">Tingkat / Peran</th>
                        <th class="px-5 py-3.5">Ketua & Periode</th>
                        <th class="px-5 py-3.5">Status</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($organizations as $org)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-5 py-4 whitespace-nowrap font-mono text-xs text-slate-500">
                                #{{ $org->sort_order }}
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-2.5">
                                    <span class="px-2 py-0.5 rounded-lg bg-blue-50 text-blue-900 font-bold font-mono text-xs border border-blue-200">
                                        {{ $org->code }}
                                    </span>
                                    <a href="{{ route('admin.organizations.edit', $org) }}" class="font-bold text-slate-900 hover:text-blue-900">
                                        {{ $org->name }}
                                    </a>
                                </div>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                <span class="text-slate-900 font-semibold block">{{ $org->level ?? '-' }}</span>
                                <span class="text-[11px] text-slate-500 block">{{ $org->role_type ?? '-' }}</span>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                <span class="text-slate-900 font-medium block">{{ $org->leader_name ?? '-' }}</span>
                                <span class="text-[11px] text-slate-400 block font-mono">{{ $org->period ?? '-' }}</span>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold {{ $org->status === 'published' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-700' }}">
                                    {{ $org->status === 'published' ? 'Terbit' : 'Draft' }}
                                </span>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-right space-x-2">
                                <a href="{{ route('admin.organizations.edit', $org) }}"
                                   class="inline-flex items-center px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs transition-colors">
                                    Edit
                                </a>

                                <form action="{{ route('admin.organizations.destroy', $org) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus lembaga ormawa {{ addslashes($org->name) }} ({{ $org->code }})? Tindakan ini tidak dapat dibatalkan.');">
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
                            <td colspan="6" class="px-5 py-12 text-center text-slate-400 text-sm">
                                Belum ada data lembaga ormawa yang ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($organizations->hasPages())
            <div class="p-4 border-t border-slate-200 bg-slate-50">
                {{ $organizations->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
