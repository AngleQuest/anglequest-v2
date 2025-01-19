<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    protected $fillable = [
        'title',
        'cost',
        'country_id',
        'period',
        'tagline',
        'details',
        'status',
    ];
}
