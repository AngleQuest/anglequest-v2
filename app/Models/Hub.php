<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Hub extends Model
{
    protected $fillable = [
        'user_id',
        'visibility',
        'name',
        'category',
        'specialization',
        'description',
        'hub_goals'
    ];
    protected $casts = [
        'specialization' => 'array',
    ];
    function members(): HasMany
    {
        return $this->HasMany(HubMember::class, 'hub_id');
    }
}
