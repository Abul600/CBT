<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Exam extends Model
{
    use HasFactory;

    // Status constants
    const STATUS_DRAFT = 'draft';
    const STATUS_ACTIVE = 'active';
    const STATUS_COMPLETED = 'completed';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'description',
        'duration',
        'start_time',
        'end_time',
        'moderator_id',
        'district_id',
        'status',
        'is_active',
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected $casts = [
        'start_time' => 'datetime',
        'end_time'   => 'datetime',
        'is_active'  => 'boolean',
    ];

    /**
     * Relationship: All questions assigned to this exam.
     */
    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    /**
     * Relationship: Pending questions only.
     */
    public function pendingQuestions(): HasMany
    {
        return $this->hasMany(Question::class)
                    ->where('status', Question::STATUS_PENDING);
    }

    /**
     * Relationship: Approved questions only.
     */
    public function approvedQuestions(): HasMany
    {
        return $this->hasMany(Question::class)
                    ->where('status', Question::STATUS_APPROVED);
    }

    /**
     * Relationship: The moderator that created the exam.
     */
    public function moderator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderator_id');
    }

    /**
     * Relationship: The district to which this exam belongs.
     */
    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    /**
     * Scope: Only active exams.
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE)
                     ->where('is_active', true);
    }

    /**
     * Scope: Upcoming exams (draft and future start_time).
     */
    public function scopeUpcoming($query)
    {
        return $query->where('status', self::STATUS_DRAFT)
                     ->where('start_time', '>', now())
                     ->where('is_active', true);
    }

    /**
     * Scope: Completed exams.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    /**
     * Check if exam is currently active.
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE &&
               $this->is_active &&
               now()->between($this->start_time, $this->end_time);
    }

    /**
     * Check if the exam is upcoming (yet to start).
     */
    public function isUpcoming(): bool
    {
        return $this->status === self::STATUS_DRAFT &&
               $this->is_active &&
               now()->lt($this->start_time);
    }

    /**
     * Check if the exam has already ended.
     */
    public function hasEnded(): bool
    {
        return $this->status === self::STATUS_COMPLETED ||
               now()->gt($this->end_time);
    }

    /**
     * Automatically update exam status before saving.
     */
    protected static function booted()
    {
        static::saving(function ($exam) {
            if ($exam->end_time && now()->gt($exam->end_time)) {
                $exam->status = self::STATUS_COMPLETED;
            } elseif ($exam->start_time && now()->gt($exam->start_time)) {
                $exam->status = self::STATUS_ACTIVE;
            } else {
                $exam->status = self::STATUS_DRAFT;
            }
        });
    }
}
