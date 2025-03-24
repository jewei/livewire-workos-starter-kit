<?php

declare(strict_types=1);

use App\Http\Requests\AuthKitAuthenticationRequest;
use App\Http\Requests\AuthKitLoginRequest;
use App\Http\Requests\AuthKitLogoutRequest;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function (): void {
    Route::get('login', fn (AuthKitLoginRequest $request) => $request->redirect())->name('login');
    Route::get('authenticate', fn (AuthKitAuthenticationRequest $request) => $request->redirect());
});

Route::middleware('auth')->group(function (): void {
    Route::post('logout', fn (AuthKitLogoutRequest $request) => $request->redirect())->name('logout');
});
