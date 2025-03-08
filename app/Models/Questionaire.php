<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Questionaire extends Model
{
    protected $fillable = [
        'user_id',
        'category',
        'title',
        'question',
        'answer',
    ];
    protected function casts(): array
    {
        return [
            'question' => 'array',
            'answer' => 'array'
        ];
    }
}
