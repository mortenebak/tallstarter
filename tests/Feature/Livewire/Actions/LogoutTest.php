<?php

use App\Livewire\Actions\Logout;
use App\Models\User;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

it('logs the user out and redirects to the home page', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user);

    expect(auth()->check())->toBeTrue();

    $response = app(Logout::class)();

    expect(auth()->check())->toBeFalse();
    expect($response->getTargetUrl())->toBe(url('/'));
});

it('invalidates the session on logout', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user)->withSession(['some-key' => 'some-value']);

    app(Logout::class)();

    expect(session()->has('some-key'))->toBeFalse();
});
