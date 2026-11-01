<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminAuthenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return redirect()->route('admin.login')->with('login_error', 'Silakan masuk terlebih dahulu untuk mengakses Dashboard Admin.');
        }

        $user = Auth::user();

        if (! $user->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('admin.login')->withErrors([
                'email' => 'Akun Anda saat ini dinonaktifkan. Silakan hubungi Super Administrator.',
            ]);
        }

        // Pastikan role valid
        if (! in_array($user->role, [UserRole::SuperAdmin, UserRole::Admin, UserRole::Advocacy, UserRole::Editor], true)) {
            abort(403, 'Akses ditolak. Akun Anda tidak memiliki peran pengelola portal.');
        }

        return $next($request);
    }
}
