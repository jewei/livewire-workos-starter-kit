<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Support\Facades\Auth;

if (! function_exists('user')) {
    /**
     * Get the current authenticated user.
     */
    function user(): ?User
    {
        return once(fn () => Auth::user());
    }
}
