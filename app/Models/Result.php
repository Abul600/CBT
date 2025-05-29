<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    /**
     * Fields allowed for mass assignment.
     */
    protected $fillable = [
        'user_id',
        'exam_id',
        'mcq_score',
        'descriptive_score',
        'score',
        'total',
        'percentage',
        'status',
        'auto_graded',
    ];

    /**
     * Default attribute values.
     */
    protected $attributes = [
        'mcq_score' => 0,
        'descriptive_score' => 0,
        'score' => 0,
        'percentage' => 0,
        'auto_graded' => false,
    ];

    /**
     * Custom attributes to be appended during serialization.
     */
    protected $appends = ['passed', 'percentage'];

    /**
     * Get the exam associated with this result.
     */
    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    /**
     * Get the user (student) associated with this result.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Determine if the student passed the exam.
     * Uses 50% as default pass threshold.
     */
    public function getPassedAttribute()
    {
        return $this->total > 0 && ($this->score / $this->total) >= 0.5;
    }

    /**
     * Get the calculated percentage.
     */
    public function getPercentageAttribute()
    {
        return $this->total > 0 ? round(($this->score / $this->total) * 100, 2) : 0;
    }
}
