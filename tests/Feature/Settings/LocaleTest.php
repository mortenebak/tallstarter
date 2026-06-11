<?php

use App\Models\User;
use Livewire\Volt\Volt;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('locale page is displayed', function (): void {
    $this->actingAs(User::factory()->create(['locale' => 'en']));

    $this->get(route('settings.locale'))->assertOk();
});

test('guests are redirected to the login page', function (): void {
    $this->get(route('settings.locale'))->assertRedirect(route('login'));
});

test('locale can be updated and persists on the user', function (): void {
    $user = User::factory()->create(['locale' => 'en']);

    $this->actingAs($user);

    $response = Volt::test('settings.locale')
        ->assertSet('locale', 'en')
        ->set('locale', 'da')
        ->call('updateLocale');

    $response
        ->assertHasNoErrors()
        ->assertDispatched('locale-updated');

    expect($user->refresh()->locale)->toBe('da');
});

test('updated locale is applied on subsequent requests', function (): void {
    $user = User::factory()->create(['locale' => 'en']);

    $this->actingAs($user);

    Volt::test('settings.locale')
        ->set('locale', 'da')
        ->call('updateLocale')
        ->assertHasNoErrors();

    $this->get(route('settings.locale'))->assertOk();

    expect(app()->getLocale())->toBe('da');
});

test('locale must be a supported locale', function (string $invalidLocale) {
    $user = User::factory()->create(['locale' => 'en']);

    $this->actingAs($user);

    Volt::test('settings.locale')
        ->set('locale', $invalidLocale)
        ->call('updateLocale')
        ->assertHasErrors(['locale']);

    expect($user->refresh()->locale)->toBe('en');
})->with([
    'unsupported locale' => 'fr',
    'empty locale' => '',
]);

test('session locale is applied for guests', function (): void {
    $this->withSession(['locale' => 'da'])->get(route('home'))->assertOk();

    expect(app()->getLocale())->toBe('da');
});

test('user locale takes precedence over session locale', function (): void {
    $user = User::factory()->create(['locale' => 'da']);

    $this->actingAs($user)
        ->withSession(['locale' => 'en'])
        ->get(route('settings.locale'))
        ->assertOk();

    expect(app()->getLocale())->toBe('da');
});
