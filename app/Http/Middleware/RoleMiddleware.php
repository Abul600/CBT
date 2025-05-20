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
        // ✅ Ensure the user is logged in
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'You must be logged in to access this page.');
        }

        $user = Auth::user();

        // ✅ Role check via Spatie's hasRole
        if (method_exists($user, 'hasRole') && $user->hasRole($role)) {
            return $next($request);
        }

        // ✅ Optional fallback: role column check
        if (property_exists($user, 'role') && $user->role === $role) {
            return $next($request);
        }

        // ✅ Unauthorized access — redirect based on actual role
        return $this->handleUnauthorizedAccess($user);
    }

    /**
     * Redirect unauthorized users based on their role or abort.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @return \Illuminate\Http\Response
     */
    protected function handleUnauthorizedAccess($user)
    {
        if (method_exists($user, 'hasRole')) {
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
        }

        // ✅ Default fallback — abort
        return abort(403, 'THIS ACTION IS UNAUTHORIZED.');
    }
}
