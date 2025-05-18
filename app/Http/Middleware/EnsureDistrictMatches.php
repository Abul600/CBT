<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureDistrictMatches
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): \Symfony\Component\HttpFoundation\Response  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            abort(403, 'Unauthorized access');
        }

        // Get the exam from the route parameters
        $exam = $request->route('exam');

        // Abort if no exam found on the route
        if (!$exam) {
            abort(404, 'Exam not found');
        }

        // Check if the user's district matches the exam's district
        if (Auth::user()->district_id !== $exam->district_id) {
            abort(403, 'This exam is not available in your district');
        }

        return $next($request);
    }
}
