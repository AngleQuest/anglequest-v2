<?php

namespace App\Enum;

enum UserStatus: string
{
    const ACTIVE = 'active';
    const SUSPENDED = 'suspended';
    const BLOCKED = 'blocked';
}
