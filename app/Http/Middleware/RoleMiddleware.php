<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware {
    public function handle(Request $request, Closure $next, $role) {
        // ✅ Redirect to login if the user is not authenticated
        if (!Auth::check()) {
            return redirect('/login')->with('error', 'You must be logged in to access this page.');
        }

        // ✅ Use Spatie's hasRole() to check permissions
        if (!Auth::user()->hasRole($role)) {
            abort(403, 'Unauthorized access'); // Prevents infinite redirect loops
        }

        return $next($request);
    }
}
