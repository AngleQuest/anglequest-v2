<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebsiteSetting extends Model
{
    protected $fillable = [
        'logo_url',
        'dashboard_logo_url',
        'favicon_url',
        'name',
        'business_name',
        'business_address',
        'business_phone',
        'business_email',
        'site_description',
        'vision',
        'mission',
        'motto',
    ];
}
