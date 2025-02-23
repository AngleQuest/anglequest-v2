<?php

namespace App\Enum;

enum AppointmentStatus: string
{
    const ACTIVE = 'active';
    const PENDING = 'pending';
    const COMPLETED = 'completed';
    const DECLINED = 'declined';
}
