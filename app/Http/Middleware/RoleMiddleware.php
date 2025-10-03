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
     * Ganti "int $roleId" menjadi "string $roles"
     */
    public function handle(Request $request, Closure $next, string $roleIds): Response 
    {
        if (!Auth::check()) {
            return redirect('login');
        }

        $user = Auth::user();
        
        // 1. Pisahkan string ID (misal: '1|2') menjadi array
        $allowedIds = explode('|', $roleIds); 
        
        // 2. Bandingkan role_id pengguna (yang merupakan integer) dengan array string ID
        // Kita konversi role_id ke string agar in_array berfungsi dengan benar.
        if (!in_array((string)$user->role_id, $allowedIds)) { 
            // Ditolak
            abort(403, 'Akses Ditolak. Anda tidak memiliki izin peran untuk halaman ini.');
        }

        return $next($request);
    }
}