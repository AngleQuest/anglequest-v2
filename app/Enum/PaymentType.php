<?php

namespace App\Enum;

enum PaymentType: string
{
    const SUBSCRIPTION = 'subscription';
    const RENEWAL = 'renewal';
    const UPGRADE = 'upgrade';
}
