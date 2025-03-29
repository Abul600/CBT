<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Http\Responses\LoginResponse as CustomLoginResponse;
use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Contracts\LoginResponse;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register services in the container.
     */
    public function register(): void
    {
        // ✅ Register Custom Login Response (Corrected)
        $this->app->singleton(LoginResponse::class, CustomLoginResponse::class);
    }

    /**
     * Bootstrap authentication services.
     */
    public function boot(): void
    {
        // ✅ Register Fortify Actions
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        // ✅ Custom Authentication Logic with Active Check
        Fortify::authenticateUsing(function (Request $request) {
            $user = User::where('email', $request->email)->first();

            if ($user && Hash::check($request->password, $user->password)) {
                if (!$user->is_active) {
                    return null; // Block login if user is inactive
                }
                return $user;
            }

            return null; // Prevent login
        });

        // ✅ Rate Limiting
        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input('email')) . '|' . $request->ip());
            return Limit::perMinute(5)->by($throttleKey);
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });

        // ✅ Authentication Views
        Fortify::loginView(fn () => view('auth.login'));
        Fortify::registerView(fn () => view('auth.register'));

        // ✅ Custom Authentication Pipeline
        Fortify::authenticateThrough(function () {
            return [
                \Laravel\Fortify\Actions\EnsureLoginIsNotThrottled::class,
                \Laravel\Fortify\Actions\AttemptToAuthenticate::class,
            ];
        });
    }
}
