<?php

use App\Livewire\Actions\RemoveUserFromTeam;
use App\Models\Team;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Config;
use Illuminate\Validation\ValidationException;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    Config::set('teams.enabled', true);
});

it('allows a team admin to remove a member', function (): void {
    $admin = User::factory()->create();
    $member = User::factory()->create();
    $team = Team::factory()->create();
    $team->users()->attach($admin->id, ['role' => 'admin']);
    $team->users()->attach($member->id, ['role' => 'member']);

    $this->actingAs($admin);

    app(RemoveUserFromTeam::class)($team, $member);

    expect($team->fresh()->hasMember($member))->toBeFalse();
});

it('prevents a non-admin from removing members', function (): void {
    $member = User::factory()->create();
    $otherMember = User::factory()->create();
    $team = Team::factory()->create();
    $team->users()->attach($member->id, ['role' => 'member']);
    $team->users()->attach($otherMember->id, ['role' => 'member']);

    $this->actingAs($member);

    expect(fn () => app(RemoveUserFromTeam::class)($team, $otherMember))
        ->toThrow(AuthorizationException::class);

    expect($team->fresh()->hasMember($otherMember))->toBeTrue();
});

it('prevents an admin from removing themselves', function (): void {
    $admin = User::factory()->create();
    $team = Team::factory()->create();
    $team->users()->attach($admin->id, ['role' => 'admin']);

    $this->actingAs($admin);

    expect(fn () => app(RemoveUserFromTeam::class)($team, $admin))
        ->toThrow(ValidationException::class);

    expect($team->fresh()->hasMember($admin))->toBeTrue();
});

it('rejects removing a user who is not a member', function (): void {
    $admin = User::factory()->create();
    $nonMember = User::factory()->create();
    $team = Team::factory()->create();
    $team->users()->attach($admin->id, ['role' => 'admin']);

    $this->actingAs($admin);

    expect(fn () => app(RemoveUserFromTeam::class)($team, $nonMember))
        ->toThrow(ValidationException::class);
});

it('allows removing an admin when another admin remains', function (): void {
    $admin = User::factory()->create();
    $otherAdmin = User::factory()->create();
    $team = Team::factory()->create();
    $team->users()->attach($admin->id, ['role' => 'admin']);
    $team->users()->attach($otherAdmin->id, ['role' => 'admin']);

    $this->actingAs($admin);

    app(RemoveUserFromTeam::class)($team, $otherAdmin);

    expect($team->fresh()->hasMember($otherAdmin))->toBeFalse();
    expect($team->fresh()->isAdmin($admin))->toBeTrue();
});

it('reassigns the current team of the removed user', function (): void {
    $admin = User::factory()->create();
    $member = User::factory()->create();
    $team = Team::factory()->create();
    $otherTeam = Team::factory()->create();
    $team->users()->attach($admin->id, ['role' => 'admin']);
    $team->users()->attach($member->id, ['role' => 'member']);
    $otherTeam->users()->attach($member->id, ['role' => 'member']);
    $member->update(['current_team_id' => $team->id]);

    $this->actingAs($admin);

    app(RemoveUserFromTeam::class)($team, $member);

    expect($member->fresh()->current_team_id)->toBe($otherTeam->id);
});

it('clears the current team when the removed user has no other teams', function (): void {
    $admin = User::factory()->create();
    $member = User::factory()->create();
    $team = Team::factory()->create();
    $team->users()->attach($admin->id, ['role' => 'admin']);
    $team->users()->attach($member->id, ['role' => 'member']);
    $member->update(['current_team_id' => $team->id]);

    $this->actingAs($admin);

    app(RemoveUserFromTeam::class)($team, $member);

    expect($member->fresh()->current_team_id)->toBeNull();
});
