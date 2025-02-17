<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class UserHub extends Model
{
    protected $fillable = [
        'user_id',
        'hub_id'
    ];

    function user(): BelongsTo
    {
        return $this->BelongsTo(User::class);
    }
}
