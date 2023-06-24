<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gratitude extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'gratitudes',
        'group_id'
    ];
}
