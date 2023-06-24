<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProfileSettings extends Model
{
    use HasFactory;
    protected $fillable=[
        'user_id',
        'category_id',
        'status'
    ];
}
