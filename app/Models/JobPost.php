<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JobPost extends Model
{
    protected $fillable = [
        'user_id',
        'category',
        'speacialization',
        'role_level',
        'candidates',
        'link',
        'description',
        'job_title',
        'status'
    ];
    public function appointments(): HasMany
    {
        return $this->HasMany(Appointment::class, 'job_id')->where('is_business', 1);
    }
}
