<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminAuditLog;
use App\Models\WorkProgram;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WorkProgramAdminController extends Controller
{
    /**
     * Tampilkan daftar program kerja organisasi dalam dashboard.
     */
    public function index(Request $request): View
    {
        $query = WorkProgram::query()->latest('created_at');

        if ($search = trim((string) $request->input('q', ''))) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('organization', 'like', "%{$search}%")
                    ->orWhere('department', 'like', "%{$search}%");
            });
        }

        if ($pubStatus = $request->input('publication_status')) {
            if (in_array($pubStatus, ['draft', 'published'], true)) {
                $query->where('publication_status', $pubStatus);
            }
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $programs = $query->paginate(15)->withQueryString();

        return view('admin.programs.index', [
            'programs' => $programs,
            'filters' => [
                'q' => $request->input('q'),
                'publication_status' => $request->input('publication_status'),
                'status' => $request->input('status'),
            ],
        ]);
    }

    /**
     * Tampilkan formulir tambah program kerja baru.
     */
    public function create(): View
    {
        return view('admin.programs.create');
    }

    /**
     * Simpan program kerja baru ke basis data.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:200'],
            'organization' => ['required', 'string', 'max:150'],
            'department' => ['nullable', 'string', 'max:150'],
            'status' => ['required', 'string', 'max:64'],
            'publication_status' => ['required', 'string', 'in:draft,published'],
            'period' => ['nullable', 'string', 'max:100'],
            'description' => ['required', 'string'],
            'objectives' => ['nullable', 'string'],
            'targets' => ['nullable', 'string'],
            'indicators' => ['nullable', 'string'],
            'timeline' => ['nullable', 'string', 'max:150'],
            'pic' => ['nullable', 'string', 'max:150'],
        ], [
            'name.required' => 'Nama program kerja wajib diisi.',
            'organization.required' => 'Organisasi penanggung jawab wajib diisi.',
            'description.required' => 'Deskripsi program kerja wajib diisi.',
            'publication_status.required' => 'Status publikasi wajib dipilih.',
        ]);

        $slug = WorkProgram::generateUniqueSlug($validated['name']);

        // Format targets ke JSON jika diberikan
        $targetsJson = null;
        if (! empty($validated['targets'])) {
            $targetLines = array_values(array_filter(array_map('trim', explode("\n", $validated['targets']))));
            $targetsJson = json_encode($targetLines);
        }

        $program = WorkProgram::create([
            'name' => $validated['name'],
            'slug' => $slug,
            'organization' => $validated['organization'],
            'department' => $validated['department'] ?? null,
            'status' => $validated['status'],
            'publication_status' => $validated['publication_status'],
            'period' => $validated['period'] ?? null,
            'description' => $validated['description'],
            'objectives' => $validated['objectives'] ?? null,
            'targets' => $targetsJson,
            'indicators' => $validated['indicators'] ?? null,
            'timeline' => $validated['timeline'] ?? null,
            'pic' => $validated['pic'] ?? null,
            'published_at' => $validated['publication_status'] === 'published' ? now() : null,
        ]);

        AdminAuditLog::record(
            'program.create',
            'Program kerja "' . $program->name . '" berhasil dibuat.',
            $program,
            ['status' => $program->status, 'organization' => $program->organization]
        );

        return redirect()
            ->route('admin.programs.index')
            ->with('success', 'Program kerja "' . $validated['name'] . '" berhasil ditambahkan.');
    }

    /**
     * Tampilkan formulir edit program kerja.
     */
    public function edit(WorkProgram $program): View
    {
        return view('admin.programs.edit', [
            'program' => $program,
        ]);
    }

    /**
     * Perbarui data program kerja dalam basis data.
     */
    public function update(Request $request, WorkProgram $program): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:200'],
            'organization' => ['required', 'string', 'max:150'],
            'department' => ['nullable', 'string', 'max:150'],
            'status' => ['required', 'string', 'max:64'],
            'publication_status' => ['required', 'string', 'in:draft,published'],
            'period' => ['nullable', 'string', 'max:100'],
            'description' => ['required', 'string'],
            'objectives' => ['nullable', 'string'],
            'targets' => ['nullable', 'string'],
            'indicators' => ['nullable', 'string'],
            'timeline' => ['nullable', 'string', 'max:150'],
            'pic' => ['nullable', 'string', 'max:150'],
        ]);

        $slug = $program->slug;
        if ($program->name !== $validated['name']) {
            $slug = WorkProgram::generateUniqueSlug($validated['name'], $program->id);
        }

        $targetsJson = null;
        if (! empty($validated['targets'])) {
            $targetLines = array_values(array_filter(array_map('trim', explode("\n", $validated['targets']))));
            $targetsJson = json_encode($targetLines);
        }

        $publishedAt = $program->published_at;
        if ($validated['publication_status'] === 'published' && ! $publishedAt) {
            $publishedAt = now();
        }

        $program->update([
            'name' => $validated['name'],
            'slug' => $slug,
            'organization' => $validated['organization'],
            'department' => $validated['department'] ?? null,
            'status' => $validated['status'],
            'publication_status' => $validated['publication_status'],
            'period' => $validated['period'] ?? null,
            'description' => $validated['description'],
            'objectives' => $validated['objectives'] ?? null,
            'targets' => $targetsJson,
            'indicators' => $validated['indicators'] ?? null,
            'timeline' => $validated['timeline'] ?? null,
            'pic' => $validated['pic'] ?? null,
            'published_at' => $publishedAt,
        ]);

        AdminAuditLog::record(
            'program.update',
            'Program kerja "' . $program->name . '" berhasil diperbarui.',
            $program,
            ['status' => $program->status, 'organization' => $program->organization]
        );

        return redirect()
            ->route('admin.programs.index')
            ->with('success', 'Program kerja "' . $program->name . '" berhasil diperbarui.');
    }

    /**
     * Hapus data program kerja dari basis data.
     */
    public function destroy(WorkProgram $program): RedirectResponse
    {
        $id = $program->id;
        $name = $program->name;
        $program->delete();

        AdminAuditLog::record(
            'program.delete',
            'Program kerja "' . $name . '" berhasil dihapus dari sistem.',
            null,
            ['program_id' => $id, 'name' => $name]
        );

        return redirect()
            ->route('admin.programs.index')
            ->with('success', 'Program kerja "' . $name . '" berhasil dihapus.');
    }
}
