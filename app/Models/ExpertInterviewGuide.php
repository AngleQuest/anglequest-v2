<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpertInterviewGuide extends Model
{
    protected $fillable = [
        'user_id',
        'target_level',
        'available_days',
        'available_times',
        'guides'
    ];
}
