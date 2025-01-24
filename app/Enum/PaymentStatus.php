<?php

namespace App\Enum;

enum PaymentStatus: string
{
    const PAID = 'paid';
    const PENDING = 'pending';
    const UNPAID = 'unpaid';
}
