<?php

use App\Livewire\Actions\ChangeTeamMemberRole;
use App\Models\Team;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Config;
use Illuminate\Validation\ValidationException;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    Config::set('teams.enabled', true);
});

it('allows a team admin to change a member role', function (): void {
    $admin = User::factory()->create();
    $member = User::factory()->create();
    $team = Team::factory()->create();
    $team->users()->attach($admin->id, ['role' => 'admin']);
    $team->users()->attach($member->id, ['role' => 'member']);

    $this->actingAs($admin);

    app(ChangeTeamMemberRole::class)($team, $member, 'admin');

    expect($team->fresh()->getRoleForUser($member))->toBe('admin');
});

it('prevents a non-admin from changing roles', function (): void {
    $member = User::factory()->create();
    $otherMember = User::factory()->create();
    $team = Team::factory()->create();
    $team->users()->attach($member->id, ['role' => 'member']);
    $team->users()->attach($otherMember->id, ['role' => 'member']);

    $this->actingAs($member);

    expect(fn () => app(ChangeTeamMemberRole::class)($team, $otherMember, 'admin'))
        ->toThrow(AuthorizationException::class);

    expect($team->fresh()->getRoleForUser($otherMember))->toBe('member');
});

it('rejects an invalid role', function (): void {
    $admin = User::factory()->create();
    $member = User::factory()->create();
    $team = Team::factory()->create();
    $team->users()->attach($admin->id, ['role' => 'admin']);
    $team->users()->attach($member->id, ['role' => 'member']);

    $this->actingAs($admin);

    expect(fn () => app(ChangeTeamMemberRole::class)($team, $member, 'owner'))
        ->toThrow(ValidationException::class);
});

it('rejects changing the role of a user who is not a member', function (): void {
    $admin = User::factory()->create();
    $nonMember = User::factory()->create();
    $team = Team::factory()->create();
    $team->users()->attach($admin->id, ['role' => 'admin']);

    $this->actingAs($admin);

    expect(fn () => app(ChangeTeamMemberRole::class)($team, $nonMember, 'admin'))
        ->toThrow(ValidationException::class);
});

it('prevents demoting the last admin', function (): void {
    $admin = User::factory()->create();
    $member = User::factory()->create();
    $team = Team::factory()->create();
    $team->users()->attach($admin->id, ['role' => 'admin']);
    $team->users()->attach($member->id, ['role' => 'member']);

    $this->actingAs($admin);

    expect(fn () => app(ChangeTeamMemberRole::class)($team, $admin, 'member'))
        ->toThrow(ValidationException::class);

    expect($team->fresh()->getRoleForUser($admin))->toBe('admin');
});

it('allows demoting an admin when another admin remains', function (): void {
    $admin = User::factory()->create();
    $otherAdmin = User::factory()->create();
    $team = Team::factory()->create();
    $team->users()->attach($admin->id, ['role' => 'admin']);
    $team->users()->attach($otherAdmin->id, ['role' => 'admin']);

    $this->actingAs($admin);

    app(ChangeTeamMemberRole::class)($team, $otherAdmin, 'member');

    expect($team->fresh()->getRoleForUser($otherAdmin))->toBe('member');
});
