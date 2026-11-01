<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminAuditLog;
use App\Models\BemMember;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BemMemberAdminController extends Controller
{
    /**
     * Tampilkan daftar pengurus BEM dalam dashboard admin.
     */
    public function index(Request $request): View
    {
        $query = BemMember::query()->orderBy('sort_order', 'asc')->latest('created_at');

        if ($search = trim((string) $request->input('q', ''))) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('position', 'like', "%{$search}%")
                    ->orWhere('department', 'like', "%{$search}%");
            });
        }

        if ($level = $request->input('level')) {
            if (array_key_exists($level, BemMember::LEVELS)) {
                $query->where('level', $level);
            }
        }

        if ($department = $request->input('department')) {
            $query->where('department', $department);
        }

        if ($request->has('is_published') && $request->input('is_published') !== '') {
            $query->where('is_published', $request->boolean('is_published'));
        }

        $members = $query->paginate(15)->withQueryString();
        $departments = BemMember::distinct()->pluck('department')->filter()->values();

        return view('admin.bem_members.index', [
            'members' => $members,
            'levels' => BemMember::LEVELS,
            'departments' => $departments,
            'filters' => [
                'q' => $request->input('q'),
                'level' => $request->input('level'),
                'department' => $request->input('department'),
                'is_published' => $request->input('is_published'),
            ],
        ]);
    }

    /**
     * Tampilkan formulir penambahan pengurus BEM baru.
     */
    public function create(): View
    {
        $defaultSortOrder = BemMember::max('sort_order') + 1;

        return view('admin.bem_members.create', [
            'levels' => BemMember::LEVELS,
            'defaultSortOrder' => $defaultSortOrder,
        ]);
    }

    /**
     * Simpan data pengurus BEM baru ke basis data.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'position' => ['required', 'string', 'max:120'],
            'department' => ['nullable', 'string', 'max:120'],
            'level' => ['required', 'string', Rule::in(array_keys(BemMember::LEVELS))],
            'period' => ['required', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:1000'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'is_published' => ['nullable', 'boolean'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ], [
            'name.required' => 'Nama lengkap pengurus wajib diisi.',
            'position.required' => 'Jabatan kepengurusan wajib diisi.',
            'level.required' => 'Pilih tingkatan hierarki struktur.',
            'period.required' => 'Periode masa bakti wajib ditentukan.',
            'sort_order.required' => 'Nomor urutan tampilan wajib diisi.',
            'photo.image' => 'Berkas foto harus berupa gambar valid.',
            'photo.mimes' => 'Format foto hanya boleh JPEG, PNG, JPG, atau WebP.',
            'photo.max' => 'Ukuran berkas foto maksimal 2MB.',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            if ($file->isValid()) {
                $photoPath = $file->store('bem-members', 'public');
            }
        }

        $member = BemMember::create([
            'name' => $validated['name'],
            'position' => $validated['position'],
            'department' => $validated['department'] ?? null,
            'level' => $validated['level'],
            'period' => $validated['period'],
            'description' => $validated['description'] ?? null,
            'sort_order' => $validated['sort_order'],
            'is_published' => $request->boolean('is_published'),
            'photo_path' => $photoPath,
        ]);

        AdminAuditLog::record(
            'bem_member.create',
            'Pengurus BEM "' . $member->name . '" (' . $member->position . ') berhasil ditambahkan.',
            $member,
            [
                'position' => $member->position,
                'department' => $member->department,
                'level' => $member->level,
                'is_published' => $member->is_published,
            ]
        );

        return redirect()
            ->route('admin.bem_members.index')
            ->with('success', 'Data pengurus BEM "' . $member->name . '" berhasil ditambahkan.');
    }

    /**
     * Tampilkan formulir penyuntingan data pengurus BEM.
     */
    public function edit(BemMember $bemMember): View
    {
        return view('admin.bem_members.edit', [
            'member' => $bemMember,
            'levels' => BemMember::LEVELS,
        ]);
    }

    /**
     * Perbarui data pengurus BEM dalam basis data.
     */
    public function update(Request $request, BemMember $bemMember): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'position' => ['required', 'string', 'max:120'],
            'department' => ['nullable', 'string', 'max:120'],
            'level' => ['required', 'string', Rule::in(array_keys(BemMember::LEVELS))],
            'period' => ['required', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:1000'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'is_published' => ['nullable', 'boolean'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ], [
            'name.required' => 'Nama lengkap pengurus wajib diisi.',
            'position.required' => 'Jabatan kepengurusan wajib diisi.',
            'level.required' => 'Pilih tingkatan hierarki struktur.',
            'period.required' => 'Periode masa bakti wajib ditentukan.',
            'sort_order.required' => 'Nomor urutan tampilan wajib diisi.',
            'photo.image' => 'Berkas foto harus berupa gambar valid.',
            'photo.mimes' => 'Format foto hanya boleh JPEG, PNG, JPG, atau WebP.',
            'photo.max' => 'Ukuran berkas foto maksimal 2MB.',
        ]);

        $photoPath = $bemMember->photo_path;

        if ($request->hasFile('photo')) {
            $newPhoto = $request->file('photo');
            if ($newPhoto->isValid()) {
                $stored = $newPhoto->store('bem-members', 'public');
                if ($stored) {
                    $oldPhoto = $bemMember->photo_path;
                    $photoPath = $stored;

                    // Hapus foto lama secara aman jika ada
                    if (! empty($oldPhoto)
                        && Storage::disk('public')->exists($oldPhoto)
                        && ! str_contains($oldPhoto, '..')
                        && ! BemMember::where('id', '!=', $bemMember->id)->where('photo_path', $oldPhoto)->exists()
                    ) {
                        Storage::disk('public')->delete($oldPhoto);
                    }
                }
            }
        }

        $bemMember->update([
            'name' => $validated['name'],
            'position' => $validated['position'],
            'department' => $validated['department'] ?? null,
            'level' => $validated['level'],
            'period' => $validated['period'],
            'description' => $validated['description'] ?? null,
            'sort_order' => $validated['sort_order'],
            'is_published' => $request->boolean('is_published'),
            'photo_path' => $photoPath,
        ]);

        AdminAuditLog::record(
            'bem_member.update',
            'Data pengurus BEM "' . $bemMember->name . '" berhasil diperbarui.',
            $bemMember,
            [
                'position' => $bemMember->position,
                'department' => $bemMember->department,
                'level' => $bemMember->level,
                'is_published' => $bemMember->is_published,
            ]
        );

        return redirect()
            ->route('admin.bem_members.index')
            ->with('success', 'Data pengurus BEM "' . $bemMember->name . '" berhasil diperbarui.');
    }

    /**
     * Hapus data pengurus BEM dari basis data.
     */
    public function destroy(BemMember $bemMember): RedirectResponse
    {
        $id = $bemMember->id;
        $name = $bemMember->name;
        $position = $bemMember->position;
        $photo = $bemMember->photo_path;

        // Hapus foto dari storage publik jika aman
        if (! empty($photo)
            && Storage::disk('public')->exists($photo)
            && ! str_contains($photo, '..')
            && ! BemMember::where('id', '!=', $bemMember->id)->where('photo_path', $photo)->exists()
        ) {
            Storage::disk('public')->delete($photo);
        }

        $bemMember->delete();

        AdminAuditLog::record(
            'bem_member.delete',
            'Data pengurus BEM "' . $name . '" (' . $position . ') berhasil dihapus.',
            null,
            ['member_id' => $id, 'name' => $name, 'position' => $position]
        );

        return redirect()
            ->route('admin.bem_members.index')
            ->with('success', 'Data pengurus BEM "' . $name . '" berhasil dihapus.');
    }
}
