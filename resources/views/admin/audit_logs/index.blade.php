@extends('layouts.admin')

@section('title', 'Log Audit Aktivitas Sistem')
@section('header_title', 'Audit Log Aktivitas Pengelola')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">Catatan Audit Keamanan & Aktivitas</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                Rekaman jejak aktivitas sensitif login, perbaruan status aspirasi, pengelolaan konten, dan perubahan hak akses admin.
            </p>
        </div>

        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl bg-slate-900 text-amber-400 font-mono text-xs font-bold border border-slate-800 self-start sm:self-auto shadow-sm">
            <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
            <span>Audit Trail Mode: Read-Only</span>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="bg-white rounded-2xl border border-slate-200 p-4 sm:p-5 shadow-sm space-y-4">
        <form action="{{ route('admin.audit_logs.index') }}" method="GET" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                <!-- Kata Kunci -->
                <div class="lg:col-span-2 relative">
                    <input type="text"
                           name="q"
                           value="{{ $filters['q'] ?? '' }}"
                           placeholder="Cari deskripsi, aksi, atau IP address..."
                           class="w-full pl-9 pr-4 py-2 text-xs sm:text-sm rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-amber-500 bg-white">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>

                <!-- Aksi -->
                <div>
                    <select name="action" class="w-full rounded-xl border border-slate-300 py-2 px-3 text-xs sm:text-sm text-slate-900 bg-white focus:outline-none focus:ring-2 focus:ring-amber-500">
                        <option value="">-- Semua Jenis Aksi --</option>
                        @foreach($distinctActions as $act)
                            <option value="{{ $act }}" {{ ($filters['action'] ?? '') === $act ? 'selected' : '' }}>{{ $act }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Pengguna -->
                <div>
                    <select name="user_id" class="w-full rounded-xl border border-slate-300 py-2 px-3 text-xs sm:text-sm text-slate-900 bg-white focus:outline-none focus:ring-2 focus:ring-amber-500">
                        <option value="">-- Semua Pengelola --</option>
                        @foreach($adminUsers as $admin)
                            <option value="{{ $admin->id }}" {{ ($filters['user_id'] ?? '') == $admin->id ? 'selected' : '' }}>{{ $admin->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Rentang Tanggal Mulai -->
                <div>
                    <input type="date"
                           name="start_date"
                           value="{{ $filters['start_date'] ?? '' }}"
                           title="Tanggal Mulai"
                           class="w-full rounded-xl border border-slate-300 py-2 px-3 text-xs sm:text-sm text-slate-900 bg-white focus:outline-none focus:ring-2 focus:ring-amber-500">
                </div>
            </div>

            <div class="flex items-center justify-between gap-3 pt-2">
                <div class="text-xs text-slate-500">
                    Menampilkan <strong>{{ $logs->total() }}</strong> catatan audit aktivitas.
                </div>

                <div class="flex items-center gap-2">
                    <button type="submit" class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs sm:text-sm font-semibold transition-colors">
                        Terapkan Filter
                    </button>
                    @if(!empty(array_filter($filters)))
                        <a href="{{ route('admin.audit_logs.index') }}" class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs sm:text-sm font-semibold transition-colors">
                            Reset
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- Table Audit Logs -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs sm:text-sm text-slate-700 divide-y divide-slate-200">
                <thead class="bg-slate-50 text-slate-500 uppercase font-semibold text-[11px] tracking-wider">
                    <tr>
                        <th class="px-5 py-3.5">Waktu Kejadian</th>
                        <th class="px-5 py-3.5">Pelaku / Pengelola</th>
                        <th class="px-5 py-3.5">Jenis Aksi</th>
                        <th class="px-5 py-3.5">Rincian Deskripsi</th>
                        <th class="px-5 py-3.5">Alamat IP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($logs as $log)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-5 py-4 whitespace-nowrap text-xs text-slate-500 font-mono">
                                <div>{{ $log->created_at ? $log->created_at->format('d/m/Y H:i:s') : '-' }}</div>
                                <span class="text-[10px] text-slate-400">{{ $log->created_at ? $log->created_at->diffForHumans() : '' }}</span>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                @if($log->user)
                                    <div class="font-bold text-slate-900">{{ $log->user->name }}</div>
                                    <span class="text-[11px] text-slate-400 font-mono">{{ $log->user->email }}</span>
                                @else
                                    <span class="text-xs text-slate-400 italic">Sistem / Pengguna Terhapus</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-lg text-[11px] font-mono font-bold bg-slate-100 text-slate-800 border border-slate-200">
                                    {{ $log->action }}
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                <div class="font-medium text-slate-900 leading-snug">{{ $log->description }}</div>
                                @if(!empty($log->metadata))
                                    <details class="mt-1 text-[11px] text-slate-500">
                                        <summary class="cursor-pointer text-amber-700 hover:underline font-mono">Lihat Metadata Audit</summary>
                                        <pre class="mt-1.5 p-2 rounded-lg bg-slate-900 text-amber-300 font-mono text-[10px] overflow-x-auto max-w-md">{{ json_encode($log->metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                                    </details>
                                @endif
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap text-xs text-slate-500 font-mono">
                                {{ $log->ip_address ?: '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-12 text-center text-slate-400 text-sm">
                                Belum ada riwayat audit log aktivitas yang tercatat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div class="p-4 border-t border-slate-200 bg-slate-50">
                {{ $logs->links() }}
            </div>
        @endif
    </div>

    <!-- Security Information Notice -->
    <div class="p-4 rounded-2xl bg-slate-100 border border-slate-200 text-xs text-slate-600 space-y-1">
        <strong class="text-slate-800 block">Pemberitahuan Batasan Teknis Audit Log:</strong>
        <p>• Rekaman audit log bersifat *append-only* di antarmuka sistem dan tidak dapat dimodifikasi atau dihapus oleh pengelola.</p>
        <p>• Data kata sandi, token otentikasi, kode pelacakan rahasia tiket, dan isi pesan sensitif secara otomatis difilter dan tidak disimpan pada metadata audit log.</p>
    </div>
</div>
@endsection
