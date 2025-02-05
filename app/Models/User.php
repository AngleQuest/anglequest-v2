<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'company_id',
        'email',
        'email_code',
        'email_code_expire_time',
        'email_verified_at',
        'phone',
        'password',
        'status',
        'role'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'created_at',
        'updated_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function profile(): BelongsTo
    {
        return $this->BelongsTo(IndividualProfile::class, 'user_id', 'id');
    }

    public function expert(): BelongsTo
    {
        return $this->BelongsTo(Expert::class);
    }
    public function company(): BelongsTo
    {
        return $this->BelongsTo(Company::class, 'company_id');
    }
    public function paymentHistories(): HasMany
    {
        return $this->HasMany(PaymentHistory::class);
    }
    public function sla(): BelongsTo
    {
        return $this->BelongsTo(Sla::class);
    }
}
