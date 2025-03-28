<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SalespersonMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // If user is not logged in, redirect to login page
        if (!$user) {
            return redirect('/login');
        }

        // Load the role relationship if not already loaded
        if (!$user->relationLoaded('role')) {
            $user->load('role');
        }

        // If user is a salesperson
        if ($user->role && $user->role->slug === 'salesperson') {
            // If trying to access /dashboard, redirect to salesperson dashboard
            if ($request->is('dashboard')) {
                return redirect()->route('salesperson.dashboard');
            }
            // Allow access to salesperson routes
            return $next($request);
        }

        // If non-salesperson tries to access salesperson routes, redirect to dashboard
        if ($request->is('salesperson/*')) {
            return redirect('/dashboard');
        }

        return $next($request);
    }
}
