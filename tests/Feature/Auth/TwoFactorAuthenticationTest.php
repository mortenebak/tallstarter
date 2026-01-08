<?php

use App\Livewire\Auth\Login;
use App\Livewire\Auth\TwoFactorChallenge;
use App\Models\User;
use Livewire\Livewire;
use PragmaRX\Google2FA\Google2FA;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('user with 2fa enabled is redirected to two factor challenge', function (): void {
    $google2fa = new Google2FA;
    $secret = $google2fa->generateSecretKey();

    $user = User::factory()->create([
        'two_factor_secret' => encrypt($secret),
        'two_factor_confirmed_at' => now(),
        'two_factor_recovery_codes' => encrypt(json_encode(['CODE-1', 'CODE-2'])),
    ]);

    Livewire::test(Login::class)
        ->set('email', $user->email)
        ->set('password', 'password')
        ->call('login')
        ->assertRedirect(route('two-factor.challenge'));

    $this->assertGuest();
    expect(session()->has('login.id'))->toBeTrue();
});

test('user without 2fa can login normally', function (): void {
    $user = User::factory()->create();

    Livewire::test(Login::class)
        ->set('email', $user->email)
        ->set('password', 'password')
        ->call('login')
        ->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticated();
});

test('two factor challenge can be passed with valid code', function (): void {
    $google2fa = new Google2FA;
    $secret = $google2fa->generateSecretKey();

    $user = User::factory()->create([
        'two_factor_secret' => encrypt($secret),
        'two_factor_confirmed_at' => now(),
        'two_factor_recovery_codes' => encrypt(json_encode(['CODE-1', 'CODE-2'])),
    ]);

    session()->put([
        'login.id' => $user->id,
        'login.remember' => false,
    ]);

    $validCode = $google2fa->getCurrentOtp($secret);

    Livewire::test(TwoFactorChallenge::class)
        ->set('code', $validCode)
        ->call('challenge')
        ->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticatedAs($user);
});

test('two factor challenge fails with invalid code', function (): void {
    $google2fa = new Google2FA;
    $secret = $google2fa->generateSecretKey();

    $user = User::factory()->create([
        'two_factor_secret' => encrypt($secret),
        'two_factor_confirmed_at' => now(),
        'two_factor_recovery_codes' => encrypt(json_encode(['CODE-1', 'CODE-2'])),
    ]);

    session()->put([
        'login.id' => $user->id,
        'login.remember' => false,
    ]);

    Livewire::test(TwoFactorChallenge::class)
        ->set('code', '000000')
        ->call('challenge')
        ->assertHasErrors(['code']);

    $this->assertGuest();
});

test('two factor challenge can be passed with recovery code', function (): void {
    $google2fa = new Google2FA;
    $secret = $google2fa->generateSecretKey();
    $recoveryCodes = ['RECOVERY-CODE-1', 'RECOVERY-CODE-2'];

    $user = User::factory()->create([
        'two_factor_secret' => encrypt($secret),
        'two_factor_confirmed_at' => now(),
        'two_factor_recovery_codes' => encrypt(json_encode($recoveryCodes)),
    ]);

    session()->put([
        'login.id' => $user->id,
        'login.remember' => false,
    ]);

    Livewire::test(TwoFactorChallenge::class)
        ->set('recovery', true)
        ->set('code', 'RECOVERY-CODE-1')
        ->call('challenge')
        ->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticatedAs($user);

    // Verify the recovery code was consumed
    $remainingCodes = $user->fresh()->recoveryCodes();
    expect($remainingCodes)->not->toContain('RECOVERY-CODE-1');
    expect($remainingCodes)->toContain('RECOVERY-CODE-2');
});

test('two factor challenge fails with invalid recovery code', function (): void {
    $google2fa = new Google2FA;
    $secret = $google2fa->generateSecretKey();

    $user = User::factory()->create([
        'two_factor_secret' => encrypt($secret),
        'two_factor_confirmed_at' => now(),
        'two_factor_recovery_codes' => encrypt(json_encode(['VALID-CODE'])),
    ]);

    session()->put([
        'login.id' => $user->id,
        'login.remember' => false,
    ]);

    Livewire::test(TwoFactorChallenge::class)
        ->set('recovery', true)
        ->set('code', 'INVALID-CODE')
        ->call('challenge')
        ->assertHasErrors(['code']);

    $this->assertGuest();
});

test('can toggle between code and recovery mode', function (): void {
    Livewire::test(TwoFactorChallenge::class)
        ->assertSet('recovery', false)
        ->call('toggleRecovery')
        ->assertSet('recovery', true)
        ->call('toggleRecovery')
        ->assertSet('recovery', false);
});
