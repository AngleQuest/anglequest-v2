<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpertSkillAnalysisFeedback extends Model
{
    protected $fillable = [
        'user_id',
        'expert_id',
        'skill_analysis_id',
        'remark',
        'rating',
        'descriptions',
        'role',
        'types',
        'starting_level',
        'target_level',
        'date',
        'completed',
        'rating_figure',
        'expert_analysis',
      ];
      protected $casts = [
        'descriptions' => 'array',
        'expert_analysis' => 'array'
    ];
}
