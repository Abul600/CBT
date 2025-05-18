<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudyMaterial extends Model
{
    protected $fillable = [
        'title',
        'description',
        'file_path',
        'district_id',
    ];

    /**
     * Get the district that owns the study material.
     */
    public function district()
    {
        return $this->belongsTo(District::class);
    }
}
