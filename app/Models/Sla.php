<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sla extends Model
{
    protected $fillable = [
        'name',
        'features',
    ];
}
