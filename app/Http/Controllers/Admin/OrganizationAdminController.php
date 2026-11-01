<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminAuditLog;
use App\Models\Organization;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrganizationAdminController extends Controller
{
    /**
     * Tampilkan daftar organisasi mahasiswa dalam dashboard.
     */
    public function index(Request $request): View
    {
        $query = Organization::query()->orderBy('sort_order', 'asc');

        if ($search = trim((string) $request->input('q', ''))) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('leader_name', 'like', "%{$search}%");
            });
        }

        if ($code = $request->input('code')) {
            $query->where('code', $code);
        }

        $organizations = $query->paginate(15)->withQueryString();

        return view('admin.organizations.index', [
            'organizations' => $organizations,
            'filters' => [
                'q' => $request->input('q'),
                'code' => $request->input('code'),
            ],
        ]);
    }

    /**
     * Tampilkan formulir tambah entitas ormawa.
     */
    public function create(): View
    {
        return view('admin.organizations.create');
    }

    /**
     * Simpan entitas ormawa baru ke basis data.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:32'],
            'name' => ['required', 'string', 'max:200'],
            'level' => ['nullable', 'string', 'max:150'],
            'role_type' => ['nullable', 'string', 'max:150'],
            'description' => ['required', 'string'],
            'leader_name' => ['nullable', 'string', 'max:150'],
            'period' => ['nullable', 'string', 'max:100'],
            'functions' => ['nullable', 'string'],
            'structure_overview' => ['nullable', 'string'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'status' => ['required', 'string', 'in:draft,published'],
        ], [
            'code.required' => 'Singkatan kode lembaga wajib diisi (misal: BEM, MAM, HIMA).',
            'name.required' => 'Nama lengkap organisasi wajib diisi.',
            'description.required' => 'Deskripsi organisasi wajib diisi.',
        ]);

        $slug = Organization::generateUniqueSlug($validated['name']);

        $functionsJson = null;
        if (! empty($validated['functions'])) {
            $items = array_values(array_filter(array_map('trim', explode("\n", $validated['functions']))));
            $functionsJson = json_encode($items);
        }

        $structureJson = null;
        if (! empty($validated['structure_overview'])) {
            $items = array_values(array_filter(array_map('trim', explode("\n", $validated['structure_overview']))));
            $structureJson = json_encode($items);
        }

        $org = Organization::create([
            'code' => strtoupper($validated['code']),
            'name' => $validated['name'],
            'slug' => $slug,
            'level' => $validated['level'] ?? null,
            'role_type' => $validated['role_type'] ?? null,
            'description' => $validated['description'],
            'leader_name' => $validated['leader_name'] ?? null,
            'period' => $validated['period'] ?? null,
            'functions' => $functionsJson,
            'structure_overview' => $structureJson,
            'sort_order' => $validated['sort_order'],
            'status' => $validated['status'],
        ]);

        AdminAuditLog::record(
            'organization.create',
            'Lembaga ormawa "' . $org->name . '" (' . $org->code . ') berhasil dibuat.',
            $org,
            ['code' => $org->code, 'status' => $org->status]
        );

        return redirect()
            ->route('admin.organizations.index')
            ->with('success', 'Organisasi "' . $validated['name'] . '" berhasil ditambahkan.');
    }

    /**
     * Tampilkan formulir edit ormawa.
     */
    public function edit(Organization $organization): View
    {
        return view('admin.organizations.edit', [
            'organization' => $organization,
        ]);
    }

    /**
     * Perbarui data ormawa dalam basis data.
     */
    public function update(Request $request, Organization $organization): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:32'],
            'name' => ['required', 'string', 'max:200'],
            'level' => ['nullable', 'string', 'max:150'],
            'role_type' => ['nullable', 'string', 'max:150'],
            'description' => ['required', 'string'],
            'leader_name' => ['nullable', 'string', 'max:150'],
            'period' => ['nullable', 'string', 'max:100'],
            'functions' => ['nullable', 'string'],
            'structure_overview' => ['nullable', 'string'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'status' => ['required', 'string', 'in:draft,published'],
        ]);

        $slug = $organization->slug;
        if ($organization->name !== $validated['name']) {
            $slug = Organization::generateUniqueSlug($validated['name'], $organization->id);
        }

        $functionsJson = null;
        if (! empty($validated['functions'])) {
            $items = array_values(array_filter(array_map('trim', explode("\n", $validated['functions']))));
            $functionsJson = json_encode($items);
        }

        $structureJson = null;
        if (! empty($validated['structure_overview'])) {
            $items = array_values(array_filter(array_map('trim', explode("\n", $validated['structure_overview']))));
            $structureJson = json_encode($items);
        }

        $organization->update([
            'code' => strtoupper($validated['code']),
            'name' => $validated['name'],
            'slug' => $slug,
            'level' => $validated['level'] ?? null,
            'role_type' => $validated['role_type'] ?? null,
            'description' => $validated['description'],
            'leader_name' => $validated['leader_name'] ?? null,
            'period' => $validated['period'] ?? null,
            'functions' => $functionsJson,
            'structure_overview' => $structureJson,
            'sort_order' => $validated['sort_order'],
            'status' => $validated['status'],
        ]);

        AdminAuditLog::record(
            'organization.update',
            'Lembaga ormawa "' . $organization->name . '" (' . $organization->code . ') berhasil diperbarui.',
            $organization,
            ['code' => $organization->code, 'status' => $organization->status]
        );

        return redirect()
            ->route('admin.organizations.index')
            ->with('success', 'Organisasi "' . $organization->name . '" berhasil diperbarui.');
    }

    /**
     * Hapus data ormawa dari basis data.
     */
    public function destroy(Organization $organization): RedirectResponse
    {
        $id = $organization->id;
        $name = $organization->name;
        $code = $organization->code;
        $organization->delete();

        AdminAuditLog::record(
            'organization.delete',
            'Lembaga ormawa "' . $name . '" (' . $code . ') berhasil dihapus dari sistem.',
            null,
            ['organization_id' => $id, 'code' => $code, 'name' => $name]
        );

        return redirect()
            ->route('admin.organizations.index')
            ->with('success', 'Organisasi "' . $name . '" berhasil dihapus.');
    }
}
