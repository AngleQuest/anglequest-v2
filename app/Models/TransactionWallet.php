<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransactionWallet extends Model
{
    protected $fillable = [
        'user_id',
        'payment_id',
        'type',
        'credit',
        'debit',
        'remark',
        'status',
    ];
    function user() : BelongsTo
    {
        return $this->belongsTo(User::class,'user_id')->withDefault([
            'name' => 'guest',
        ]);
    }
}
