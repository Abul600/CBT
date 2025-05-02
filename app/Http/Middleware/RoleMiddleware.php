<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return \Illuminate\Http\Response
     */
    public function handle(Request $request, Closure $next, $role)
    {
        // ✅ Ensure user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'You must be logged in to access this page.');
        }

        $user = Auth::user();

        // ✅ Role check via Spatie
        if (method_exists($user, 'hasRole') && $user->hasRole($role)) {
            return $next($request);
        }

        // ✅ Fallback role column check (if needed)
        if (isset($user->role) && $user->role === $role) {
            return $next($request);
        }

        // ✅ Unauthorized access handling
        return $this->handleUnauthorizedAccess($user);
    }

    /**
     * Handle unauthorized access and redirect appropriately.
     *
     * @param  \Illuminate\Foundation\Auth\User  $user
     * @return \Illuminate\Http\Response
     */
    protected function handleUnauthorizedAccess($user)
    {
        // ✅ Redirect based on user's actual role
        if ($user->hasRole('admin')) {
            return redirect()->route('admin.dashboard')->with('error', 'You are not authorized to access that section.');
        }

        if ($user->hasRole('moderator')) {
            return redirect()->route('moderator.dashboard')->with('error', 'You are not authorized to access that section.');
        }

        if ($user->hasRole('student')) {
            return redirect()->route('student.dashboard')->with('error', 'You are not authorized to access that section.');
        }

        if ($user->hasRole('paper_setter')) {
            return redirect()->route('paper_setter.dashboard')->with('error', 'You are not authorized to access that section.');
        }

        // ✅ Default: abort with 403
        return abort(403, 'Unauthorized access.');
    }
}
