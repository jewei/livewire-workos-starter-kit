<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use App\Models\User;

trait HasReferralCode
{
    protected static function bootHasReferralCode(): void
    {
        static::creating(function (User $user): void {
            $generateReferralCode = (function () {
                $code = null;

                while ($code === null || self::where('referral_code', $code)->exists()) {
                    $code = str(str()->random(6))->upper()->value();
                }

                return $code;
            });

            $user->referral_code = $user->referral_code ?? $generateReferralCode();
        });
    }
}
