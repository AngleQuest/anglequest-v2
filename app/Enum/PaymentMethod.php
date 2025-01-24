<?php

namespace App\Enum;

enum PaymentMethod: string
{
    const TRANSFER = 'transfer';
    const PAYSTACK = 'paystack';
    const STRIPE = 'stripe';
    const FLUTTERWAVE = 'flutterwave';
}
