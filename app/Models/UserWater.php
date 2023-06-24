<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserWater extends Model
{
    use HasFactory;
    protected $fillable= [
        'id',
        'user_id',
        'waterglass_id',
        'created_at'
    ];
}
