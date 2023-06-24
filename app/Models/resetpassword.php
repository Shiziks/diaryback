<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class resetpassword extends Model
{
    use HasFactory;

    protected $table = 'reset_passwords';

    protected $fillable = [
        'password_token',
        'user_id',
        'email'
    ];
}
