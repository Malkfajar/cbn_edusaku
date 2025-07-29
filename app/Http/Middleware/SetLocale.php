<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SetLocale
{
 public function handle(Request $request, Closure $next)
{
    // Ambil locale dari session. Jika tidak ada, ambil dari konfigurasi default di config/app.php
    $locale = session('locale', config('app.locale'));
    

    // Set locale untuk aplikasi
    App::setLocale($locale);

    return $next($request);
}
}