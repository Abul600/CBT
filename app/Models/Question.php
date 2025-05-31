<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    use HasFactory;

    // ====== Status Constants ======
    public const STATUS_DRAFT    = 'draft';
    public const STATUS_PENDING  = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

    // ====== Question Type Constants ======
    public const TYPE_MCQ1        = 'mcq1';
    public const TYPE_MCQ2        = 'mcq2';
    public const TYPE_DESCRIPTIVE = 'descriptive';

    // ====== Fillable Fields ======
    protected $fillable = [
        'text',
        'type',
        'marks',
        'district_id',
        'paper_setter_id',
        'moderator_id',
        'question_text',
        'option_a',
        'option_b',
        'option_c',
        'option_d',
        'correct_option',
        'status',
        'sent_to_moderator_id',
        'sent_at',
    ];

    // ====== Casts ======
    protected $casts = [
        'status'         => 'string',
        'correct_option' => 'array',   // supports multiple answers for MCQ2
        'marks'          => 'integer',
        'sent_at'        => 'datetime',
    ];

    // ====== Relationships ======

    /**
     * Many-to-many: This question can belong to multiple exams.
     */
    public function exams(): BelongsToMany
    {
        return $this->belongsToMany(Exam::class, 'exam_question')->withPivot('marks');
    }

    /**
     * The paper setter who created this question.
     */
    public function paperSetter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'paper_setter_id');
    }

    /**
     * The moderator responsible for this question.
     */
    public function moderator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderator_id');
    }

    /**
     * The moderator to whom this question was sent for review.
     */
    public function sentToModerator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sent_to_moderator_id');
    }

    /**
     * Optional: Original exam this question may belong to.
     */
    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    /**
     * Optional: Study material (if derived from it).
     */
    public function studyMaterial(): BelongsTo
    {
        return $this->belongsTo(StudyMaterial::class);
    }

    /**
     * The options related to this question (for MCQs).
     */
    public function options(): HasMany
    {
        return $this->hasMany(Option::class);
    }

    /**
     * The answers submitted for this question.
     */
    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class);
    }

    /**
     * The descriptive answers submitted for this question.
     */
    public function descriptiveAnswers(): HasMany
    {
        return $this->hasMany(DescriptiveAnswer::class);
    }

    // ====== Scopes ======

    public function scopeDraft($query)
    {
        return $query->where('status', self::STATUS_DRAFT);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopeRejected($query)
    {
        return $query->where('status', self::STATUS_REJECTED);
    }

    // ====== Helper Methods ======

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    public function getIsDescriptiveAttribute(): bool
    {
        return $this->type === self::TYPE_DESCRIPTIVE;
    }

    // ====== Model Event Validation ======

    protected static function booted()
    {
        static::saving(function ($question) {
            if ($question->exam && $question->exam->type === 'mock') {
                if (!in_array($question->type, [self::TYPE_MCQ1, self::TYPE_MCQ2])) {
                    throw new \Exception("Descriptive questions are not allowed in mock exams.");
                }
            }
        });
    }
}
