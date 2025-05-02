<?php

namespace App\Http\Responses;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        $user = Auth::user();

        if ($user->role == 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif ($user->role == 'student') {
            return redirect()->route('student.dashboard');
        } elseif ($user->role == 'moderator') {
            return redirect()->route('moderator.dashboard');
        } elseif ($user->role == 'paper_setter') {
            return redirect()->route('paper_setter.dashboard');
        }

        return redirect('/dashboard');
    }
}
