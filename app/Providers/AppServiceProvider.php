<?php

declare(strict_types=1);

namespace App\Providers;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

final class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Prevent accidental data corruption.
        DB::prohibitDestructiveCommands($this->app->environment('production'));

        // Strict Model.
        Model::shouldBeStrict(! $this->app->environment('production'));
        Model::unguard();

        // Always use immutable dates.
        Date::use(CarbonImmutable::class);

        // Loosen password rules in non-production environments.
        Password::defaults(
            fn () => $this->app->environment('production')
                ? Password::min(8)->uncompromised()
                : null
        );
    }
}
