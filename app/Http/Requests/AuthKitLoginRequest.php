<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\User;
use App\WorkOS;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use WorkOS\UserManagement;

final class AuthKitLoginRequest extends FormRequest
{
    /**
     * Redirect the user to WorkOS for authentication.
     */
    public function redirect(): RedirectResponse
    {
        WorkOS::configure();

        $url = (new UserManagement)->getAuthorizationUrl(
            config('services.workos.redirect_url'),
            $state = [
                'state' => Str::random(20),
                'previous_url' => base64_encode(URL::previous()),
            ],
            'authkit',
        );

        $this->session()->put('referred_code', $this->query('ref'));
        $this->session()->put('referred_by', User::where('referral_code', $this->query('ref'))->first()?->id);
        $this->session()->put('state', json_encode($state));

        return redirect($url);
    }
}
