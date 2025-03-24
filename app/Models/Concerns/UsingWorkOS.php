<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use App\WorkOS;
use Illuminate\Database\Eloquent\Casts\Attribute;
use WorkOS\UserManagement;

trait UsingWorkOS
{
    /**
     * Send the email verification notification.
     */
    public function sendEmailVerificationNotification(): void
    {
        WorkOS::configure();

        (new UserManagement)->sendVerificationEmail($this->workos_id);
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return str($this->first_name.' '.$this->last_name)
            ->explode(' ')
            ->map(fn (string $name) => str($name)->substr(0, 1))
            ->implode('');
    }

    /**
     * Get the column name for the "remember me" token.
     */
    public function getRememberTokenName(): string
    {
        return '';
    }

    /**
     * Get the user's name.
     */
    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => $attributes['first_name'].' '.$attributes['last_name'],
        );
    }
}
