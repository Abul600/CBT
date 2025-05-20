<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Exam extends Model
{
    use HasFactory;

    // ====== Status Constants ======
    public const STATUS_DRAFT     = 'draft';
    public const STATUS_ACTIVE    = 'active';
    public const STATUS_COMPLETED = 'completed';

    // ====== Fillable Attributes ======
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

    // ====== Attribute Casting ======
    protected $casts = [
        'start_time' => 'datetime',
        'end_time'   => 'datetime',
        'is_active'  => 'boolean',
    ];

    // ====== Relationships ======

    /**
     * Questions assigned to this exam (many-to-many).
     */
    public function questions(): BelongsToMany
    {
        return $this->belongsToMany(Question::class, 'exam_question');
    }

    /**
     * Pending questions only (via pivot).
     */
    public function pendingQuestions(): BelongsToMany
    {
        return $this->questions()->where('status', Question::STATUS_PENDING);
    }

    /**
     * Approved questions only (via pivot).
     */
    public function approvedQuestions(): BelongsToMany
    {
        return $this->questions()->where('status', Question::STATUS_APPROVED);
    }

    /**
     * The moderator that created the exam.
     */
    public function moderator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderator_id');
    }

    /**
     * The district the exam belongs to.
     */
    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    // ====== Scopes ======

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE)
                     ->where('is_active', true);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('status', self::STATUS_DRAFT)
                     ->where('start_time', '>', now())
                     ->where('is_active', true);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    // ====== Helper Methods ======

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE &&
               $this->is_active &&
               now()->between($this->start_time, $this->end_time);
    }

    public function isUpcoming(): bool
    {
        return $this->status === self::STATUS_DRAFT &&
               $this->is_active &&
               now()->lt($this->start_time);
    }

    public function hasEnded(): bool
    {
        return $this->status === self::STATUS_COMPLETED ||
               now()->gt($this->end_time);
    }

    // ====== Model Events ======

    /**
     * Automatically update exam status based on time before saving.
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
