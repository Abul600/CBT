<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

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
        'application_start',
        'application_end',
        'exam_start',
        'moderator_id',
        'district_id',
        'status',
        'is_active',
    ];

    // ====== Attribute Casting ======
    protected $casts = [
        'application_start' => 'datetime',
        'application_end'   => 'datetime',
        'exam_start'        => 'datetime',
        'duration'          => 'integer',
        'is_active'         => 'boolean',
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
     * Pending questions only.
     */
    public function pendingQuestions(): BelongsToMany
    {
        return $this->questions()->where('status', Question::STATUS_PENDING);
    }

    /**
     * Approved questions only.
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

    /**
     * Users who have applied for this exam (many-to-many).
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    // ====== Accessors ======

    /**
     * Get calculated exam end time with type safety.
     */
    public function getExamEndAttribute(): ?Carbon
    {
        return $this->exam_start?->copy()->addMinutes((int) $this->duration);
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
                     ->where('exam_start', '>', now())
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
               now()->between($this->exam_start, $this->exam_end);
    }

    public function isUpcoming(): bool
    {
        return $this->status === self::STATUS_DRAFT &&
               $this->is_active &&
               now()->lt($this->exam_start);
    }

    public function hasEnded(): bool
    {
        return $this->status === self::STATUS_COMPLETED ||
               now()->gt($this->exam_end);
    }

    public function canApply(): bool
    {
        if (!$this->application_start || !$this->application_end) {
            return false;
        }

        return now()->between(
            $this->application_start->copy()->subMinutes(5),
            $this->application_end->copy()->addMinutes(5)
        );
    }

    public function canJoinExam(): bool
    {
        return now()->between(
            $this->exam_start->copy()->subMinutes(10),
            $this->exam_end
        );
    }

    /**
     * Check if the given (or current) user has applied for this exam.
     */
    public function hasApplied(User $user = null): bool
    {
        $user = $user ?? auth()->user();
        return $this->users()->where('user_id', $user->id)->exists();
    }

    // ====== Model Events ======

    protected static function booted()
    {
        static::saving(function ($exam) {
            if ($exam->exam_start && $exam->duration) {
                $end = $exam->exam_end;

                if ($end && now()->gt($end)) {
                    $exam->status = self::STATUS_COMPLETED;
                } elseif (now()->gt($exam->exam_start)) {
                    $exam->status = self::STATUS_ACTIVE;
                } else {
                    $exam->status = self::STATUS_DRAFT;
                }
            }
        });
    }
}
