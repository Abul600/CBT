<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles; // ✅ Import Spatie's HasRoles

class User extends Authenticatable
{
    use HasApiTokens, 
        HasFactory, 
        HasProfilePhoto, 
        Notifiable, 
        TwoFactorAuthenticatable, 
        HasRoles; // ✅ Ensure HasRoles is included

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id', // ✅ Store the role ID for Spatie's permission system
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
     * The accessors to append to the model's array form.
     */
    protected $appends = [
        'profile_photo_url',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Define relationship with Role model (if needed)
     */
    public function role()
    {
        return $this->belongsTo(\Spatie\Permission\Models\Role::class, 'role_id');
    }

    /**
     * Redirect user based on their role.
     */
    public function redirectToRoleDashboard()
    {
        return match($this->getRoleNames()->first()) { // ✅ Uses Spatie's role system
            'admin'        => redirect()->route('admin.dashboard'),
            'moderator'    => redirect()->route('moderator.dashboard'),
            'paper_setter' => redirect()->route('paper_setter.dashboard'),
            'student'      => redirect()->route('student.dashboard'),
            default        => redirect('/dashboard'),
        };
    }
}
