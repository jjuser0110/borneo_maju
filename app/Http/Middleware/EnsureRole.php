<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Only let users whose role_id is in the given list through.
 * Usage: ->middleware('role:1,3')   (1 = admin, 2 = staff, 3 = agent)
 */
class EnsureRole
{
    public function handle(Request $request, Closure $next, string ...$roles)
    {
        $user = $request->user();

        if (!$user || !in_array((string) $user->role_id, $roles, true)) {
            abort(403, 'Access denied');
        }

        return $next($request);
    }
}
