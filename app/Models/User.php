<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Concerns\HasReferralCode;
use App\Models\Concerns\HasUuid;
use App\Models\Concerns\UsingWorkOS;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

final class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, HasReferralCode, HasUuid, Notifiable, UsingWorkOS;

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'id',
        'workos_id',
        'authentication_method',
        'referral_code',
        'referred_code',
        'referred_by',
        'source',
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
        ];
    }
}
