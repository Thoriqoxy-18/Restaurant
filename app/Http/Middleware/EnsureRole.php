<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    /**
     * Pastikan user yang login punya salah satu role yang diizinkan.
     * Contoh: ->middleware('role:kasir') atau ->middleware('role:admin').
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        // User yang dinonaktifkan tidak boleh lanjut akses (session langsung di-revoke).
        if (! $user->is_active) {
            auth('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->with('error', 'Akun Anda telah dinonaktifkan. Silakan hubungi administrator.');
        }

        if (! in_array($user->role, $roles, true)) {
            abort(403, 'Akses ditolak.');
        }

        return $next($request);
    }
}
