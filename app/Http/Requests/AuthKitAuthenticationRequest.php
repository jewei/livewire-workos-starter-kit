<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\User;
use App\WorkOS;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Throwable;
use WorkOS\Resource\AuthenticationResponse;
use WorkOS\UserManagement;

final class AuthKitAuthenticationRequest extends FormRequest
{
    /**
     * Redirect the user to the previous URL or a default URL if no previous URL is available.
     */
    public function redirect(string $default = '/'): RedirectResponse
    {
        if ($resource = $this->authenticate()) {
            $this->createOrUpdateUser($resource);
        }

        try {
            $to = rtrim(base64_decode($this->sessionState()['previous_url'], true));
        } catch (Throwable) {
            $to = $default;
        }

        return redirect($to);
    }

    /**
     * Authenticate the user with WorkOS.
     */
    protected function authenticate(): ?AuthenticationResponse
    {
        WorkOS::configure();

        try {
            $this->ensureStateIsValid();
        } catch (Throwable $throwable) {
            Log::error('Failed to ensure state is valid', ['message' => $throwable->getMessage()]);

            abort(403);
        }

        try {
            $resource = (new UserManagement)->authenticateWithCode(
                config('services.workos.client_id'),
                $this->query('code'),
            );
        } catch (Throwable $throwable) {
            Log::error('Failed to authenticate with WorkOS', ['message' => $throwable->getMessage()]);

            return null;
        }

        return $resource;
    }

    /**
     * Create or update a user from the given WorkOS user.
     */
    protected function createOrUpdateUser(AuthenticationResponse $resource): User
    {
        // Find the user with the given WorkOS ID.
        $user = User::where('workos_id', $resource->user->id)->first();

        if (! $user instanceof User) {
            // Create a user from the given WorkOS user.
            $user = User::create([
                'first_name' => $resource->user->firstName,
                'last_name' => $resource->user->lastName,
                'email' => $resource->user->email,
                'email_verified_at' => $resource->user->emailVerified ? now() : null,
                'workos_id' => $resource->user->id,
                'avatar' => $resource->user->profilePictureUrl,
                'authentication_method' => $resource->authentication_method,
                'source' => 'WorkOS',
                'referred_code' => $this->session()->get('referred_code'),
                'referred_by' => $this->session()->get('referred_by'),
            ]);

            if (empty($user->first_name) && empty($user->last_name)) {
                $user->update([
                    'first_name' => str($resource->user->email)->before('@')->value(),
                ]);
            }

            event(new Registered($user));
        } else {
            // Update a user from the given WorkOS user.
            $user->update([
                'email' => $resource->user->email,
                'email_verified_at' => $user->email_verified_at ?? $resource->user->emailVerified ? now() : null,
                'avatar' => $resource->user->profilePictureUrl,
                'authentication_method' => $resource->authentication_method,
            ]);
        }

        Auth::guard('web')->login($user);

        $this->session()->put('workos_access_token', $resource->access_token);
        $this->session()->put('workos_refresh_token', $resource->refresh_token);
        $this->session()->regenerate();

        return $user;
    }

    /**
     * Ensure the request state is valid.
     */
    protected function ensureStateIsValid(): void
    {
        $state = json_decode($this->query('state'), true)['state'] ?? false;

        abort_unless($state === $this->sessionState()['state'], 403);

        $this->session()->forget('state');
    }

    /**
     * Get the session state.
     */
    protected function sessionState(): array
    {
        return json_decode($this->session()->get('state', ''), true) ?: [];
    }
}
