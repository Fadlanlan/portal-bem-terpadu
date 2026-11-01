@extends('layouts.app')

@section('title', 'Program Kerja')
@section('meta_description', 'Daftar program kerja kementerian dan biro Badan Eksekutif Mahasiswa (BEM) periode aktif.')

@section('content')
    <div class="text-white py-12 border-b" style="background-color: var(--theme-primary); border-bottom-color: var(--theme-secondary);">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-breadcrumb :items="['Program Kerja' => null]" />
            <div class="mt-4 max-w-3xl">
                <span class="text-xs font-bold uppercase tracking-wider" style="color: var(--theme-accent);">Transparansi Kinerja</span>
                <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight mt-1">Program Kerja Mahasiswa</h1>
                <p class="text-sm sm:text-base text-slate-300 mt-2">
                    Dokumentasi rencana aksi, kegiatan berjalan, serta evaluasi capaian program kementerian BEM untuk kepentingan mahasiswa.
                </p>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <!-- Filters -->
        <div class="bg-white rounded-2xl border border-slate-200 p-4 sm:p-6 mb-8 shadow-sm space-y-4">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <!-- Status Filter -->
                <div class="flex items-center flex-wrap gap-2">
                    <span class="text-xs font-semibold text-slate-500 mr-1">Status:</span>
                    @foreach($statuses as $st)
                        <a href="{{ route('programs.index', ['status' => $st, 'departemen' => $currentDept]) }}"
                           class="px-3 py-1.5 rounded-lg text-xs font-medium transition-colors {{ $currentStatus === $st ? 'bg-slate-900 text-white font-semibold' : 'bg-slate-100 text-slate-700 hover:bg-slate-200' }}">
                            {{ $st }}
                        </a>
                    @endforeach
                </div>

                <!-- Departemen Filter -->
                <div class="flex items-center flex-wrap gap-2">
                    <span class="text-xs font-semibold text-slate-500 mr-1">Bidang:</span>
                    <select onchange="window.location.href = this.value"
                            aria-label="Filter berdasarkan bidang/departemen"
                            class="text-xs sm:text-sm rounded-lg border border-slate-300 py-1.5 px-3 bg-white text-slate-800 focus:outline-none focus:ring-2 focus:ring-amber-500">
                        @foreach($departments as $dept)
                            <option value="{{ route('programs.index', ['departemen' => $dept, 'status' => $currentStatus]) }}"
                                {{ $currentDept === $dept ? 'selected' : '' }}>
                                {{ $dept }}
                            </option>
                        @endforeach
                    </select>

                    @if($currentStatus !== 'Semua' || $currentDept !== 'Semua')
                        <a href="{{ route('programs.index') }}" class="px-2.5 py-1.5 rounded-lg bg-slate-100 text-slate-500 hover:text-slate-800 hover:bg-slate-200 text-xs font-medium" title="Reset filter">
                            Reset
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Programs Grid -->
        @if(count($programs) > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
                @foreach($programs as $prog)
                    <div class="bg-white rounded-2xl border border-slate-200 p-6 flex flex-col justify-between hover:shadow-md hover:border-slate-300 transition-all group">
                        <div class="space-y-4">
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-xs font-bold text-slate-500 uppercase tracking-wide truncate">
                                    {{ $prog['department'] }}
                                </span>
                                <x-badge :type="$prog['status']">{{ $prog['status'] }}</x-badge>
                            </div>

                            <div>
                                <h2 class="text-lg font-bold text-slate-900 group-hover:text-blue-900 transition-colors">
                                    <a href="{{ route('programs.show', $prog['slug']) }}">
                                        {{ $prog['name'] }}
                                    </a>
                                </h2>
                                <p class="text-xs text-slate-400 font-medium mt-0.5">{{ $prog['organization'] }}</p>
                            </div>

                            <p class="text-xs sm:text-sm text-slate-600 line-clamp-3 leading-relaxed">
                                {{ $prog['description'] }}
                            </p>
                        </div>

                        <div class="pt-4 mt-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                            <span class="truncate max-w-[150px]">{{ $prog['period'] }}</span>
                            <a href="{{ route('programs.show', $prog['slug']) }}"
                               class="font-semibold text-amber-700 hover:text-amber-800 flex items-center gap-1 shrink-0">
                                <span>Rincian & Indikator</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Tautan paginasi hanya ditampilkan pada hasil basis data (paginator) --}}
            @if($programs instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator)
                <div class="mt-10">
                    {{ $programs->links() }}
                </div>
            @endif
        @else
            <x-empty-state
                title="Tidak Ada Program Kerja yang Sesuai"
                description="Tidak ditemukan program kerja untuk status atau bidang kementerian yang dipilih."
                :actionUrl="route('programs.index')"
                actionText="Tampilkan Semua Program Kerja"
            />
        @endif
    </div>
@endsection
