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
        'gender',
        'specialization',
        'yrs_of_experience',
        'about',
        'location',
        'profile_photo',
    ];
}
