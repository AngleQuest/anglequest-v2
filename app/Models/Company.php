<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    protected $fillable = [
        'name',
        'administrator_name',
        'email',
        'business_email',
        'address',
        'nda_file',
        'company_logo',
        'business_reg_number',
        'business_phone',
        'company_size',
        'website',
        'about',
        'service_type',
        'country',
        'city',
        'state',
    ];
    
    function employees(): HasMany
    {
        return $this->hasMany(User::class, 'company_id');
    }
}
