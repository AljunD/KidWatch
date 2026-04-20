<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TeacherAuthMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            // Redirect to login form if not authenticated
            return redirect()->route('login.form')->with('status', 'Please log in first.');
        }

        return $next($request);
    }
}
