<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSla extends Model
{
    protected $fillable = [
        'user_id',
        'sla_id',
    ];
}
