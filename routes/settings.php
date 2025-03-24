<?php

declare(strict_types=1);

use App\Http\Middleware\ValidateSessionWithWorkOS;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Profile;
use Illuminate\Support\Facades\Route;

Route::middleware([
    'auth',
    ValidateSessionWithWorkOS::class,
])->group(function (): void {
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');
});
