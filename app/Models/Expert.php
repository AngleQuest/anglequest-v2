<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expert extends Model
{
    protected $fillable = [
        'user_id',
        'category',
        'name',
        'email',
        'phone',
        'specialization',
        'available_days',
        'about',
        'location',
        'profile_photo',
    ];
    protected $casts = [
        'specialization' => 'array',
        'available_days' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->BelongsTo(User::class, 'user_id');
    }
    public function fullName()
    {
        return $this->first_name . " " . $this->last_name;
    }
}
