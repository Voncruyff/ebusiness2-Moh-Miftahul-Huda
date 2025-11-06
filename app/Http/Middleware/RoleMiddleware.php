<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, $role)
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        // Pastikan role yang sedang login sama dengan yang dibutuhkan
        if (Auth::user()->role !== $role) {
            abort(403, 'Akses ditolak. Role kamu: ' . Auth::user()->role . ' (dibutuhkan: ' . $role . ')');
        }

        return $next($request);
    }
}
