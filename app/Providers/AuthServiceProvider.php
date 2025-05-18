<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Models\Exam;
use App\Policies\ExamPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Exam::class => ExamPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot()
    {
        $this->registerPolicies();

        // ✅ Allow moderators to manage paper setters
        Gate::define('manage-paper-setters', function (User $user) {
            return $user->hasRole('moderator');
        });

        // ✅ Allow paper setters to manage their own questions
        Gate::define('manage-own-questions', function (User $user) {
            return $user->hasRole('paper_setter');
        });
    }
}
