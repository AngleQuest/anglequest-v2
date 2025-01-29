<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
