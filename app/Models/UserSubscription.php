<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSubscription extends Model
{
    protected $fillable = [
        'user_id',
        'subscription_plan_id',
        'payment_id',
        'plan_start',
        'plan_end',
        'amount',
        'plan_name',
        'authorization_data',
        'authorization_code',
        'authorization_email',
        'status',
    ];
    protected $casts = [
        'authorization_data' => 'array',
    ];
    function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class, 'subscription_plan_id');
    }
}
