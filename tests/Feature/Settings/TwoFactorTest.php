<?php

use App\Livewire\Settings\TwoFactor;
use App\Models\User;
use Livewire\Livewire;
use PragmaRX\Google2FA\Google2FA;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('two factor settings page can be rendered', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/settings/two-factor');

    $response->assertStatus(200);
});

test('two factor authentication can be enabled', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(TwoFactor::class)
        ->call('enableTwoFactorAuthentication')
        ->assertSet('showingQrCode', true);

    $this->assertNotNull($user->fresh()->two_factor_secret);
    $this->assertNull($user->fresh()->two_factor_confirmed_at);
});

test('two factor authentication can be confirmed with valid code', function (): void {
    $user = User::factory()->create();
    $google2fa = new Google2FA;
    $secret = $google2fa->generateSecretKey();

    $user->two_factor_secret = encrypt($secret);
    $user->save();

    $validCode = $google2fa->getCurrentOtp($secret);

    Livewire::actingAs($user)
        ->test(TwoFactor::class)
        ->set('showingQrCode', true)
        ->set('code', $validCode)
        ->call('confirmTwoFactorAuthentication')
        ->assertHasNoErrors();

    $this->assertNotNull($user->fresh()->two_factor_confirmed_at);
    $this->assertNotNull($user->fresh()->two_factor_recovery_codes);
});

test('two factor authentication cannot be confirmed with invalid code', function (): void {
    $user = User::factory()->create();
    $google2fa = new Google2FA;
    $secret = $google2fa->generateSecretKey();

    $user->two_factor_secret = encrypt($secret);
    $user->save();

    Livewire::actingAs($user)
        ->test(TwoFactor::class)
        ->set('showingQrCode', true)
        ->set('code', '000000')
        ->call('confirmTwoFactorAuthentication')
        ->assertHasErrors(['code']);

    $this->assertNull($user->fresh()->two_factor_confirmed_at);
});

test('recovery codes can be regenerated', function (): void {
    $user = User::factory()->create();
    $google2fa = new Google2FA;
    $secret = $google2fa->generateSecretKey();

    $user->two_factor_secret = encrypt($secret);
    $user->two_factor_confirmed_at = now();
    $user->two_factor_recovery_codes = encrypt(json_encode(['OLD-CODE-1', 'OLD-CODE-2']));
    $user->save();

    $oldCodes = $user->recoveryCodes();

    Livewire::actingAs($user)
        ->test(TwoFactor::class)
        ->call('regenerateRecoveryCodes')
        ->assertSet('showingRecoveryCodes', true);

    $newCodes = $user->fresh()->recoveryCodes();

    expect($newCodes)->not->toBe($oldCodes);
    expect($newCodes)->toHaveCount(8);
});

test('two factor authentication can be disabled', function (): void {
    $user = User::factory()->create();
    $google2fa = new Google2FA;
    $secret = $google2fa->generateSecretKey();

    $user->two_factor_secret = encrypt($secret);
    $user->two_factor_confirmed_at = now();
    $user->two_factor_recovery_codes = encrypt(json_encode(['CODE-1', 'CODE-2']));
    $user->save();

    Livewire::actingAs($user)
        ->test(TwoFactor::class)
        ->call('disableTwoFactorAuthentication');

    $user = $user->fresh();

    $this->assertNull($user->two_factor_secret);
    $this->assertNull($user->two_factor_confirmed_at);
    $this->assertNull($user->two_factor_recovery_codes);
});

test('recovery codes can be viewed', function (): void {
    $user = User::factory()->create();
    $recoveryCodes = ['CODE-1', 'CODE-2', 'CODE-3'];

    $user->two_factor_secret = encrypt('secret');
    $user->two_factor_confirmed_at = now();
    $user->two_factor_recovery_codes = encrypt(json_encode($recoveryCodes));
    $user->save();

    Livewire::actingAs($user)
        ->test(TwoFactor::class)
        ->call('showRecoveryCodes')
        ->assertSet('showingRecoveryCodes', true)
        ->assertSet('recoveryCodes', $recoveryCodes);
});
