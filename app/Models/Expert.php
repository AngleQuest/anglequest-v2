<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expert extends Model
{
    protected $fillable = [
        'user_id',
        'category',
        'specialization',
        'time'
    ];
    protected $casts = [
        'specialization' => 'array',
    ];
    // public function supportRequests()
    // {
    //     return $this->hasMany(SupportRequest::class);
    // }
}
