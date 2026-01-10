<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

it('can redirect to provider', function (string $provider) {
    Socialite::shouldReceive('driver')
        ->with($provider)
        ->andReturn(Mockery::mock('Laravel\Socialite\Contracts\Provider')
            ->shouldReceive('redirect')
            ->andReturn(redirect('https://example.com/auth/'.$provider))
            ->getMock()
        );

    $response = $this->get(route('social.redirect', $provider));

    $response->assertRedirect('https://example.com/auth/'.$provider);
})->with(['google', 'facebook', 'twitter']);

it('can authenticate with provider', function (string $provider) {
    $socialUser = Mockery::mock('Laravel\Socialite\Two\User');
    $socialUser->shouldReceive('getEmail')->andReturn('test@example.com');
    $socialUser->shouldReceive('getName')->andReturn('Test User');
    $socialUser->shouldReceive('getId')->andReturn('12345');
    $socialUser->token = 'fake-token';

    Socialite::shouldReceive('driver')
        ->with($provider)
        ->andReturn(Mockery::mock('Laravel\Socialite\Contracts\Provider')
            ->shouldReceive('user')
            ->andReturn($socialUser)
            ->getMock()
        );

    $response = $this->get(route('social.callback', $provider));

    $response->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticated();

    $user = User::where('email', 'test@example.com')->first();
    expect($user->provider_name)->toBe($provider)
        ->and($user->provider_id)->toBe('12345')
        ->and($user->name)->toBe('Test User')
        ->and($user->password)->not->toBeNull();
})->with(['google', 'facebook', 'twitter']);

it('does not overwrite password for existing user when logging in via social', function (string $provider) {
    $existingPassword = Hash::make('existing-password');
    $user = User::factory()->create([
        'email' => 'existing@example.com',
        'password' => $existingPassword,
    ]);

    $socialUser = Mockery::mock('Laravel\Socialite\Two\User');
    $socialUser->shouldReceive('getEmail')->andReturn('existing@example.com');
    $socialUser->shouldReceive('getName')->andReturn('Updated Name');
    $socialUser->shouldReceive('getId')->andReturn('67890');
    $socialUser->token = 'new-fake-token';

    Socialite::shouldReceive('driver')
        ->with($provider)
        ->andReturn(Mockery::mock('Laravel\Socialite\Contracts\Provider')
            ->shouldReceive('user')
            ->andReturn($socialUser)
            ->getMock()
        );

    $response = $this->get(route('social.callback', $provider));

    $response->assertRedirect(route('dashboard', absolute: false));

    $user->refresh();
    expect($user->password)->toBe($existingPassword)
        ->and($user->provider_name)->toBe($provider)
        ->and($user->provider_id)->toBe('67890')
        ->and($user->name)->toBe('Updated Name');
})->with(['google', 'facebook', 'twitter']);
