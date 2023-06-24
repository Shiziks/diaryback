<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class energy extends Model
{
    use HasFactory;
    protected $fillable = [
        'energy_name',
        'icon',
        'icon_prefix'
    ];
}
