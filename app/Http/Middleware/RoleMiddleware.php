<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware {
    public function handle(Request $request, Closure $next, $role) {
        // ✅ Ensure user is logged in
        if (!Auth::check()) {
            return redirect('/login')->with('error', 'You must be logged in to access this page.');
        }

        // ✅ Check if user has the required role
        if (!Auth::user()->hasRole($role)) {
            abort(403, 'Unauthorized access'); // Prevents infinite redirect loops
        }

        return $next($request);
    }
}
