<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable =[
        'title',
        'number_of_users',
        'price',
        'duration',
        'type',
        'note',
        'fetures',
    ];

    protected function casts(): array
    {
        return [
            'fetures' => 'array'
        ];
    }

}
