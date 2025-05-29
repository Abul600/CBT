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

    public const STATUS_DRAFT     = 'draft';
    public const STATUS_ACTIVE    = 'active';
    public const STATUS_COMPLETED = 'completed';

    protected $fillable = [
        'name',
        'description',
        'duration',
        'application_start',
        'application_end',
        'exam_start',
        'moderator_id',     // ✅ Nullable foreign key
        'district_id',      // ✅ Nullable foreign key
        'status',
        'is_active',
        'type',
        'is_released',
        'released_at',
        'converted_to_mock',
        'converted_at',
    ];

    protected $casts = [
        'application_start' => 'datetime',
        'application_end'   => 'datetime',
        'exam_start'        => 'datetime',
        'duration'          => 'integer',
        'is_active'         => 'boolean',
        'type'              => 'string',
        'is_released'       => 'boolean',
        'released_at'       => 'datetime',
        'converted_to_mock' => 'boolean',
        'converted_at'      => 'datetime',
    ];

    // Relationship: Questions (with pivot marks)
    public function questions(): BelongsToMany
    {
        return $this->belongsToMany(Question::class, 'exam_question')->withPivot('marks');
    }

    public function pendingQuestions(): BelongsToMany
    {
        return $this->questions()->where('status', Question::STATUS_PENDING);
    }

    public function approvedQuestions(): BelongsToMany
    {
        return $this->questions()->where('status', Question::STATUS_APPROVED);
    }

    public function moderator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderator_id');
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    public function studyMaterials()
    {
        return $this->hasMany(StudyMaterial::class, 'original_exam_id');
    }

    public function descriptiveAnswers()
    {
        return $this->hasManyThrough(
            DescriptiveAnswer::class,
            Question::class,
            'exam_id',
            'question_id',
            'id',
            'id'
        );
    }

    public function getExamEndAttribute(): ?Carbon
    {
        return $this->exam_start?->copy()->addMinutes((int) $this->duration);
    }

    public function getIsMockAttribute(): bool
    {
        return $this->type === 'mock';
    }

    public function getLocalApplicationEndAttribute(): ?Carbon
    {
        return $this->application_end?->setTimezone(auth()->user()->timezone ?? config('app.timezone'));
    }

    public function getFormattedApplicationPeriodAttribute()
    {
        return [
            'start' => optional($this->application_start)->format('M j, Y H:i'),
            'end'   => optional($this->application_end)->format('M j, Y H:i'),
        ];
    }

    public function getStatusAttribute(): string
    {
        if (!$this->exam_start || !$this->exam_end) {
            return 'invalid';
        }

        if ($this->converted_to_mock) {
            return 'converted_mock';
        }

        if (now()->gt($this->exam_end)) {
            return self::STATUS_COMPLETED;
        }

        if (now()->gte($this->exam_start)) {
            return self::STATUS_ACTIVE;
        }

        return self::STATUS_DRAFT;
    }

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

    public function scopeReleased($query)
    {
        return $query->where('is_released', true);
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE &&
               $this->is_active &&
               $this->exam_start && $this->exam_end &&
               now()->between($this->exam_start, $this->exam_end);
    }

    public function isUpcoming(): bool
    {
        return $this->status === self::STATUS_DRAFT &&
               $this->is_active &&
               $this->exam_start &&
               now()->lt($this->exam_start);
    }

    public function hasEnded(): bool
    {
        return $this->exam_end && (
            $this->status === self::STATUS_COMPLETED || now()->gt($this->exam_end)
        );
    }

    public function canApply(): bool
    {
        if ($this->type !== 'scheduled') return false;
        if (!$this->application_start || !$this->application_end) return false;

        $now = now();
        return $now->between(
            $this->application_start->copy()->subMinutes(5),
            $this->application_end->copy()->addMinutes(5)
        );
    }

    public function canJoinExam(): bool
    {
        if ($this->type === 'mock') return true;

        return $this->exam_start && $this->exam_end &&
               now()->between(
                   $this->exam_start->copy()->subMinutes(10),
                   $this->exam_end
               );
    }

    public function hasApplied(User $user = null): bool
    {
        $user = $user ?? auth()->user();
        return $this->users()->where('user_id', $user->id)->exists();
    }

    public function hasStarted(): bool
    {
        return $this->exam_start && now()->gte($this->exam_start);
    }

    public function isConverted(): bool
    {
        return $this->converted_to_mock;
    }

    public function isCurrentlyRunning(): bool
    {
        return $this->exam_start &&
               $this->exam_end &&
               now()->between($this->exam_start, $this->exam_end) &&
               $this->is_active &&
               $this->status !== 'invalid';
    }

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

        static::retrieved(function ($exam) {
            if ($exam->shouldConvertToMock()) {
                $exam->update([
                    'type' => 'mock',
                    'converted_to_mock' => true,
                    'converted_at' => now()
                ]);
            }
        });
    }

    public function shouldConvertToMock(): bool
    {
        return $this->type === 'scheduled' &&
               !$this->converted_to_mock &&
               $this->exam_start &&
               now()->diffInHours($this->exam_start) >= 24;
    }
}
