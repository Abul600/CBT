<?php

namespace App\Actions\Fortify;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Closure; // Add this line

class RedirectBasedOnRole
{
    public function handle(Request $request, Closure $next) // Use Closure type hint
    {
        $user = Auth::user();

        if ($user->hasRole('admin')) {
            return redirect()->intended('/admin/dashboard');
        } elseif ($user->hasRole('moderator')) {
            return redirect()->intended('/moderator/dashboard');
        } elseif ($user->hasRole('paper_seater')) {
            return redirect()->intended('/paper-seater/dashboard');
        } else {
            return redirect()->intended('/student/dashboard');
        }

        return $next($request);
    }
}
