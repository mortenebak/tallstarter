<?php

use App\Livewire\Settings\Appearance;
use App\Models\User;
use Livewire\Livewire;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('appearance page is displayed', function (): void {
    $this->actingAs(User::factory()->create());

    $this->get(route('settings.appearance'))
        ->assertOk()
        ->assertSeeLivewire(Appearance::class);
});

test('guests are redirected to the login page', function (): void {
    $this->get(route('settings.appearance'))->assertRedirect(route('login'));
});

test('appearance component renders successfully', function (): void {
    Livewire::actingAs(User::factory()->create())
        ->test(Appearance::class)
        ->assertOk();
});
