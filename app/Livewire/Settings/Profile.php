<?php

declare(strict_types=1);

namespace App\Livewire\Settings;

use App\WorkOS;
use DateTimeZone;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Throwable;
use WorkOS\UserManagement;

final class Profile extends Component
{
    public string $email = '';
    public string $first_name = '';
    public string $last_name = '';
    public string $timezone = '';
    public array $timezones = [];

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->email = user()->email;
        $this->first_name = user()->first_name ?? '';
        $this->last_name = user()->last_name ?? '';
        $this->timezone = user()->timezone ?? '';
        $this->timezones = collect(DateTimeZone::listIdentifiers())->mapWithKeys(fn (string $timezone) => [$timezone => $timezone])->toArray();
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = user();

        $validated = $this->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['sometimes', 'string', 'max:255'],
            'timezone' => ['required', 'string', 'timezone'],
        ]);

        $user->update([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'] ?? null,
            'timezone' => $validated['timezone'],
        ]);

        if (isset($user->workos_id) && ! app()->runningUnitTests()) {
            WorkOS::configure();

            (new UserManagement)->updateUser(
                userId: $user->workos_id,
                firstName: $user->first_name,
                lastName: $user->last_name,
                metadata: [
                    'timezone' => $user->timezone,
                ],
            );
        }

        $this->dispatch('profile-updated', name: $user->name);
    }

    /**
     * Send an email verification notification to the current user.
     */
    public function resendVerificationNotification(): void
    {
        $user = user();

        try {
            if (! $user->hasVerifiedEmail()) {
                $user->sendEmailVerificationNotification();
                session()->flash('status', 'verification-link-sent');
            }
        } catch (\WorkOS\Exception\BadRequestException $exception) {
            if ($exception->responseCode === 'email_already_verified') {
                $user->markEmailAsVerified();

                session()->flash('custom_status', $exception->responseMessage);
            }
        } catch (Throwable $throwable) {
            Log::warning('Failed to send verification notification', ['message' => $throwable->getMessage()]);

            session()->flash('error', 'verification-link-failed');
        }
    }
}
