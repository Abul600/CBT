<?php

namespace App\Http\Responses;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract {
    /**
     * Handle user redirection after login.
     */
    public function toResponse($request) {
        $user = Auth::user();

        // ✅ Redirect users based on their role
        if ($user->hasRole('Admin')) {
            return redirect('/admin/dashboard');
        } elseif ($user->hasRole('Moderator')) {
            return redirect('/moderator/dashboard');
        } elseif ($user->hasRole('Student')) {
            return redirect('/student/dashboard');
        } else {
            return redirect('/dashboard'); // Default redirect if role is unknown
        }
    }
}
