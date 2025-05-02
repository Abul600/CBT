<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'paper_setter_id',
        'moderator_id',
        'exam_id',
        'question_text',
        'option_a',
        'option_b',
        'option_c',
        'option_d',
        'correct_option',
        'type',
        'marks',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
        'correct_option' => 'string',
        'marks' => 'integer',
    ];

    // ====== Status Constants ======
    public const STATUS_DRAFT    = 'draft';
    public const STATUS_SENT     = 'sent';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

    // ====== Question Type Constants ======
    public const TYPE_MCQ1        = 'mcq1';         // Single correct option
    public const TYPE_MCQ2        = 'mcq2';         // Multiple correct options (if needed)
    public const TYPE_DESCRIPTIVE = 'descriptive';

    // ====== Relationships ======

    /**
     * The paper setter who created the question.
     */
    public function paperSetter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'paper_setter_id');
    }

    /**
     * The moderator who reviewed the question.
     */
    public function moderator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderator_id');
    }

    /**
     * The exam the question is assigned to.
     */
    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    // ====== Query Scopes ======

    /**
     * Scope: Draft questions (not yet submitted).
     */
    public function scopeDraft($query)
    {
        return $query->where('status', self::STATUS_DRAFT);
    }

    /**
     * Scope: Sent to moderator.
     */
    public function scopeSent($query)
    {
        return $query->where('status', self::STATUS_SENT);
    }

    /**
     * Scope: Approved by moderator.
     */
    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    /**
     * Scope: Rejected by moderator.
     */
    public function scopeRejected($query)
    {
        return $query->where('status', self::STATUS_REJECTED);
    }
}
