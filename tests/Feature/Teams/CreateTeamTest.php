<?php

use App\Livewire\Teams\CreateTeam;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\Config;
use Livewire\Livewire;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    Config::set('teams.enabled', true);
});

it('returns 404 when teams feature is disabled', function (): void {
    Config::set('teams.enabled', false);

    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('teams.create'))
        ->assertNotFound();
});

it('can create a team', function (): void {
    $user = User::factory()->create();

    expect(Team::count())->toBe(0);

    Livewire::actingAs($user)
        ->test(CreateTeam::class)
        ->set('name', 'My Team')
        ->set('description', 'Team Description')
        ->call('create')
        ->assertRedirect(route('teams.manage', Team::first()));

    expect(Team::count())->toBe(1);
    expect(Team::first()->name)->toBe('My Team');
    expect(Team::first()->description)->toBe('Team Description');
    expect($user->fresh()->teams()->where('team_id', Team::first()->id)->first()->pivot->role)->toBe('admin');
    expect($user->fresh()->current_team_id)->toBe(Team::first()->id);
});

it('requires name to create team', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(CreateTeam::class)
        ->set('name', '')
        ->call('create')
        ->assertHasErrors(['name']);
});

it('sets current team if user does not have one', function (): void {
    $user = User::factory()->create();
    $existingTeam = Team::factory()->create();
    $user->teams()->attach($existingTeam->id, ['role' => 'admin']);
    $user->update(['current_team_id' => $existingTeam->id]);

    Livewire::actingAs($user)
        ->test(CreateTeam::class)
        ->set('name', 'New Team')
        ->call('create');

    // Should not change current team
    expect($user->fresh()->current_team_id)->toBe($existingTeam->id);
});
