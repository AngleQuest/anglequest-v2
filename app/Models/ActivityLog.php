<?php

namespace App\Models;

use Jenssegers\Agent\Facades\Agent;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{

    protected $fillable = [
        'user',
        'content',
        'os',
        'browser',
        'ip_address',
    ];

    static function createRow($name, $content)
    {
        return self::create([
            'user' => $name,
            'content' => $content,
            'browser' => Agent::browser(),
            'os' => Agent::platform(),
            'ip_address' => request()->ip(),
        ]);
    }
    public static function createAdminLog($admin, $roleID, $tableName, $action)
    {
        return self::create([
            'admin_id'         => $admin,
            'role_id'       => $roleID,
            'table_name'    => $tableName,
            'action'        => $action,
            'ip_address' => request()->ip(),
            'browser' => Agent::browser(),
            'os' => Agent::platform(),
        ]);
    }
}
