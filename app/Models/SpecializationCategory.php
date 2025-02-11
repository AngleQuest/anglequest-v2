<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SpecializationCategory extends Model
{
    protected $fillable = [
        'name'
    ];
    function specializations(): HasMany
    {
        return $this->hasMany(Specialization::class);
    }
}
