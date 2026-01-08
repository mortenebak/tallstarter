<?php

use App\Models\User;
use PragmaRX\Google2FA\Google2FA;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('has two factor enabled returns true when 2fa is active', function (): void {
    $google2fa = new Google2FA;
    $secret = $google2fa->generateSecretKey();

    $user = User::factory()->create([
        'two_factor_secret' => encrypt($secret),
        'two_factor_confirmed_at' => now(),
    ]);

    expect($user->hasTwoFactorEnabled())->toBeTrue();
});

test('has two factor enabled returns false when secret is null', function (): void {
    $user = User::factory()->create([
        'two_factor_secret' => null,
        'two_factor_confirmed_at' => now(),
    ]);

    expect($user->hasTwoFactorEnabled())->toBeFalse();
});

test('has two factor enabled returns false when not confirmed', function (): void {
    $google2fa = new Google2FA;
    $secret = $google2fa->generateSecretKey();

    $user = User::factory()->create([
        'two_factor_secret' => encrypt($secret),
        'two_factor_confirmed_at' => null,
    ]);

    expect($user->hasTwoFactorEnabled())->toBeFalse();
});

test('recovery codes can be retrieved', function (): void {
    $codes = ['CODE-1', 'CODE-2', 'CODE-3'];

    $user = User::factory()->create([
        'two_factor_recovery_codes' => encrypt(json_encode($codes)),
    ]);

    expect($user->recoveryCodes())->toBe($codes);
});

test('recovery codes returns empty array when null', function (): void {
    $user = User::factory()->create([
        'two_factor_recovery_codes' => null,
    ]);

    expect($user->recoveryCodes())->toBe([]);
});

test('recovery code can be replaced', function (): void {
    $codes = ['CODE-1', 'CODE-2', 'CODE-3'];

    $user = User::factory()->create([
        'two_factor_recovery_codes' => encrypt(json_encode($codes)),
    ]);

    $user->replaceRecoveryCode('CODE-2');

    $remaining = $user->fresh()->recoveryCodes();

    expect($remaining)->toHaveCount(2);
    expect($remaining)->not->toContain('CODE-2');
    expect($remaining)->toContain('CODE-1');
    expect($remaining)->toContain('CODE-3');
});
