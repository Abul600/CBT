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

        // ✅ Check if the user is active before allowing login
        if (!$user || !$user->is_active) {
            Auth::logout();
            return redirect()->route('login')->withErrors([
                'email' => 'Your account is deactivated.',
            ]);
        }

        // ✅ Debugging: Log user role during login
        Log::info('User Logging In:', ['user_id' => $user->id, 'roles' => $user->getRoleNames()]);

        // ✅ Redirect based on role
        return redirect()->intended($this->redirectToRoleDashboard($user));
    }

    /**
     * Determine the redirect path based on user role.
     */
    protected function redirectToRoleDashboard($user)
    {
        return match ($user->getRoleNames()->first() ?? 'default') {
            'Admin'     => '/admin/dashboard',
            'Moderator' => '/moderator/dashboard',
            'Student'   => '/student/dashboard',
            default     => '/dashboard',
        };
    }
}
