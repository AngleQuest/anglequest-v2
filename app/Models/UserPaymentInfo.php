<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPaymentInfo extends Model
{
    protected $fillable = [
        'user_id',
        'account_name',
        'account_number',
        'bank',
        'country'
    ];
}
