<?php

namespace App\Models;

use Jenssegers\Agent\Facades\Agent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AdminLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'admin_id',
        'role_id',
        'table_name',
        'action',
        'os',
        'browser',
        'ip_address',
    ];
  
}
