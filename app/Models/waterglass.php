<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class waterglass extends Model
{
    use HasFactory;
    protected $fillable=[
        'id',
        'glass_number',
        'icon',
        'created_at'
    ];
}
