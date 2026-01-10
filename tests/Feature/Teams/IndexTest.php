<?php

use App\Livewire\Teams\Index;
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
        ->get(route('teams.index'))
        ->assertNotFound();
});

it('shows teams index for authenticated users', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->create();

    $user->teams()->attach($team->id, ['role' => 'admin']);

    $this->actingAs($user)
        ->get(route('teams.index'))
        ->assertOk();
});

it('displays all teams user belongs to', function (): void {
    $user = User::factory()->create();
    $team1 = Team::factory()->create(['name' => 'Team 1']);
    $team2 = Team::factory()->create(['name' => 'Team 2']);

    $user->teams()->attach($team1->id, ['role' => 'admin']);
    $user->teams()->attach($team2->id, ['role' => 'member']);

    Livewire::actingAs($user)
        ->test(Index::class)
        ->assertSee('Team 1')
        ->assertSee('Team 2');
});

it('can switch teams', function (): void {
    $user = User::factory()->create();
    $team1 = Team::factory()->create();
    $team2 = Team::factory()->create();

    $user->teams()->attach($team1->id, ['role' => 'admin']);
    $user->teams()->attach($team2->id, ['role' => 'admin']);

    expect($user->fresh()->current_team_id)->toBeNull();

    Livewire::actingAs($user)
        ->test(Index::class)
        ->call('switchTeam', $team1)
        ->assertDispatched('team-switched');

    expect($user->fresh()->current_team_id)->toBe($team1->id);
});

it('prevents switching to team user is not a member of', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->create();

    Livewire::actingAs($user)
        ->test(Index::class)
        ->call('switchTeam', $team)
        ->assertHasErrors();
});
