<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HubMeeting extends Model
{
    protected $fillable = [
        'user_id',
        'meeting_topic',
        'description',
        'meeting_date',
        'meeting_time',
        'expert_id',
        'hub_id',
        'candidate_link',
        'expert_link',
        'status'
    ];
}
