<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\WorkOS;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Livewire\Features\SupportRedirects\Redirector;
use WorkOS\UserManagement;

final class AuthKitAccountDeletionRequest extends FormRequest
{
    /**
     * Redirect the user to WorkOS for authentication.
     */
    public function delete(): RedirectResponse|Redirector
    {
        $user = $this->user();

        if (isset($user->workos_id) && ! app()->runningUnitTests()) {
            WorkOS::configure();

            (new UserManagement)->deleteUser(
                $user->workos_id
            );
        }

        Auth::guard('web')->logout();

        $user->delete();

        if ($this->hasSession()) {
            $this->session()->invalidate();
            $this->session()->regenerateToken();
        }

        return redirect('/');
    }
}
