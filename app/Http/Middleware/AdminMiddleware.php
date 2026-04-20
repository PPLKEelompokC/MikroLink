<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Menjaga agar rute admin tidak bisa diakses user biasa.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Jika belum login atau role bukan admin, balikkan ke dashboard biasa
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            return redirect()->route('dashboard');
        }

        return $next($request);
    }
}