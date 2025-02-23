<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppointmentFeedback extends Model
{
    protected $fillable = [
        'user_id',
        'expert_id',
        'appointment_id',
        'note',
        'rating',
        'key_strengths',
        'improvements',
        'recommendation',
    ];
    protected $casts = [
        'key_strengths' => 'array',
        'improvements' => 'array',
    ];
}
