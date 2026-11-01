@extends('layouts.admin')

@section('title', 'Manajemen Aspirasi')
@section('header_title', 'Kelola Aspirasi Mahasiswa')

@section('content')
<div class="space-y-6">
    <!-- Header Summary & Quick Info -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">Daftar Aspirasi Masuk</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Pantau, verifikasi, dan tindak lanjuti aspirasi, keluhan sarana, dan permohonan advokasi mahasiswa.
            </p>
        </div>
    </div>

    <!-- Filters & Search Form -->
    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm space-y-4">
        <form action="{{ route('admin.aspirations.index') }}" method="GET" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                <!-- Search -->
                <div class="lg:col-span-2">
                    <label for="q" class="block text-xs font-bold text-slate-700 mb-1">Cari Tiket / Subjek</label>
                    <input type="text"
                           id="q"
                           name="q"
                           value="{{ $filters['q'] ?? '' }}"
                           placeholder="Nomor tiket atau judul aspirasi..."
                           class="w-full rounded-xl border border-slate-300 py-2 px-3 text-xs sm:text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-500">
                </div>

                <!-- Status Filter -->
                <div>
                    <label for="status" class="block text-xs font-bold text-slate-700 mb-1">Filter Status</label>
                    <select id="status" name="status" class="w-full rounded-xl border border-slate-300 py-2 px-3 text-xs sm:text-sm text-slate-900 bg-white focus:outline-none focus:ring-2 focus:ring-amber-500">
                        <option value="">-- Semua Status --</option>
                        @foreach($statuses as $val => $lbl)
                            <option value="{{ $val }}" {{ ($filters['status'] ?? '') === $val ? 'selected' : '' }}>
                                {{ $lbl }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Category Filter -->
                <div>
                    <label for="category" class="block text-xs font-bold text-slate-700 mb-1">Kategori</label>
                    <select id="category" name="category" class="w-full rounded-xl border border-slate-300 py-2 px-3 text-xs sm:text-sm text-slate-900 bg-white focus:outline-none focus:ring-2 focus:ring-amber-500">
                        <option value="">-- Semua Kategori --</option>
                        @foreach($categories as $val => $lbl)
                            <option value="{{ $val }}" {{ ($filters['category'] ?? '') === $val ? 'selected' : '' }}>
                                {{ $lbl }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Tanggal Dari -->
                <div>
                    <label for="date_from" class="block text-xs font-bold text-slate-700 mb-1">Dari Tanggal</label>
                    <input type="date"
                           id="date_from"
                           name="date_from"
                           value="{{ $filters['date_from'] ?? '' }}"
                           class="w-full rounded-xl border border-slate-300 py-2 px-3 text-xs sm:text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-500">
                </div>
            </div>

            <div class="flex items-center justify-between pt-2 border-t border-slate-100">
                <span class="text-xs text-slate-500">
                    Menampilkan total <strong>{{ $aspirations->total() }}</strong> laporan aspirasi
                </span>
                <div class="flex items-center gap-2">
                    @if(!empty(array_filter($filters)))
                        <a href="{{ route('admin.aspirations.index') }}" class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition-colors">
                            Reset Filter
                        </a>
                    @endif
                    <button type="submit" class="px-4 py-1.5 rounded-lg bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold shadow-sm transition-colors">
                        Terapkan Filter
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Table Aspirations -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs sm:text-sm text-slate-700 divide-y divide-slate-200">
                <thead class="bg-slate-50 text-slate-500 uppercase font-semibold text-[11px] tracking-wider">
                    <tr>
                        <th class="px-5 py-3.5">Nomor Tiket</th>
                        <th class="px-5 py-3.5">Kategori</th>
                        <th class="px-5 py-3.5">Subjek Laporan</th>
                        <th class="px-5 py-3.5">Pelapor</th>
                        <th class="px-5 py-3.5">Status</th>
                        <th class="px-5 py-3.5">Waktu Masuk</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($aspirations as $asp)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-5 py-4 font-mono font-bold text-slate-900 whitespace-nowrap">
                                {{ $asp->ticket_number }}
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded text-xs bg-slate-100 text-slate-700 font-medium">
                                    {{ $asp->category_label }}
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                <span class="font-bold text-slate-900 line-clamp-1 block max-w-xs sm:max-w-md">
                                    {{ $asp->subject }}
                                </span>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                @if($asp->is_anonymous)
                                    <span class="inline-flex items-center gap-1 text-slate-500 italic text-xs font-medium">
                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path></svg>
                                        <span>Anonim</span>
                                    </span>
                                @else
                                    <span class="font-semibold text-slate-800 text-xs block">{{ $asp->reporter_name }}</span>
                                    <span class="text-[11px] text-slate-400 block">{{ $asp->reporter_student_id ?: 'NIM Tidak Ada' }}</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                <x-badge :type="$asp->status_badge_type" class="text-xs px-2.5 py-0.5">
                                    {{ $asp->status_label }}
                                </x-badge>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-xs text-slate-500">
                                {{ $asp->submitted_at?->format('d/m/Y H:i') ?? '-' }}
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-right">
                                <a href="{{ route('admin.aspirations.show', $asp) }}"
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow-sm transition-colors">
                                    <span>Tindak Lanjut</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center text-slate-500">
                                <svg class="w-10 h-10 text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                <span class="font-semibold block text-sm">Tidak Ada Data Aspirasi</span>
                                <span class="text-xs text-slate-400">Tidak ada aspirasi yang sesuai dengan kriteria filter saat ini.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($aspirations->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $aspirations->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
