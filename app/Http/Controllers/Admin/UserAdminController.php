<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\AdminAuditLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserAdminController extends Controller
{
    /**
     * Tampilkan daftar seluruh pengguna pengelola portal (Khusus Super Admin).
     */
    public function index(Request $request): View
    {
        $query = User::query()->latest('created_at');

        if ($search = trim((string) $request->input('q', ''))) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($role = $request->input('role')) {
            if (in_array($role, UserRole::values(), true)) {
                $query->where('role', $role);
            }
        }

        if ($request->has('is_active') && $request->input('is_active') !== '') {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $users = $query->paginate(15)->withQueryString();

        return view('admin.users.index', [
            'users' => $users,
            'roles' => UserRole::options(),
            'filters' => [
                'q' => $request->input('q'),
                'role' => $request->input('role'),
                'is_active' => $request->input('is_active'),
            ],
        ]);
    }

    /**
     * Tampilkan formulir pembuatan pengguna pengelola baru.
     */
    public function create(): View
    {
        return view('admin.users.create', [
            'roles' => UserRole::options(),
        ]);
    }

    /**
     * Simpan akun pengelola baru ke basis data.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'string', 'email', 'max:150', 'unique:users,email'],
            'role' => ['required', 'string', Rule::in(UserRole::values())],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'name.required' => 'Nama lengkap pengelola wajib diisi.',
            'email.required' => 'Alamat surel wajib diisi.',
            'email.email' => 'Format alamat surel tidak valid.',
            'email.unique' => 'Alamat surel sudah terdaftar dalam sistem.',
            'role.required' => 'Pilih peran / role yang sesuai.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => strtolower($validated['email']),
            'role' => UserRole::from($validated['role']),
            'password' => Hash::make($validated['password']),
            'is_active' => $request->boolean('is_active', true),
        ]);

        AdminAuditLog::record(
            'user.create',
            'Akun pengelola baru "' . $user->name . '" (' . $user->role->label() . ') berhasil dibuat.',
            $user,
            ['user_id' => $user->id, 'email' => $user->email, 'role' => $user->role->value, 'is_active' => $user->is_active]
        );

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Akun pengelola untuk ' . $validated['name'] . ' berhasil dibuat.');
    }

    /**
     * Tampilkan formulir edit akun pengelola.
     */
    public function edit(User $user): View
    {
        return view('admin.users.edit', [
            'user' => $user,
            'roles' => UserRole::options(),
        ]);
    }

    /**
     * Perbarui data akun pengelola.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $currentUser = $request->user();

        $rules = [
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'string', 'email', 'max:150', Rule::unique('users', 'email')->ignore($user->id)],
        ];

        // Jika bukan mengedit diri sendiri, izinkan ubah role & status
        if ($currentUser->id !== $user->id) {
            $rules['role'] = ['required', 'string', Rule::in(UserRole::values())];
            $rules['is_active'] = ['nullable', 'boolean'];
        }

        $validated = $request->validate($rules, [
            'name.required' => 'Nama lengkap pengelola wajib diisi.',
            'email.required' => 'Alamat surel wajib diisi.',
            'email.unique' => 'Alamat surel sudah digunakan akun lain.',
        ]);

        $updateData = [
            'name' => $validated['name'],
            'email' => strtolower($validated['email']),
        ];

        if ($currentUser->id !== $user->id) {
            $targetRole = UserRole::from($validated['role']);
            $targetActive = $request->boolean('is_active');

            // Proteksi: jangan sampai super admin terakhir dinonaktifkan atau diturunkan role-nya
            if ($user->isSuperAdmin() && ($targetRole !== UserRole::SuperAdmin || ! $targetActive)) {
                $activeSuperAdminCount = User::where('role', UserRole::SuperAdmin)
                    ->where('is_active', true)
                    ->where('id', '!=', $user->id)
                    ->count();

                if ($activeSuperAdminCount < 1) {
                    return back()
                        ->withInput()
                        ->withErrors([
                            'role' => 'Tidak dapat menonaktifkan atau mengubah peran Super Administrator terakhir yang aktif.',
                        ]);
                }
            }

            $updateData['role'] = $targetRole;
            $updateData['is_active'] = $targetActive;
        }

        $user->update($updateData);

        AdminAuditLog::record(
            'user.update',
            'Data akun pengelola "' . $user->name . '" berhasil diperbarui.',
            $user,
            ['user_id' => $user->id, 'role' => $user->role->value, 'is_active' => $user->is_active]
        );

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Data akun pengelola ' . $user->name . ' berhasil diperbarui.');
    }

    /**
     * Reset kata sandi pengguna pengelola.
     */
    public function resetPassword(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'new_password.required' => 'Kata sandi baru wajib diisi.',
            'new_password.min' => 'Kata sandi baru minimal 8 karakter.',
            'new_password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
        ]);

        $user->update([
            'password' => Hash::make($validated['new_password']),
        ]);

        AdminAuditLog::record(
            'user.password_reset',
            'Kata sandi akun pengelola "' . $user->name . '" berhasil direset oleh administrator.',
            $user,
            ['user_id' => $user->id, 'email' => $user->email]
        );

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Kata sandi akun ' . $user->name . ' berhasil diperbarui.');
    }

    /**
     * Hapus akun pengelola dari sistem.
     */
    public function destroy(Request $request, User $user): RedirectResponse
    {
        $currentUser = $request->user();

        // 1. Tidak boleh menghapus diri sendiri saat login
        if ($currentUser->id === $user->id) {
            return back()->withErrors([
                'delete_error' => 'Anda tidak dapat menghapus akun Anda sendiri saat sedang masuk.',
            ]);
        }

        // 2. Tidak boleh menghapus Super Admin terakhir
        if ($user->isSuperAdmin()) {
            $activeSuperAdminCount = User::where('role', UserRole::SuperAdmin)
                ->where('is_active', true)
                ->where('id', '!=', $user->id)
                ->count();

            if ($activeSuperAdminCount < 1) {
                return back()->withErrors([
                    'delete_error' => 'Tidak dapat menghapus Super Administrator terakhir dalam sistem.',
                ]);
            }
        }

        $id = $user->id;
        $name = $user->name;
        $email = $user->email;
        $user->delete();

        AdminAuditLog::record(
            'user.delete',
            'Akun pengelola "' . $name . '" (' . $email . ') berhasil dihapus dari sistem.',
            null,
            ['user_id' => $id, 'name' => $name, 'email' => $email]
        );

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'Akun pengelola ' . $name . ' berhasil dihapus.');
    }
}
