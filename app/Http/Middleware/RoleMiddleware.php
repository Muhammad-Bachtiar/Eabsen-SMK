<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * Support 2 mode:
     *  - Role biasa:       role:admin, role:guru, role:bk, dll
     *  - Koordinator BK:   role:koordinator_bk → cek role='bk' DAN is_koordinator_bk=true
     *
     * @param  string  ...$roles  Nama role atau 'koordinator_bk'
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = Auth::user();
        $userRole = $user->role->nama_role;

        $allowed = in_array($userRole, $roles);

        // Khusus koordinator_bk: role di DB tetap 'bk', tapi harus punya flag is_koordinator_bk
        if (! $allowed && in_array('koordinator_bk', $roles)) {
            $allowed = ($userRole === 'bk' && $user->is_koordinator_bk);
        }

        abort_unless(
            $allowed,
            403,
            'Akses Ditolak. Anda tidak memiliki hak untuk membuka halaman ini.'
        );

        return $next($request);
    }
}
