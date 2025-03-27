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

        // Agar user logged in nahi hai to use login page pe bhej do
        if (!$user) {
            return redirect('/login');
        }

        // Agar user ka role admin hai
        if ($user->role && $user->role->slug === 'salesperson') {
            // Admin ko sirf /dashboard se /admin/dashboard pe redirect karo
            if ($request->is('dashboard')) {
                return redirect()->route('salesperson.dashboard');
            }
            return $next($request);
        }

        // Agar non-admin user admin panel access kar raha hai, use /dashboard pe redirect karo
        if ($request->is('salesperson/*')) {
            return redirect('/dashboard');
        }

        return $next($request);
    }
}
