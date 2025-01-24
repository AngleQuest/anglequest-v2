<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminBank extends Model
{
    protected $fillable = [
        'account_name',
        'account_number',
        'bank_name',
        'country',
    ];
}
