<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DescriptiveAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'submission_id',
        'question_id',
        'answer_text',
        'marks',
        'graded_by',
        'graded_at',
        'user_id', // Added user_id to allow mass-assignment
    ];

    protected $casts = [
        'graded_at' => 'datetime',
        'marks'     => 'integer',
    ];

    // ====== Relationships ======

    /**
     * The submission this answer belongs to.
     */
    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class);
    }

    /**
     * The question this answer is for.
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    /**
     * The user who graded this answer.
     */
    public function gradedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'graded_by');
    }

    /**
     * The student who submitted this answer.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ====== Scopes ======

    public function scopeUngraded($query)
    {
        return $query->whereNull('marks');
    }

    public function scopeGraded($query)
    {
        return $query->whereNotNull('marks');
    }

    // ====== Validation Rules ======

    public static function rules()
    {
        return [
            'answer_text'   => 'required|string|max:2000',
            'marks'         => 'nullable|integer|min:0',
            'question_id'   => 'required|exists:questions,id',
            'submission_id' => 'required|exists:submissions,id',
            'user_id'       => 'required|exists:users,id', // ✅ Optional: add validation for user_id
        ];
    }

    // ====== Helper Methods ======

    public function isGraded(): bool
    {
        return !is_null($this->marks);
    }

    public function maxMarks(): int
    {
        return $this->question->marks ?? 0;
    }
}
