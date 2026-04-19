<?php

use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::middleware('guest')->group(function () {

    Volt::route('forgot-password', 'auth.forgot-password')
        ->name('forgotPassword');

    Volt::route('reset-password/{token}', 'auth.reset-password')
        ->name('resetPassword');
});

Route::middleware('auth')->group(function () {
    Volt::route('verify-email', 'auth.verify-email')
        ->name('verifyEmailNotice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verifyEmailAction');

    Volt::route('confirm-password', 'auth.confirm-password')
        ->name('confirmPassword');
});

Route::post('logout', App\Livewire\Actions\Logout::class)
    ->name('logout');