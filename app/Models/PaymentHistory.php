<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentHistory extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'payment_id',
        'plan_id',
        'plan_start',
        'plan_end',
        'amount',
        'method',
        'payment_type',
        'status'
    ];
    function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class, 'plan_id');
    }
}
