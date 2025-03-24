<?php

declare(strict_types=1);

namespace App\Livewire\Settings;

use App\Http\Requests\AuthKitAccountDeletionRequest;
use App\Rules\CurrentEmail;
use Illuminate\Http\RedirectResponse;
use Livewire\Component;

final class DeleteUserForm extends Component
{
    public string $email = '';

    /**
     * Delete the currently authenticated user.
     */
    public function deleteUser(AuthKitAccountDeletionRequest $request): void
    {
        $this->validate([
            'email' => ['required', 'email', new CurrentEmail],
        ]);

        $redirect = $request->delete();

        if ($redirect instanceof RedirectResponse) {
            $this->redirect($redirect->getTargetUrl(), navigate: true);
        }
    }
}
