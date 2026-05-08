<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */

    /**
     * Role aliases: maps display names (stored in DB) → internal route keys.
     */
    protected array $aliases = [
        'Admin Koperasi'   => 'admin',
        'Manajer Koperasi' => 'manager',
        'Super Admin'      => 'super_admin',
    ];

    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(403, 'Unauthorized access.');
        }

        // Normalize: map display name → internal key, fallback to raw role value
        $normalizedRole = $this->aliases[$user->role] ?? $user->role;

        // Accept if either the raw role or the normalized role matches
        if (! in_array($normalizedRole, $roles) && ! in_array($user->role, $roles)) {
            abort(403, 'Unauthorized access.');
        }

        return $next($request);
    }
}