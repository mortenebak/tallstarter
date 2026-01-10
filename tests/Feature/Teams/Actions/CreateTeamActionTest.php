<?php

use App\Livewire\Actions\CreateTeam;
use App\Models\Team;
use App\Models\User;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

it('creates a team and adds creator as admin', function (): void {
    $user = User::factory()->create();
    $this->actingAs($user);

    $team = app(CreateTeam::class)('My Team', 'Team Description');

    expect($team)->toBeInstanceOf(Team::class);
    expect($team->name)->toBe('My Team');
    expect($team->description)->toBe('Team Description');
    expect($user->fresh()->teams()->where('team_id', $team->id)->exists())->toBeTrue();
    expect($user->fresh()->teams()->where('team_id', $team->id)->first()->pivot->role)->toBe('admin');
    expect($user->fresh()->current_team_id)->toBe($team->id);
});

it('sets current team if user does not have one', function (): void {
    $user = User::factory()->create();
    $this->actingAs($user);
    $existingTeam = Team::factory()->create();
    $user->teams()->attach($existingTeam->id, ['role' => 'admin']);
    $user->update(['current_team_id' => $existingTeam->id]);

    $team = app(CreateTeam::class)('New Team');

    expect($user->fresh()->current_team_id)->toBe($existingTeam->id);
});
