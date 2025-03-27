<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class CheckPendingReminders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $now = Carbon::now();
            
            // Check for pending meeting reminders
            $pendingReminders = $user->meetings()
                ->where('status', 'pending')
                ->where('reminder_date', '<=', $now)
                ->where('meeting_date', '>', $now)
                ->count();
                
            // Share the count with all views
            view()->share('pendingReminders', $pendingReminders);
        }
        
        return $next($request);
    }
} 