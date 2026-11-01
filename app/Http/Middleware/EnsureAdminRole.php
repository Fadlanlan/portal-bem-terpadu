<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $permission  Modul atau nama role yang diizinkan (misal: 'super_admin', 'aspirations', 'content', 'users')
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = $request->user();

        if (! $user || ! $user->is_active) {
            abort(403, 'Akses ditolak.');
        }

        // Super Admin memiliki semua izin
        if ($user->role === UserRole::SuperAdmin) {
            return $next($request);
        }

        // Jika permission adalah pengecekan role spesifik 'super_admin', 'users', atau 'settings'
        if ($permission === 'super_admin' || $permission === 'users' || $permission === 'settings') {
            abort(403, 'Hanya Super Administrator yang berwenang mengakses modul ini.');
        }

        // Pengecekan modul aspirasi
        if ($permission === 'aspirations') {
            if ($user->canAccessModule('aspirations')) {
                return $next($request);
            }
            abort(403, 'Anda tidak memiliki hak akses untuk mengelola aspirasi mahasiswa.');
        }

        // Pengecekan modul konten publik
        if ($permission === 'content' || in_array($permission, ['articles', 'programs', 'events', 'organizations', 'bem_members'], true)) {
            if ($user->canAccessModule($permission)) {
                return $next($request);
            }
            abort(403, 'Anda tidak memiliki hak akses untuk mengelola konten portal ini.');
        }

        // Pengecekan modul chat
        if ($permission === 'chat') {
            if ($user->canAccessModule('chat')) {
                return $next($request);
            }
            abort(403, 'Anda tidak memiliki hak akses untuk mengelola Live Chat.');
        }

    }
}
