<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        $user = Auth::user();
        $token = $user->createToken('api-token')->plainTextToken;

        // Load the role relationship
        $user->load('role');

        // Agar request API se hai toh JSON response bheje
        if ($request->expectsJson()) {
            return response()->json([
                'token' => $token,
                'role' => $user->role->slug ?? 'user',
                'redirect' => $this->getRedirectUrl($user)
            ]);
        }

        // Normal website redirect
        return redirect($this->getRedirectUrl($user))->with('token', $token);
    }
    private function getRedirectUrl($user)
    {
        if ($user->hasRole('admin')) {
            return route('admin.dashboard');
        } elseif ($user->hasRole('salesperson')) {
            return route('salesperson.dashboard');
        }
        return route('dashboard');
    }
    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
