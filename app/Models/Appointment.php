<?php

namespace App\Models;

use App\Models\Expert;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    protected $fillable = [
        'user_id',
        'specialization',
        'category',
        'title',
        'role',
        'description',
        'job_description',
        'cv',
        'expert_id',
        'individual_name',
        'rating',
        'status',
        'expert_name',
        'appointment_date',
        'appointment_time',
        'expert_link',
        'individual_link',

    ];
    public function expert()
    {
        return $this->belongsTo(Expert::class, 'user_id');
    }
}
