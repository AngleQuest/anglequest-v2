<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expert extends Model
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
        'available_days',
        'available_time',
        'yrs_of_experience',
        'about',
        'location',
        'profile_photo',
    ];
    protected $casts = [
        'specialization' => 'array',
        'available_days' => 'array',
        'available_time' => 'array',
    ];
    // public function supportRequests()
    // {
    //     return $this->hasMany(SupportRequest::class);
    // }

    public function user(): BelongsTo
    {
        return $this->BelongsTo(User::class, 'user_id');
    }
}
