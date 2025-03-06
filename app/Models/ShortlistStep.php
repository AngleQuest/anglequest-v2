<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShortlistStep extends Model
{
    protected $fillable = [
        'user_id',
        'last_step',
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}
