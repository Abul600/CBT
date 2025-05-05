<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Exam extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'duration',
        'start_time',
        'end_time',
        'status',
        'moderator_id',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time'   => 'datetime',
    ];

    /**
     * All questions assigned to this exam.
     */
    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    /**
     * Questions pending moderator approval (status = 'sent').
     */
    public function pendingQuestions(): HasMany
    {
        return $this->hasMany(Question::class)
                    ->where('status', Question::STATUS_SENT);
    }

    /**
     * Questions approved by the moderator.
     */
    public function approvedQuestions(): HasMany
    {
        return $this->hasMany(Question::class)
                    ->where('status', Question::STATUS_APPROVED);
    }

    /**
     * Moderator assigned to this exam.
     */
    public function moderator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderator_id');
    }

    /**
     * Determine if the exam is currently active.
     */
    public function isActive(): bool
    {
        return now()->between($this->start_time, $this->end_time);
    }

    /**
     * Determine if the exam is scheduled for a future time.
     */
    public function isUpcoming(): bool
    {
        return now()->lt($this->start_time);
    }

    /**
     * Determine if the exam has ended.
     */
    public function hasEnded(): bool
    {
        return now()->gt($this->end_time);
    }
}
