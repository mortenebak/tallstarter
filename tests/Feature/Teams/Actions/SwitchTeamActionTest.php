<?php

use App\Livewire\Actions\SwitchTeam;
use App\Models\Team;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Config;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    Config::set('teams.enabled', true);
});

it('switches the current team for a member', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->create();
    $otherTeam = Team::factory()->create();
    $user->teams()->attach($team->id, ['role' => 'admin']);
    $user->teams()->attach($otherTeam->id, ['role' => 'member']);
    $user->update(['current_team_id' => $team->id]);

    $this->actingAs($user);

    app(SwitchTeam::class)($otherTeam);

    expect($user->fresh()->current_team_id)->toBe($otherTeam->id);
});

it('prevents switching to a team the user does not belong to', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->create();
    $otherTeam = Team::factory()->create();
    $user->teams()->attach($team->id, ['role' => 'admin']);
    $user->update(['current_team_id' => $team->id]);

    $this->actingAs($user);

    expect(fn () => app(SwitchTeam::class)($otherTeam))
        ->toThrow(AuthorizationException::class);

    expect($user->fresh()->current_team_id)->toBe($team->id);
});
