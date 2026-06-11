<?php

use App\Models\User;
use Livewire\Volt\Volt;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('account is deleted and user is logged out when the correct password is provided', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = Volt::test('settings.delete-user-form')
        ->set('password', 'password')
        ->call('deleteUser');

    $response
        ->assertHasNoErrors()
        ->assertRedirect('/');

    expect($user->fresh())->toBeNull();
    expect(auth()->check())->toBeFalse();
});

test('account is not deleted when the wrong password is provided', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = Volt::test('settings.delete-user-form')
        ->set('password', 'wrong-password')
        ->call('deleteUser');

    $response->assertHasErrors(['password']);

    expect($user->fresh())->not->toBeNull();
    expect(auth()->check())->toBeTrue();
});

test('password is required to delete the account', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user);

    Volt::test('settings.delete-user-form')
        ->set('password', '')
        ->call('deleteUser')
        ->assertHasErrors(['password' => 'required']);

    expect($user->fresh())->not->toBeNull();
});
