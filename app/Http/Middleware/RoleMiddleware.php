<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    // Terima $roleId sebagai integer
    public function handle(Request $request, Closure $next, int $roleId): Response
    {
        // 1. Pastikan pengguna sudah login
        if (! $request->user()) {
            return redirect('/login');
        }

        // 2. Cek apakah role ID pengguna sesuai
        if (! $request->user()->hasRole($roleId)) {
            // Jika tidak, kembalikan response 403 Forbidden
            abort(403, 'Akses Ditolak: Anda tidak memiliki peran yang sesuai.'); 
        }

        // 3. Jika sesuai, lanjutkan request
        return $next($request);
    }
}
