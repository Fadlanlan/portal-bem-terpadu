@extends('layouts.admin')

@section('title', 'Laporan Rekapitulasi Aspirasi')
@section('header_title', 'Laporan & Rekapitulasi Aspirasi Mahasiswa')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">Laporan & Rekapitulasi Aspirasi</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                Statistik penanganan, filter data periodik, dan ekspor berkas CSV aman untuk audit transparansi kemahasiswaan.
            </p>
        </div>

        <a href="{{ route('admin.reports.export', request()->query()) }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs sm:text-sm shadow-md transition-colors self-start sm:self-auto">
            <svg class="w-4 h-4 text-emerald-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            <span>Ekspor Hasil ke CSV</span>
        </a>
    </div>

    <!-- Ringkasan Kartu Metrik -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 sm:gap-4">
        <!-- Total -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
            <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wide block">Total Terfilter</span>
            <span class="text-xl sm:text-2xl font-extrabold text-slate-900 block font-mono mt-1">{{ $totalAspirations }}</span>
            <span class="text-[10px] text-slate-400 block mt-0.5">{{ $anonymousCount }} Laporan Anonim</span>
        </div>

        <!-- Diterima -->
        <div class="bg-white p-4 rounded-2xl border border-blue-200 shadow-sm bg-gradient-to-br from-white to-blue-50/40">
            <span class="text-[11px] font-bold text-blue-700 uppercase tracking-wide block">Diterima</span>
            <span class="text-xl sm:text-2xl font-extrabold text-blue-900 block font-mono mt-1">{{ $statusCounts['submitted'] ?? 0 }}</span>
            <span class="text-[10px] text-blue-600 block mt-0.5">Antrean awal</span>
        </div>

        <!-- Ditinjau -->
        <div class="bg-white p-4 rounded-2xl border border-indigo-200 shadow-sm bg-gradient-to-br from-white to-indigo-50/40">
            <span class="text-[11px] font-bold text-indigo-700 uppercase tracking-wide block">Ditinjau</span>
            <span class="text-xl sm:text-2xl font-extrabold text-indigo-900 block font-mono mt-1">{{ $statusCounts['under_review'] ?? 0 }}</span>
            <span class="text-[10px] text-indigo-600 block mt-0.5">Kajian advokasi</span>
        </div>

        <!-- Ditindaklanjuti -->
        <div class="bg-white p-4 rounded-2xl border border-amber-200 shadow-sm bg-gradient-to-br from-white to-amber-50/40">
            <span class="text-[11px] font-bold text-amber-700 uppercase tracking-wide block">Tindak Lanjut</span>
            <span class="text-xl sm:text-2xl font-extrabold text-amber-900 block font-mono mt-1">{{ $statusCounts['in_progress'] ?? 0 }}</span>
            <span class="text-[10px] text-amber-600 block mt-0.5">Proses penyelesaian</span>
        </div>

        <!-- Selesai -->
        <div class="bg-white p-4 rounded-2xl border border-emerald-200 shadow-sm bg-gradient-to-br from-white to-emerald-50/40">
            <span class="text-[11px] font-bold text-emerald-700 uppercase tracking-wide block">Selesai</span>
            <span class="text-xl sm:text-2xl font-extrabold text-emerald-900 block font-mono mt-1">{{ $statusCounts['resolved'] ?? 0 }}</span>
            <span class="text-[10px] text-emerald-600 block mt-0.5">Terselesaikan</span>
        </div>

        <!-- Ditolak -->
        <div class="bg-white p-4 rounded-2xl border border-rose-200 shadow-sm bg-gradient-to-br from-white to-rose-50/40">
            <span class="text-[11px] font-bold text-rose-700 uppercase tracking-wide block">Ditolak</span>
            <span class="text-xl sm:text-2xl font-extrabold text-rose-900 block font-mono mt-1">{{ $statusCounts['rejected'] ?? 0 }}</span>
            <span class="text-[10px] text-rose-600 block mt-0.5">Tidak relevan / batal</span>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="bg-white rounded-2xl border border-slate-200 p-4 sm:p-5 shadow-sm space-y-4">
        <form action="{{ route('admin.reports.index') }}" method="GET" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                <!-- Tanggal Mulai -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Tanggal Mulai</label>
                    <input type="date"
                           name="start_date"
                           value="{{ $filters['start_date'] ?? '' }}"
                           class="w-full rounded-xl border border-slate-300 py-2 px-3 text-xs sm:text-sm text-slate-900 bg-white focus:outline-none focus:ring-2 focus:ring-amber-500">
                </div>

                <!-- Tanggal Akhir -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Tanggal Akhir</label>
                    <input type="date"
                           name="end_date"
                           value="{{ $filters['end_date'] ?? '' }}"
                           class="w-full rounded-xl border border-slate-300 py-2 px-3 text-xs sm:text-sm text-slate-900 bg-white focus:outline-none focus:ring-2 focus:ring-amber-500">
                </div>

                <!-- Kategori -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Kategori Laporan</label>
                    <select name="category" class="w-full rounded-xl border border-slate-300 py-2 px-3 text-xs sm:text-sm text-slate-900 bg-white focus:outline-none focus:ring-2 focus:ring-amber-500">
                        <option value="">-- Semua Kategori --</option>
                        @foreach($categories as $catVal => $catLabel)
                            <option value="{{ $catVal }}" {{ ($filters['category'] ?? '') === $catVal ? 'selected' : '' }}>{{ $catLabel }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Status Penanganan</label>
                    <select name="status" class="w-full rounded-xl border border-slate-300 py-2 px-3 text-xs sm:text-sm text-slate-900 bg-white focus:outline-none focus:ring-2 focus:ring-amber-500">
                        <option value="">-- Semua Status --</option>
                        @foreach($statuses as $stVal => $stLabel)
                            <option value="{{ $stVal }}" {{ ($filters['status'] ?? '') === $stVal ? 'selected' : '' }}>{{ $stLabel }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex items-center justify-between gap-3 pt-2">
                <div class="text-xs text-slate-500">
                    Menampilkan <strong>{{ $aspirations->total() }}</strong> baris laporan terfilter.
                </div>

                <div class="flex items-center gap-2">
                    <button type="submit" class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs sm:text-sm font-semibold transition-colors">
                        Terapkan Filter
                    </button>
                    @if(!empty(array_filter($filters)))
                        <a href="{{ route('admin.reports.index') }}" class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs sm:text-sm font-semibold transition-colors">
                            Reset
                        </a>
                    @endif
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
                        <th class="px-5 py-3.5">No. Tiket</th>
                        <th class="px-5 py-3.5">Kategori</th>
                        <th class="px-5 py-3.5">Subjek & Pesan</th>
                        <th class="px-5 py-3.5">Tipe Laporan</th>
                        <th class="px-5 py-3.5">Status</th>
                        <th class="px-5 py-3.5">Waktu Masuk</th>
                        <th class="px-5 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($aspirations as $asp)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-5 py-4 whitespace-nowrap font-mono font-bold text-slate-900">
                                {{ $asp->ticket_number }}
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                <span class="px-2.5 py-0.5 rounded-lg text-xs bg-slate-100 text-slate-800 font-medium">
                                    {{ $asp->category_label }}
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                <a href="{{ route('admin.aspirations.show', $asp) }}" class="font-bold text-slate-900 hover:text-blue-900 line-clamp-1">
                                    {{ $asp->subject }}
                                </a>
                                <p class="text-xs text-slate-500 line-clamp-1 mt-0.5">{{ $asp->message }}</p>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded text-[11px] font-semibold {{ $asp->is_anonymous ? 'bg-purple-100 text-purple-800' : 'bg-slate-100 text-slate-700' }}">
                                    {{ $asp->is_anonymous ? 'Anonim' : 'Terbuka' }}
                                </span>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                <x-badge :type="$asp->status_badge_type" class="text-xs">
                                    {{ $asp->status_label }}
                                </x-badge>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-xs text-slate-500">
                                {{ $asp->submitted_at ? $asp->submitted_at->format('d/m/Y H:i') : '-' }}
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-right">
                                <a href="{{ route('admin.aspirations.show', $asp) }}"
                                   class="inline-flex items-center px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs transition-colors">
                                    Buka Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center text-slate-400 text-sm">
                                Tidak ada data aspirasi mahasiswa yang cocok dengan kriteria filter yang dipilih.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($aspirations->hasPages())
            <div class="p-4 border-t border-slate-200 bg-slate-50">
                {{ $aspirations->links() }}
            </div>
        @endif
    </div>

    <!-- Security & Privacy Guard Notice -->
    <div class="p-4 rounded-2xl bg-slate-100 border border-slate-200 text-xs text-slate-600 space-y-1">
        <strong class="text-slate-800 block">Protokol Keamanan Ekspor CSV:</strong>
        <p>• Data pelapor anonim (nama, surel, nomor telepon/NIM, alamat IP) secara ketat dikeluarkan dari kolom berkas CSV.</p>
        <p>• Seluruh sel keluaran CSV otomatis dinetralisir dari potensi <em>CSV Formula Injection</em> sehingga aman saat dibuka pada Microsoft Excel, LibreOffice, maupun Google Sheets.</p>
    </div>
</div>
@endsection
