<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class daylog extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'text',
        'user_id'
    ];
}
