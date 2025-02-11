<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Specialization extends Model
{
    protected $fillable = [
        'specialization_category_id',
        'name'
    ];
    function category(): BelongsTo
    {
        return $this->BelongsTo(SpecializationCategory::class,'specialization_category_id');
    }
}
