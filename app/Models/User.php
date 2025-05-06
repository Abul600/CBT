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
            HasRoles::assignRole as spatieAssignRole;
        }

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'is_active',
        'role',
        'district', // Changed from district_id to match your migration
        'moderator_id', // Added for paper setter logic
        'is_moderator',
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
        'is_moderator' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::rolesUpdated(function ($user) {
            $user->syncRoleToColumn();
        });
    }

    protected static function rolesUpdated($callback)
    {
        static::registerModelEvent('rolesUpdated', $callback);
    }

    public function assignRole($role, $guard = null)
    {
        $this->spatieAssignRole($role, $guard);
        $this->syncRoleToColumn();
        $this->fireModelEvent('rolesUpdated');
    }

    public function syncRoleToColumn()
    {
        $role = $this->roles->first()?->name;

        if ($role && $this->role !== $role) {
            $this->forceFill(['role' => $role])->saveQuietly();
        }
    }

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

    protected function defaultDashboard()
    {
        return $this->is_active ? route('dashboard') : route('welcome');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeActiveModerators($query)
    {
        return $query->where('role', 'moderator')
                     ->where('is_moderator', true);
    }

    public function scopeForDistrict($query, $district)
    {
        return $query->where('district', $district);
    }

    // ✅ Optional: Get the Moderator who created this Paper Setter
    public function moderator()
    {
        return $this->belongsTo(User::class, 'moderator_id');
    }
}
