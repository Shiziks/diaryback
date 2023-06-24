<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSleep extends Model
{
    use HasFactory;
    protected $fillable=[
        'user_id',
        'sleep_hours_id'
    ];
}
