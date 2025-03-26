<?php

namespace App\Http\Responses;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        $user = Auth::user();

        // ✅ Debugging: Log user role during login
        Log::info('User Logging In:', ['roles' => $user->getRoleNames()]);

        // ✅ Prevents redirect loops by using redirect()->intended()
        if ($user->hasRole('Admin')) {
            return redirect()->intended('/admin/dashboard');
        } elseif ($user->hasRole('Moderator')) {
            return redirect()->intended('/moderator/dashboard');
        } elseif ($user->hasRole('Student')) {
            return redirect()->intended('/student/dashboard');
        } else {
            return redirect()->intended('/dashboard'); // ✅ Default redirect for unknown roles
        }
    }
}
