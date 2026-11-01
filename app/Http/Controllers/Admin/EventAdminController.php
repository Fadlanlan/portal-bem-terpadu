<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminAuditLog;
use App\Models\Event;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EventAdminController extends Controller
{
    /**
     * Tampilkan daftar agenda dan kegiatan kampus.
     */
    public function index(Request $request): View
    {
        $query = Event::query()->orderBy('started_at', 'desc');

        if ($search = trim((string) $request->input('q', ''))) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%")
                    ->orWhere('organizer', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('status')) {
            if (in_array($status, ['draft', 'published'], true)) {
                $query->where('status', $status);
            }
        }

        if ($category = $request->input('category')) {
            $query->where('category', $category);
        }

        $events = $query->paginate(15)->withQueryString();
        $categories = Event::select('category')->distinct()->pluck('category')->filter()->values();

        return view('admin.events.index', [
            'events' => $events,
            'categories' => $categories,
            'filters' => [
                'q' => $request->input('q'),
                'status' => $request->input('status'),
                'category' => $request->input('category'),
            ],
        ]);
    }

    /**
     * Tampilkan formulir tambah agenda baru.
     */
    public function create(): View
    {
        return view('admin.events.create');
    }

    /**
     * Simpan agenda baru ke basis data.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'category' => ['required', 'string', 'max:64'],
            'summary' => ['nullable', 'string', 'max:500'],
            'description' => ['required', 'string'],
            'location' => ['required', 'string', 'max:255'],
            'started_at' => ['required', 'date'],
            'ended_at' => ['required', 'date', 'after_or_equal:started_at'],
            'organizer' => ['required', 'string', 'max:150'],
            'registration_url' => ['nullable', 'url', 'max:255'],
            'is_registration_open' => ['nullable', 'boolean'],
            'speakers' => ['nullable', 'string'],
            'agenda_points' => ['nullable', 'string'],
            'status' => ['required', 'string', 'in:draft,published'],
        ], [
            'title.required' => 'Judul agenda wajib diisi.',
            'category.required' => 'Kategori agenda wajib diisi.',
            'description.required' => 'Deskripsi agenda wajib diisi.',
            'location.required' => 'Lokasi kegiatan wajib diisi.',
            'started_at.required' => 'Waktu mulai agenda wajib ditentukan.',
            'ended_at.required' => 'Waktu selesai agenda wajib ditentukan.',
            'ended_at.after_or_equal' => 'Waktu selesai tidak boleh lebih awal dari waktu mulai.',
            'organizer.required' => 'Penyelenggara agenda wajib diisi.',
            'registration_url.url' => 'Format tautan pendaftaran tidak valid.',
        ]);

        $slug = Event::generateUniqueSlug($validated['title']);

        $speakersJson = null;
        if (! empty($validated['speakers'])) {
            $speakers = array_values(array_filter(array_map('trim', explode("\n", $validated['speakers']))));
            $speakersJson = json_encode($speakers);
        }

        $agendaPointsJson = null;
        if (! empty($validated['agenda_points'])) {
            $points = array_values(array_filter(array_map('trim', explode("\n", $validated['agenda_points']))));
            $agendaPointsJson = json_encode($points);
        }

        $event = Event::create([
            'title' => $validated['title'],
            'slug' => $slug,
            'category' => $validated['category'],
            'summary' => $validated['summary'] ?? null,
            'description' => $validated['description'],
            'location' => $validated['location'],
            'started_at' => $validated['started_at'],
            'ended_at' => $validated['ended_at'],
            'organizer' => $validated['organizer'],
            'registration_url' => $validated['registration_url'] ?? null,
            'is_registration_open' => $request->boolean('is_registration_open'),
            'speakers' => $speakersJson,
            'agenda_points' => $agendaPointsJson,
            'status' => $validated['status'],
        ]);

        AdminAuditLog::record(
            'event.create',
            'Agenda kegiatan "' . $event->title . '" berhasil dibuat.',
            $event,
            ['status' => $event->status, 'category' => $event->category]
        );

        return redirect()
            ->route('admin.events.index')
            ->with('success', 'Agenda "' . $validated['title'] . '" berhasil ditambahkan.');
    }

    /**
     * Tampilkan formulir edit agenda kegiatan.
     */
    public function edit(Event $event): View
    {
        return view('admin.events.edit', [
            'event' => $event,
        ]);
    }

    /**
     * Perbarui data agenda kegiatan dalam basis data.
     */
    public function update(Request $request, Event $event): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'category' => ['required', 'string', 'max:64'],
            'summary' => ['nullable', 'string', 'max:500'],
            'description' => ['required', 'string'],
            'location' => ['required', 'string', 'max:255'],
            'started_at' => ['required', 'date'],
            'ended_at' => ['required', 'date', 'after_or_equal:started_at'],
            'organizer' => ['required', 'string', 'max:150'],
            'registration_url' => ['nullable', 'url', 'max:255'],
            'is_registration_open' => ['nullable', 'boolean'],
            'speakers' => ['nullable', 'string'],
            'agenda_points' => ['nullable', 'string'],
            'status' => ['required', 'string', 'in:draft,published'],
        ], [
            'ended_at.after_or_equal' => 'Waktu selesai tidak boleh lebih awal dari waktu mulai.',
        ]);

        $slug = $event->slug;
        if ($event->title !== $validated['title']) {
            $slug = Event::generateUniqueSlug($validated['title'], $event->id);
        }

        $speakersJson = null;
        if (! empty($validated['speakers'])) {
            $speakers = array_values(array_filter(array_map('trim', explode("\n", $validated['speakers']))));
            $speakersJson = json_encode($speakers);
        }

        $agendaPointsJson = null;
        if (! empty($validated['agenda_points'])) {
            $points = array_values(array_filter(array_map('trim', explode("\n", $validated['agenda_points']))));
            $agendaPointsJson = json_encode($points);
        }

        $event->update([
            'title' => $validated['title'],
            'slug' => $slug,
            'category' => $validated['category'],
            'summary' => $validated['summary'] ?? null,
            'description' => $validated['description'],
            'location' => $validated['location'],
            'started_at' => $validated['started_at'],
            'ended_at' => $validated['ended_at'],
            'organizer' => $validated['organizer'],
            'registration_url' => $validated['registration_url'] ?? null,
            'is_registration_open' => $request->boolean('is_registration_open'),
            'speakers' => $speakersJson,
            'agenda_points' => $agendaPointsJson,
            'status' => $validated['status'],
        ]);

        AdminAuditLog::record(
            'event.update',
            'Agenda kegiatan "' . $event->title . '" berhasil diperbarui.',
            $event,
            ['status' => $event->status, 'category' => $event->category]
        );

        return redirect()
            ->route('admin.events.index')
            ->with('success', 'Agenda "' . $event->title . '" berhasil diperbarui.');
    }

    /**
     * Hapus agenda kegiatan dari basis data.
     */
    public function destroy(Event $event): RedirectResponse
    {
        $id = $event->id;
        $title = $event->title;
        $event->delete();

        AdminAuditLog::record(
            'event.delete',
            'Agenda kegiatan "' . $title . '" berhasil dihapus dari sistem.',
            null,
            ['event_id' => $id, 'title' => $title]
        );

        return redirect()
            ->route('admin.events.index')
            ->with('success', 'Agenda "' . $title . '" berhasil dihapus.');
    }
}
