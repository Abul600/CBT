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
        HasRoles;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'district',
        'password',
        'role', // Ensuring 'role' is fillable
        'is_active', // ✅ Added is_active field
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean', // ✅ Ensure it's treated as a boolean
    ];

    /**
     * Automatically update role column when assigning roles.
     */
    protected static function boot()
    {
        parent::boot();

        static::saved(function ($user) {
            $user->syncRoleColumn();
        });

        static::created(function ($user) {
            $user->syncRoleColumn();
        });
    }

    /**
     * Synchronize the 'role' column with the assigned roles.
     */
    public function syncRoleColumn()
    {
        $roleName = $this->getRoleNames()->first();

        if ($roleName && $this->role !== $roleName) {
            $this->updateQuietly(['role' => $roleName]);
        }
    }

    /**
     * Redirect user based on their role after login.
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
     * Scope to get only active users.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
