<?php

use App\Livewire\Teams\ManageTeam;
use App\Models\Team;
use App\Models\TeamInvitation;
use App\Models\User;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    Config::set('teams.enabled', true);
    Notification::fake();
});

it('returns 404 when teams feature is disabled', function (): void {
    Config::set('teams.enabled', false);

    $user = User::factory()->create();
    $team = Team::factory()->create();

    $this->actingAs($user)
        ->get(route('teams.manage', $team))
        ->assertNotFound();
});

it('returns 403 if user is not a member of team', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->create();

    $this->actingAs($user)
        ->get(route('teams.manage', $team))
        ->assertForbidden();
});

it('allows team members to view team management', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->create();

    $user->teams()->attach($team->id, ['role' => 'member']);

    $this->actingAs($user)
        ->get(route('teams.manage', $team))
        ->assertOk();
});

it('allows admin to invite users to team', function (): void {
    $admin = User::factory()->create();
    $team = Team::factory()->create();

    $admin->teams()->attach($team->id, ['role' => 'admin']);

    Livewire::actingAs($admin)
        ->test(ManageTeam::class, ['team' => $team])
        ->set('inviteEmail', 'newuser@example.com')
        ->set('inviteRole', 'member')
        ->call('inviteUser')
        ->assertHasNoErrors();

    expect(TeamInvitation::where('team_id', $team->id)->where('email', 'newuser@example.com')->exists())->toBeTrue();
    Notification::assertSentTimes(\App\Notifications\TeamInvitationNotification::class, 1);
});

it('prevents non-admin from inviting users', function (): void {
    $member = User::factory()->create();
    $team = Team::factory()->create();

    $member->teams()->attach($team->id, ['role' => 'member']);

    Livewire::actingAs($member)
        ->test(ManageTeam::class, ['team' => $team])
        ->set('inviteEmail', 'newuser@example.com')
        ->set('inviteRole', 'member')
        ->call('inviteUser')
        ->assertHasErrors();
});

it('allows admin to change member role', function (): void {
    $admin = User::factory()->create();
    $member = User::factory()->create();
    $team = Team::factory()->create();

    $admin->teams()->attach($team->id, ['role' => 'admin']);
    $team->users()->attach($member->id, ['role' => 'member']);

    expect($team->getRoleForUser($member))->toBe('member');

    Livewire::actingAs($admin)
        ->test(ManageTeam::class, ['team' => $team])
        ->call('editRole', $member)
        ->set('newRole', 'admin')
        ->call('updateRole')
        ->assertHasNoErrors();

    expect($team->fresh()->getRoleForUser($member))->toBe('admin');
});

it('prevents removing last admin', function (): void {
    $admin = User::factory()->create();
    $member = User::factory()->create();
    $team = Team::factory()->create();

    $admin->teams()->attach($team->id, ['role' => 'admin']);
    $team->users()->attach($member->id, ['role' => 'member']);

    expect($team->admins()->count())->toBe(1);

    Livewire::actingAs($admin)
        ->test(ManageTeam::class, ['team' => $team])
        ->call('editRole', $admin)
        ->set('newRole', 'member')
        ->call('updateRole')
        ->assertHasErrors();
});

it('allows admin to remove member from team', function (): void {
    $admin = User::factory()->create();
    $member = User::factory()->create();
    $team = Team::factory()->create();

    $admin->teams()->attach($team->id, ['role' => 'admin']);
    $team->users()->attach($member->id, ['role' => 'member']);

    expect($team->users()->where('user_id', $member->id)->exists())->toBeTrue();

    Livewire::actingAs($admin)
        ->test(ManageTeam::class, ['team' => $team])
        ->call('removeUser', $member)
        ->assertHasNoErrors();

    expect($team->fresh()->users()->where('user_id', $member->id)->exists())->toBeFalse();
});

it('prevents removing last admin from team', function (): void {
    $admin = User::factory()->create();
    $team = Team::factory()->create();

    $admin->teams()->attach($team->id, ['role' => 'admin']);

    expect($team->admins()->count())->toBe(1);

    Livewire::actingAs($admin)
        ->test(ManageTeam::class, ['team' => $team])
        ->call('removeUser', $admin)
        ->assertHasErrors();
});

it('prevents removing self from team', function (): void {
    $admin = User::factory()->create();
    $member = User::factory()->create();
    $team = Team::factory()->create();

    $admin->teams()->attach($team->id, ['role' => 'admin']);
    $team->users()->attach($member->id, ['role' => 'admin']);

    Livewire::actingAs($admin)
        ->test(ManageTeam::class, ['team' => $team])
        ->call('removeUser', $admin)
        ->assertHasErrors();
});
