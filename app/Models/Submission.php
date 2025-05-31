<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Submission extends Model
{
    protected $fillable = [
        'exam_id',
        'student_id',
        'submitted_at',
        'is_graded',
    ];

    /**
     * The exam this submission belongs to.
     */
    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    /**
     * The student who submitted this submission.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Descriptive answers submitted for this submission.
     */
    public function answers(): HasMany
    {
        return $this->hasMany(DescriptiveAnswer::class, 'submission_id');
    }
}
