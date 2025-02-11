<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
        'profile_photo',
    ];
    protected $casts = [
        'specialization' => 'array',
    ];

    public function fullName()
    {
        return $this->first_name . " " . $this->last_name;
    }
}
