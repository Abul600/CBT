<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, $role)
    {
        // ✅ Ensure user is logged in
        if (!Auth::check()) {
            return redirect('/login')->with('error', 'You must be logged in to access this page.');
        }

        $user = Auth::user();

        // ✅ If using Spatie Permissions
        if (method_exists($user, 'hasRole') && $user->hasRole($role)) {
            return $next($request);
        }

        // ✅ If checking role column in users table
        if (property_exists($user, 'role') && $user->role === $role) {
            return $next($request);
        }

        // ✅ Unauthorized Access
        abort(403, 'Unauthorized access');
    }
}
