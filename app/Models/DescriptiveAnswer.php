<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DescriptiveAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'answer',
        'marks',
        'graded_by',
        'graded_at',
        'user_id',
        'exam_id',
        'question_id'
    ];

    protected $casts = [
        'graded_at' => 'datetime',
        'marks' => 'integer'
    ];

    // Relationships
    public function student()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function gradedBy()
    {
        return $this->belongsTo(User::class, 'graded_by');
    }

    // Scopes
    public function scopeUngraded($query)
    {
        return $query->whereNull('marks');
    }

    public function scopeGraded($query)
    {
        return $query->whereNotNull('marks');
    }

    // Validation rules
    public static function rules()
    {
        return [
            'answer' => 'required|string|max:2000',
            'marks' => 'nullable|integer|min:0',
            'question_id' => 'required|exists:questions,id',
            'exam_id' => 'required|exists:exams,id',
            'user_id' => 'required|exists:users,id'
        ];
    }

    // Helper methods
    public function isGraded()
    {
        return !is_null($this->marks);
    }

    public function maxMarks()
    {
        return $this->question->marks ?? 0;
    }
}