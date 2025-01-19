<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hub extends Model
{
    protected $fillable = [
        'user_id',
        'visibility',
        'name',
        'category',
        'specialization',
        'hub_description',
        'hub_goals'
    ];
}
