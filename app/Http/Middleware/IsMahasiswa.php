<?php

    namespace App\Http\Middleware;

    use Closure;
    use Illuminate\Http\Request;
    use Symfony\Component\HttpFoundation\Response;
    use Illuminate\Support\Facades\Auth;

    class IsMahasiswa
    {
        /**
         * Handle an incoming request.
         *
         * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
         */
        public function handle(Request $request, Closure $next): Response
        {
            // Cek apakah pengguna sudah login
            if (!Auth::check()) {
                return redirect('/login');
            }

            // Jika pengguna adalah MAHASISWA (bukan admin), izinkan akses.
            if (!Auth::user()->is_admin) {
                return $next($request);
            }

            // Jika yang mencoba mengakses adalah ADMIN, lempar ke dasbor admin.
            if (Auth::user()->is_admin) {
                return redirect()->route('admin.dashboard');
            }

            // Default fallback jika ada kasus lain (seharusnya tidak terjadi)
            return redirect('/login');
        }
    }
    