<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hub extends Model
{
    protected $fillable = [
        'user_id',
        'visibility',
        'name',
        'hub_description',
        'meeting_day',
        'from',
        'to',
        'coaching_hub_fee',
        'coaching_hub_goals',
        'coaching_hub_limit',
        'category',
    ];
}
