<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\WorkOS;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use WorkOS\Exception\WorkOSException;

final class ValidateSessionWithWorkOS
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (app()->runningUnitTests()) {
            return $next($request);
        }

        WorkOS::configure();

        if (! $request->session()->get('workos_access_token') ||
            ! $request->session()->get('workos_refresh_token')) {
            return $this->logout($request);
        }

        try {
            [$accessToken, $refreshToken] = WorkOS::ensureAccessTokenIsValid(
                $request->session()->get('workos_access_token'),
                $request->session()->get('workos_refresh_token'),
            );

            $request->session()->put('workos_access_token', $accessToken);
            $request->session()->put('workos_refresh_token', $refreshToken);
        } catch (WorkOSException $e) {
            report($e);

            return $this->logout($request);
        }

        return $next($request);
    }

    /**
     * Log the user out of the application.
     */
    private function logout(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
