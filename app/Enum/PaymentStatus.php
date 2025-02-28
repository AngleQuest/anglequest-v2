<?php

namespace App\Enum;

enum PaymentStatus: string
{
    const PAID = 'paid';
    const PENDING = 'pending';
    const DECLINED = 'declined';
    const UNPAID = 'unpaid';
    const ACTIVE = 'active';
    const EXPIRED = 'expired';
}
