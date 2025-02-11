<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Configuration extends Model
{
    protected $fillable = [
        'usd_rate',
        'email_verify',
        'currency_code',
        'currency_symbol',
        'paystack_perc',
        'paystack_fixed',
        'paystack_max',
        'withdrawal_min',
        'withdrawal_max',
        'expert_fee',
        'charges'
    ];
}
