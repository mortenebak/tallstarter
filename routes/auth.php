<?php

use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function (): void {
    Route::get('login', \App\Livewire\Auth\Login::class)
        ->name('login');

    Route::get('register', \App\Livewire\Auth\Register::class)
        ->name('register');

    Route::get('auth/{provider}/redirect', [App\Http\Controllers\Auth\SocialLoginController::class, 'redirect'])->name('social.redirect');

    Route::get('auth/{provider}/callback', [App\Http\Controllers\Auth\SocialLoginController::class, 'callback'])->name('social.callback');

    Route::get('forgot-password', \App\Livewire\Auth\ForgotPassword::class)
        ->name('password.request');

    Route::get('reset-password/{token}', \App\Livewire\Auth\ResetPassword::class)
        ->name('password.reset');

    Route::get('two-factor-challenge', \App\Livewire\Auth\TwoFactorChallenge::class)
        ->name('two-factor.challenge');
});

Route::middleware('auth')->group(function (): void {
    Route::get('verify-email', \App\Livewire\Auth\VerifyEmail::class)
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::get('confirm-password', \App\Livewire\Auth\ConfirmPassword::class)
        ->name('password.confirm');
});

Route::post('logout', App\Livewire\Actions\Logout::class)
    ->name('logout');
