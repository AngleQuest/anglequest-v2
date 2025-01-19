<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSubscription extends Model
{
    protected $fillable = [
        'user_id',
        'subscription_plan_id',
        'payment_id',
        'plan_start',
        'plan_end',
        'authorization_data',
        'status',
    ];
}
