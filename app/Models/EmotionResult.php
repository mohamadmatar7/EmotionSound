<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmotionResult extends Model
{
    protected $fillable = [
        'name',
        'age',
        'gender',
        'emotional_state',
        'answers',
        'final_emotion',
    ];
}
