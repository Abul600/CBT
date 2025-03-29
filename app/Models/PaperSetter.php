<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaperSetter extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'moderator_id'];

    // Relationship with User (Paper Setter)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relationship with User (Moderator)
    public function moderator()
    {
        return $this->belongsTo(User::class, 'moderator_id');
    }
}
