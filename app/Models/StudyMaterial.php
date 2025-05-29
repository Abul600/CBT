<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StudyMaterial extends Model
{
    // ====== Fillable Fields ======
    protected $fillable = [
        'title',
        'description',
        'file_path',
        'district_id',
        'original_exam_id',
    ];

    // ====== Relationships ======

    /**
     * The district that owns the study material.
     */
    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    /**
     * The exam from which this study material was originally derived.
     */
    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class, 'original_exam_id');
    }

    /**
     * Questions linked to this study material.
     */
    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }
}
