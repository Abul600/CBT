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
        'is_active',
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

        // Custom event to sync roles
        static::rolesUpdated(function ($user) {
            $user->syncRoleToColumn();
        });
    }

    /**
     * Custom event hook for role changes
     */
    protected static function rolesUpdated($callback)
    {
        static::registerModelEvent('rolesUpdated', $callback);
    }

    /**
     * Override assignRole to sync and fire event
     */
    public function assignRole($role, $guard = null)
    {
        $this->spatieAssignRole($role, $guard); // Call original trait method
        $this->syncRoleToColumn();
        $this->fireModelEvent('rolesUpdated');
    }

    /**
     * Sync the first role to the legacy `role` column
     */
    public function syncRoleToColumn()
    {
        $role = $this->roles->first()?->name;

        if ($role && $this->role !== $role) {
            $this->forceFill(['role' => $role])->saveQuietly();
        }
    }

    /**
     * Role-based redirection
     */
    public function redirectToRoleDashboard()
    {
        return match (true) {
            $this->hasRole('admin') => route('admin.dashboard'),
            $this->hasRole('moderator') => route('moderator.dashboard'),
            $this->hasRole('paper_setter') => route('paper_setter.dashboard'),
            $this->hasRole('student') => route('student.dashboard'),
            default => $this->defaultDashboard(),
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
