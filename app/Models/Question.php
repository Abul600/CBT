<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

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
        'district_id',
        'paper_setter_id',
        'moderator_id',
        'question_text',
        'option_a',
        'option_b',
        'option_c',
        'option_d',
        'correct_option',
        'type',
        'marks',
        'status',
        'sent_to_moderator_id',
        'sent_at',
    ];

    // ====== Casts ======
    protected $casts = [
        'status'          => 'string',
        'correct_option'  => 'string',
        'marks'           => 'integer',
        'sent_at'         => 'datetime',
    ];

    // ====== Relationships ======

    /**
     * Exams this question belongs to (many-to-many).
     */
    public function exams(): BelongsToMany
    {
        return $this->belongsToMany(Exam::class, 'exam_question');
    }

    /**
     * Paper setter who created this question.
     */
    public function paperSetter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'paper_setter_id');
    }

    /**
     * Moderator associated with this question.
     */
    public function moderator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderator_id');
    }

    /**
     * Moderator to whom this question was sent.
     */
    public function sentToModerator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sent_to_moderator_id');
    }

    // ====== Query Scopes ======

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
}
