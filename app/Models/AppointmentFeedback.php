<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppointmentFeedback extends Model
{
    protected $fillable = [
        'user_id',
        'expert_id',
        'appointment_id',
        'note'
    ];
}
