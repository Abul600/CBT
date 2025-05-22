<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    protected $fillable = [
        'user_id',   // add this
        'exam_id',
        'score',
        'status',
        // add any other fields you want to mass assign
    ];

    // ...
}
