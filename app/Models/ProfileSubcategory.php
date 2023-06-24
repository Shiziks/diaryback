<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfileSubcategory extends Model
{
    use HasFactory;
    protected $fillable=[
        'subcategory_name',
        'profilecategory_id',
        'admin_status',
    ];
}
