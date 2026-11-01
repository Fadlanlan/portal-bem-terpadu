@extends('layouts.admin')

@section('title', 'Sunting Agenda: ' . $event->title)
@section('header_title', 'Sunting Agenda Kegiatan')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.events.index') }}"
           class="inline-flex items-center gap-2 text-xs sm:text-sm font-semibold text-slate-600 hover:text-slate-900 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            <span>Kembali ke Daftar Agenda</span>
        </a>

        @if($event->status === 'published')
            <a href="{{ route('events.show', $event->slug) }}" target="_blank"
               class="text-xs text-amber-700 font-semibold hover:underline inline-flex items-center gap-1">
                <span>Lihat Tampilan Publik</span>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
            </a>
        @endif
    </div>

    <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-6">
        <div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900">Sunting Agenda Kegiatan</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Perbarui jadwal pelaksanaan, lokasi, narasumber, rundown, dan status publikasi agenda.
            </p>
        </div>

        <form action="{{ route('admin.events.update', $event) }}" method="POST" class="space-y-6" novalidate>
            @csrf
            @method('PUT')

            <!-- Judul Agenda -->
            <div>
                <label for="title" class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-1.5">
                    Judul Agenda Kegiatan <span class="text-rose-500">*</span>
                </label>
                <input type="text"
                       id="title"
                       name="title"
                       value="{{ old('title', $event->title) }}"
                       required
                       maxlength="200"
                       class="w-full rounded-xl border border-slate-300 py-3 px-4 text-sm font-semibold text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-500 @error('title') border-rose-500 ring-1 ring-rose-500 @enderror">
                @error('title')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Kategori & Penyelenggara -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="category" class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-1.5">
                        Kategori Kegiatan <span class="text-rose-500">*</span>
                    </label>
                    <input type="text"
                           id="category"
                           name="category"
                           value="{{ old('category', $event->category) }}"
                           required
                           class="w-full rounded-xl border border-slate-300 py-2.5 px-3.5 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-500 @error('category') border-rose-500 @enderror">
                    @error('category')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="organizer" class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-1.5">
                        Penyelenggara Kegiatan <span class="text-rose-500">*</span>
                    </label>
                    <input type="text"
                           id="organizer"
                           name="organizer"
                           value="{{ old('organizer', $event->organizer) }}"
                           required
                           class="w-full rounded-xl border border-slate-300 py-2.5 px-3.5 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-500 @error('organizer') border-rose-500 @enderror">
                    @error('organizer')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Tanggal & Waktu Mulai / Selesai -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="started_at" class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-1.5">
                        Waktu Mulai <span class="text-rose-500">*</span>
                    </label>
                    <input type="datetime-local"
                           id="started_at"
                           name="started_at"
                           value="{{ old('started_at', $event->started_at?->format('Y-m-d\TH:i')) }}"
                           required
                           class="w-full rounded-xl border border-slate-300 py-2.5 px-3.5 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-500 @error('started_at') border-rose-500 @enderror">
                    @error('started_at')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="ended_at" class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-1.5">
                        Waktu Selesai <span class="text-rose-500">*</span>
                    </label>
                    <input type="datetime-local"
                           id="ended_at"
                           name="ended_at"
                           value="{{ old('ended_at', $event->ended_at?->format('Y-m-d\TH:i')) }}"
                           required
                           class="w-full rounded-xl border border-slate-300 py-2.5 px-3.5 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-500 @error('ended_at') border-rose-500 @enderror">
                    @error('ended_at')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Lokasi Kegiatan -->
            <div>
                <label for="location" class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-1.5">
                    Lokasi / Tempat Kegiatan <span class="text-rose-500">*</span>
                </label>
                <input type="text"
                       id="location"
                       name="location"
                       value="{{ old('location', $event->location) }}"
                       required
                       class="w-full rounded-xl border border-slate-300 py-2.5 px-3.5 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-500 @error('location') border-rose-500 @enderror">
                @error('location')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Ringkasan Singkat -->
            <div>
                <label for="summary" class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-1.5">
                    Ringkasan Singkat (Maks. 500 Karakter)
                </label>
                <textarea id="summary"
                          name="summary"
                          rows="2"
                          maxlength="500"
                          class="w-full rounded-xl border border-slate-300 py-2.5 px-3.5 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-500">{{ old('summary', $event->summary) }}</textarea>
            </div>

            <!-- Deskripsi Lengkap -->
            <div>
                <label for="description" class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-1.5">
                    Deskripsi Lengkap Kegiatan <span class="text-rose-500">*</span>
                </label>
                <textarea id="description"
                          name="description"
                          rows="6"
                          required
                          class="w-full rounded-xl border border-slate-300 py-3 px-4 text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-amber-500 @error('description') border-rose-500 @enderror">{{ old('description', $event->description) }}</textarea>
                @error('description')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Narasumber & Rundown Agenda -->
            @php
                $speakersVal = is_array($event->speakers) ? implode("\n", $event->speakers) : (string) $event->speakers;
                $agendaVal = is_array($event->agenda_points) ? implode("\n", $event->agenda_points) : (string) $event->agenda_points;
            @endphp
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="speakers" class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-1.5">
                        Narasumber / Pengisi Acara
                    </label>
                    <textarea id="speakers"
                              name="speakers"
                              rows="3"
                              placeholder="1 nama narasumber per baris..."
                              class="w-full rounded-xl border border-slate-300 py-2.5 px-3.5 text-xs text-slate-900 font-mono focus:outline-none focus:ring-2 focus:ring-amber-500">{{ old('speakers', $speakersVal) }}</textarea>
                </div>

                <div>
                    <label for="agenda_points" class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-1.5">
                        Poin Rundown Acara
                    </label>
                    <textarea id="agenda_points"
                              name="agenda_points"
                              rows="3"
                              placeholder="1 poin agenda per baris..."
                              class="w-full rounded-xl border border-slate-300 py-2.5 px-3.5 text-xs text-slate-900 font-mono focus:outline-none focus:ring-2 focus:ring-amber-500">{{ old('agenda_points', $agendaVal) }}</textarea>
                </div>
            </div>

            <!-- Tautan Registrasi & Status Terbuka -->
            <div class="bg-slate-50 rounded-2xl p-4 sm:p-5 border border-slate-200 space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 items-center">
                    <div>
                        <label for="registration_url" class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-1.5">
                            Tautan Registrasi Peserta (Opsional)
                        </label>
                        <input type="url"
                               id="registration_url"
                               name="registration_url"
                               value="{{ old('registration_url', $event->registration_url) }}"
                               class="w-full rounded-xl border border-slate-300 py-2.5 px-3.5 text-sm text-slate-900 bg-white focus:outline-none focus:ring-2 focus:ring-amber-500 @error('registration_url') border-rose-500 @enderror">
                        @error('registration_url')
                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="sm:pt-5">
                        <label class="inline-flex items-center gap-3 cursor-pointer">
                            <input type="checkbox"
                                   name="is_registration_open"
                                   value="1"
                                   {{ old('is_registration_open', $event->is_registration_open) ? 'checked' : '' }}
                                   class="w-4 h-4 text-blue-900 rounded border-slate-300 focus:ring-amber-500">
                            <span class="text-xs font-semibold text-slate-800">Buka Pendaftaran untuk Mahasiswa</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Status Publikasi -->
            <div>
                <label for="status" class="block text-xs font-bold text-slate-800 uppercase tracking-wider mb-1.5">
                    Status Publikasi Agenda <span class="text-rose-500">*</span>
                </label>
                <select id="status"
                        name="status"
                        required
                        class="w-full sm:w-64 rounded-xl border border-slate-300 py-2.5 px-3.5 text-sm text-slate-900 bg-white focus:outline-none focus:ring-2 focus:ring-amber-500">
                    <option value="published" {{ old('status', $event->status) === 'published' ? 'selected' : '' }}>Terbitkan di Publik (Published)</option>
                    <option value="draft" {{ old('status', $event->status) === 'draft' ? 'selected' : '' }}>Simpan sebagai Draft</option>
                </select>
            </div>

            <!-- Tombol Aksi -->
            <div class="flex items-center justify-between pt-4 border-t border-slate-200">
                <div>
                    <!-- Tombol Hapus -->
                    <button type="button"
                            onclick="if(confirm('Apakah Anda yakin ingin menghapus agenda \'{{ addslashes($event->title) }}\'? Tindakan ini tidak dapat dibatalkan.')) { document.getElementById('delete-event-form').submit(); }"
                            class="px-4 py-2.5 rounded-xl border border-rose-200 text-rose-600 hover:bg-rose-50 text-xs sm:text-sm font-semibold transition-colors">
                        Hapus Agenda
                    </button>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.events.index') }}"
                       class="px-5 py-2.5 rounded-xl border border-slate-300 text-xs sm:text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
                        Batal
                    </a>
                    <button type="submit"
                            class="px-6 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs sm:text-sm shadow-md transition-colors">
                        Simpan Perubahan
                    </button>
                </div>
            </div>
        </form>

        <form id="delete-event-form" action="{{ route('admin.events.destroy', $event) }}" method="POST" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    </div>
</div>
@endsection
