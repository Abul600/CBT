<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens,
        HasFactory,
        HasProfilePhoto,
        Notifiable,
        TwoFactorAuthenticatable,
        HasRoles {
            HasRoles::assignRole as spatieAssignRole; // Alias the trait method
        }

    protected $fillable = [
        'name',
        'email',
        'phone',
        'district',
        'password',
        'role', // Ensuring 'role' is fillable
        'is_active', // ✅ Added is_active field
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        // Hook into the saved event to sync the role to the 'role' column when a role is assigned
        static::saved(function ($user) {
            if ($user->isDirty('roles')) {
                $user->syncRoleToColumn();
            }
        });
    }

    /**
     * Override assignRole to sync and fire event
     */
    public function assignRole($role, $guard = null)
    {
        $this->spatieAssignRole($role, $guard); // Call original trait method
        $this->syncRoleToColumn();
    }

    /**
     * Sync the first role to the legacy `role` column
     */
    public function syncRoleToColumn()
    {
        $role = $this->roles->first()?->name;

        if ($role && $this->role !== $role) {
            $this->updateQuietly(['role' => $role]);
        }
    }

    /**
     * Role-based redirection
     */
    public function redirectToRoleDashboard()
    {
        return match ($this->getRoleNames()->first()) {
            'admin'        => route('admin.dashboard'),
            'moderator'    => route('moderator.dashboard'),
            'paper_setter' => route('paper_setter.dashboard'),
            'student'      => route('student.dashboard'),
            default        => route('dashboard'),
        };
    }

    /**
     * Fallback dashboard
     */
    protected function defaultDashboard()
    {
        return $this->is_active ? route('dashboard') : route('welcome');
    }

    /**
     * Scope for active users
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
