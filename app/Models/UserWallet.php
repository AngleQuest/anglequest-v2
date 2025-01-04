<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserWallet extends Model
{
    protected $fillable = [
        'user_id',
        'master_wallet',
        'transaction_wallet',
    ];

    public function user(): BelongsTo
    {
        return $this->BelongsTo(User::class,'user_id')->withDefault([
            'name' => 'guest',
        ]);
    }
}
