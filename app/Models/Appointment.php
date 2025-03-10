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
        'type',
        'role',
        'is_business',
        'job_id',
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
        'individual_photo',
        'expert_photo',
    ];

    protected $hidden = [
        'job_id',
        'is_business',
    ];
    public function expert()
    {
        return $this->belongsTo(Expert::class, 'expert_id');
    }
    public function individual()
    {
        return $this->belongsTo(IndividualProfile::class, 'user_id');
    }
}
