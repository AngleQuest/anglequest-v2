<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CvAnalysis extends Model
{
    protected $fillable = [
        'user_id',
        'result',
    ];
    protected $casts = [
        'result' => 'array',
    ];
}
