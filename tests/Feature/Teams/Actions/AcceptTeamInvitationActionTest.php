<?php

use App\Livewire\Actions\AcceptTeamInvitation;
use App\Models\Team;
use App\Models\TeamInvitation;
use App\Models\User;
use Illuminate\Support\Facades\Config;
use Illuminate\Validation\ValidationException;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    Config::set('teams.enabled', true);
});

it('adds the user to the team with a valid token', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->create();
    $invitation = TeamInvitation::query()->create([
        'team_id' => $team->id,
        'email' => $user->email,
        'role' => 'member',
        'expires_at' => now()->addDays(7),
    ]);

    app(AcceptTeamInvitation::class)($invitation, $invitation->token);

    expect($team->fresh()->hasMember($user))->toBeTrue();
    expect($team->fresh()->getRoleForUser($user))->toBe('member');
    expect($user->fresh()->current_team_id)->toBe($team->id);
    expect(TeamInvitation::query()->find($invitation->id))->toBeNull();
});

it('does not overwrite the current team of the user', function (): void {
    $user = User::factory()->create();
    $existingTeam = Team::factory()->create();
    $user->teams()->attach($existingTeam->id, ['role' => 'member']);
    $user->update(['current_team_id' => $existingTeam->id]);

    $team = Team::factory()->create();
    $invitation = TeamInvitation::query()->create([
        'team_id' => $team->id,
        'email' => $user->email,
        'role' => 'admin',
        'expires_at' => now()->addDays(7),
    ]);

    app(AcceptTeamInvitation::class)($invitation, $invitation->token);

    expect($team->fresh()->hasMember($user->fresh()))->toBeTrue();
    expect($user->fresh()->current_team_id)->toBe($existingTeam->id);
});

it('rejects an invalid token', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->create();
    $invitation = TeamInvitation::query()->create([
        'team_id' => $team->id,
        'email' => $user->email,
        'role' => 'member',
        'expires_at' => now()->addDays(7),
    ]);

    expect(fn () => app(AcceptTeamInvitation::class)($invitation, 'invalid-token'))
        ->toThrow(ValidationException::class);

    expect($team->fresh()->hasMember($user))->toBeFalse();
    expect(TeamInvitation::query()->find($invitation->id))->not->toBeNull();
});

it('rejects an expired invitation', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->create();
    $invitation = TeamInvitation::query()->create([
        'team_id' => $team->id,
        'email' => $user->email,
        'role' => 'member',
        'expires_at' => now()->subDay(),
    ]);

    expect(fn () => app(AcceptTeamInvitation::class)($invitation, $invitation->token))
        ->toThrow(ValidationException::class);

    expect($team->fresh()->hasMember($user))->toBeFalse();
});

it('rejects an invitation when no user exists for the email', function (): void {
    $team = Team::factory()->create();
    $invitation = TeamInvitation::query()->create([
        'team_id' => $team->id,
        'email' => 'nobody@example.com',
        'role' => 'member',
        'expires_at' => now()->addDays(7),
    ]);

    expect(fn () => app(AcceptTeamInvitation::class)($invitation, $invitation->token))
        ->toThrow(ValidationException::class);
});

it('deletes the invitation when the user is already a member', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->create();
    $team->users()->attach($user->id, ['role' => 'member']);

    $invitation = TeamInvitation::query()->create([
        'team_id' => $team->id,
        'email' => $user->email,
        'role' => 'member',
        'expires_at' => now()->addDays(7),
    ]);

    expect(fn () => app(AcceptTeamInvitation::class)($invitation, $invitation->token))
        ->toThrow(ValidationException::class);

    expect(TeamInvitation::query()->find($invitation->id))->toBeNull();
});

it('accepts an invitation through the route and redirects to the teams index', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->create();
    $invitation = TeamInvitation::query()->create([
        'team_id' => $team->id,
        'email' => $user->email,
        'role' => 'member',
        'expires_at' => now()->addDays(7),
    ]);

    $this->actingAs($user)
        ->get(route('teams.invitations.accept', ['invitation' => $invitation->id, 'token' => $invitation->token]))
        ->assertRedirect(route('teams.index'))
        ->assertSessionHas('success');

    expect($team->fresh()->hasMember($user))->toBeTrue();
});
