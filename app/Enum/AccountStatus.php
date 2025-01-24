<?php

namespace App\Enum;

enum AccountStatus: string
{
    const ACTIVE = 'active';
    const INACTIVE = 'inactive';
    const SUSPENDED = 'suspended';
    const BLOCKED = 'blocked';
}
