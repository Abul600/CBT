<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Exam extends Model
{
    use HasFactory;

    // Status constants for consistency
    const STATUS_DRAFT = 'draft';
    const STATUS_ACTIVE = 'active';
    const STATUS_COMPLETED = 'completed';

    protected $fillable = [
        'name',
        'description',
        'duration',
        'start_time',
        'end_time',
        'status',
        'moderator_id',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time'   => 'datetime',
    ];

    /**
     * Relationship with questions
     */
    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    /**
     * Questions pending approval
     */
    public function pendingQuestions(): HasMany
    {
        return $this->hasMany(Question::class)
                    ->where('status', Question::STATUS_PENDING);
    }

    /**
     * Approved questions
     */
    public function approvedQuestions(): HasMany
    {
        return $this->hasMany(Question::class)
                    ->where('status', Question::STATUS_APPROVED);
    }

    /**
     * Moderator relationship
     */
    public function moderator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderator_id');
    }

    /**
     * Status scopes
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('status', self::STATUS_DRAFT)
                     ->where('start_time', '>', now());
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    /**
     * Status checkers
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE &&
               now()->between($this->start_time, $this->end_time);
    }

    public function isUpcoming(): bool
    {
        return $this->status === self::STATUS_DRAFT &&
               now()->lt($this->start_time);
    }

    public function hasEnded(): bool
    {
        return $this->status === self::STATUS_COMPLETED ||
               now()->gt($this->end_time);
    }

    /**
     * Automatically set status based on timestamps
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