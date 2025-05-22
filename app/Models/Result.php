<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    protected $fillable = [
        'user_id',
        'exam_id',
        'score',
        'total',   // optional: total possible score
        'status',  // optional: pass/fail or custom
    ];

    /**
     * Get the exam this result belongs to.
     */
    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    /**
     * Get the user (student) who owns this result.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
