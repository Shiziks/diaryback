<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppImage extends Model
{
    use HasFactory;
    protected $fillable=[
        'name',
        'type',
        'size',
        'path'
    ];
}
