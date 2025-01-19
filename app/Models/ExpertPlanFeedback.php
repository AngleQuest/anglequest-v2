<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpertPlanFeedback extends Model
{
    protected $fillable = [
        'user_id',
        'expert_id',
        'plan_id',
        'remark',
        'rating',
        'descriptions',
        'title',
        'role',
        'date',
        'performance_rating',
        'coach',
        'completed',
        'rating_figure'
    ];
    protected $casts = [
        'descriptions' => 'array',
    ];
}
