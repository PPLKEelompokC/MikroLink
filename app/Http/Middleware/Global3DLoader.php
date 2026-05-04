<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Global3DLoader
{
    public function handle(Request $request, Closure $next): Response
    {
        // Fitur global loader telah dipindahkan ke layout utama menggunakan @persist
        return $next($request);
    }
}