<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Testemonial extends Model
{
    use HasFactory;
    protected $fillable=[
        "text",
        "title",
        "user_id",
        "anonymous",
    ];
}
