<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserHub extends Model
{
    protected $fillable = [
        'user_id',
        'hub_id',
        'expert_id',
        'hub_count'
    ];
}
