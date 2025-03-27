<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Check if the user is authenticated
        if (!Auth::check()) {
            abort(403, 'Unauthorized access');
        }

        // Get the authenticated user
        $user = Auth::user();

        // Check if the user has the specified role
        if (!$user->hasRole($role)) {
            abort(403, 'Unauthorized access');
        }

        return $next($request);
    }
}
