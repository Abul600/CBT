<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
        'is_active',
        'district_id',
        'moderator_id',
        'is_moderator',
        'timezone', // Ensure this is fillable if users can have custom timezones
    ];

    /**
     * The attributes that should be hidden for arrays.
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
        'is_active' => 'boolean',
        'is_moderator' => 'boolean',
    ];

    /**
     * Boot method for model event listeners.
     */
    protected static function boot()
    {
        parent::boot();

        static::saved(function ($user) {
            if ($user->isDirty('roles')) {
                $user->syncRoleToColumn();
            }
        });
    }

    /**
     * Override assignRole to sync and fire event.
     */
    public function assignRole($role, $guard = null)
    {
        $this->spatieAssignRole($role, $guard);
        $this->syncRoleToColumn();
    }

    /**
     * Sync the role to the 'role' column in the database.
     */
    public function syncRoleToColumn()
    {
        $role = $this->roles->first()?->name;

        if ($role && $this->role !== $role) {
            $this->updateQuietly(['role' => $role]);
        }
    }

    /**
     * Redirect user to the appropriate dashboard based on their role.
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
     * Get the default dashboard route.
     */
    protected function defaultDashboard()
    {
        return $this->is_active ? route('dashboard') : route('welcome');
    }

    /**
     * Scope for active users.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for active moderators.
     */
    public function scopeActiveModerators($query)
    {
        return $query->where('role', 'moderator')
                     ->where('is_moderator', true);
    }

    /**
     * Scope for users within a specific district ID.
     */
    public function scopeForDistrict($query, $districtId)
    {
        return $query->where('district_id', $districtId);
    }

    /**
     * The district this user belongs to.
     */
    public function district()
    {
        return $this->belongsTo(District::class, 'district_id');
    }

    /**
     * Get the Moderator who created this Paper Setter.
     */
    public function moderator()
    {
        return $this->belongsTo(User::class, 'moderator_id');
    }

    /**
     * Get the exam results for this user.
     */
    public function results()
    {
        return $this->hasMany(Result::class);
    }

    /**
     * Exams the student has applied to.
     */
    public function appliedExams()
    {
        return $this->belongsToMany(Exam::class)->withTimestamps();
    }

    /**
     * Exams the student has taken or is associated with (many-to-many).
     */
    public function exams(): BelongsToMany
    {
        return $this->belongsToMany(Exam::class, 'exam_user', 'user_id', 'exam_id')
                    ->withTimestamps();
    }

    /**
     * Get the user's timezone or fall back to app default.
     */
    public function getTimezoneAttribute()
    {
        return $this->attributes['timezone'] ?? config('app.timezone');
    }

    /**
     * Accessor for user's local timezone (alias to timezone).
     */
    public function getLocalTimezoneAttribute()
    {
        return $this->timezone ?? config('app.timezone');
    }

    /**
     * Check if user has applied to a given exam.
     */
    public function hasApplied(Exam $exam): bool
    {
        return $this->appliedExams->contains($exam->id);
    }
}
