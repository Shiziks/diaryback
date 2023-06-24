<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MoodUser extends Model
{
    use HasFactory;
    protected $fillable = [
        'mood_id',
        'user_id'
    ];
}
