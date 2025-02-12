<?php

namespace App\Models;

use phpseclib3\System\SSH\Agent;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{

    // protected $fillable = [
    //     'admin_id',
    //     'role_id',
    //     'table_name',
    //     'action',
    //     'os',
    //     'browser',
    //     'ip_address',
    // ];
    // public function admin(): BelongsTo
    // {
    //     return $this->BelongsTo(Admin::class,'admin_id');
    // }
    // static function createRow($adm, $role_id, $table, $action)
    // {
    //     return self::create([
    //         'admin_id' => $adm->id,
    //         'role_id' => $role_id,
    //         'table_name' => $table,
    //         'action' => $action,
    //         'browser' => Agent::browser(),
    //         'os' => Agent::platform(),
    //         'ip_address' => request()->ip(),
    //     ]);
    // }
    // public static function createAdminLog($admin, $roleID, $tableName, $action)
    // {
    //     return self::create([
    //         'admin_id'         => $admin,
    //         'role_id'       => $roleID,
    //         'table_name'    => $tableName,
    //         'action'        => $action,
    //         'ip_address' => request()->ip(),
    //         'browser' => Agent::browser(),
    //         'os' => Agent::platform(),
    //     ]);
    // }
}
