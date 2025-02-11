<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppointmentGuide extends Model
{
    protected $fillable = [
        "user_id",
        "specialization",
        "topic",
        "available_days",
        "description",
        "expert_name",
        "guides",
        "location",
        "time_zone",
    ];
    protected $casts = [
        'specialization' => 'array',
    ];
}
