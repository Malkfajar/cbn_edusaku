<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->is_admin) {
            return $next($request);
        }
        // Jika bukan admin, arahkan ke halaman dashboard mahasiswa
        return redirect('/dashboard');
    }
}