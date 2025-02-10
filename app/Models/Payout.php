<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Payout extends Model
{
    protected $fillable = [
        'user_id',
        'amount',
        'account_name',
        'account_number',
        'bank',
        'status',
        'date_paid',
    ];

    function bank_info(): HasOne {
        return $this->hasOne(UserPaymentInfo::class, 'user_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withDefault([
            'name' => 'guest'
        ]);
    }
}
