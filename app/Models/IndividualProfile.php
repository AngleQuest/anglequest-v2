<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IndividualProfile extends Model
{
    protected $fillable = [
        'user_id',
        'category',
        'first_name',
        'last_name',
        'email',
        'phone',
        'dob',
        'current_role',
        'target_role',
        'gender',
        'specialization',
        'yrs_of_experience',
        'about',
        'location',
        'preferred_mode',
        'salary_range',
        'country',
        'profile_photo',
    ];
    protected $casts = [
        'specialization' => 'array',
    ];

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class,'user_id');
    }
    public function fullName()
    {
        return $this->first_name . " " . $this->last_name;
    }
}
