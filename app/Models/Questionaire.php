<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Questionaire extends Model
{
    protected $fillable = [
        'user_id',
        'category',
        'title',
        'question_answer',
        'answer',
    ];
    protected function casts(): array
    {
        return [
            'question_answer' => 'array'
        ];
    }
}
