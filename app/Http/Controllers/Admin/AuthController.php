<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\AdminAuditLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Tampilkan formulir login admin.
     */
    public function showLoginForm(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.auth.login');
    }

    /**
     * Proses autentikasi masuk pengelola.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Alamat surel wajib diisi.',
            'email.email' => 'Format alamat surel tidak valid.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        $remember = $request->boolean('remember');

        if (! Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password'], 'is_active' => true], $remember)) {
            return back()
                ->withInput($request->only('email', 'remember'))
                ->withErrors([
                    'email' => 'Kombinasi surel dan kata sandi tidak cocok atau akun Anda tidak aktif.',
                ]);
        }

        $user = Auth::user();

        // Validasi peran admin
        if (! in_array($user->role, [UserRole::SuperAdmin, UserRole::Admin, UserRole::Advocacy, UserRole::Editor], true)) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('admin.login')->withErrors([
                'email' => 'Akun Anda tidak memiliki izin untuk mengakses Dashboard Admin.',
            ]);
        }

        $request->session()->regenerate();

        AdminAuditLog::record(
            'auth.login',
            'Pengelola ' . $user->name . ' berhasil masuk ke dashboard panel administrasi.',
            $user,
            ['role' => $user->role instanceof UserRole ? $user->role->value : (string) $user->role],
            $user->id
        );

        return redirect()
            ->intended(route('admin.dashboard'))
            ->with('success', 'Selamat datang kembali, ' . $user->name . '!');
    }

    /**
     * Proses logout pengelola dan pembatalan sesi.
     */
    public function logout(Request $request): RedirectResponse
    {
        $currentUser = Auth::user();
        if ($currentUser) {
            AdminAuditLog::record(
                'auth.logout',
                'Pengelola ' . $currentUser->name . ' keluar dari sistem.',
                $currentUser,
                [],
                $currentUser->id
            );
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('admin.login')
            ->with('status_notice', 'Anda telah berhasil keluar dari sistem.');
    }
}
