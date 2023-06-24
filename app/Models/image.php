<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class image extends Model
{
    use HasFactory;
    protected $fillable = [
        'file_name',
        'file_path',
        'file_size',
        'file_type',
        'user_id',
        'daylog_id'
    ];
}
