<?php

use App\Livewire\Actions\InviteUserToTeam;
use App\Models\Team;
use App\Models\TeamInvitation;
use App\Models\User;
use App\Notifications\TeamInvitationNotification;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    Config::set('teams.enabled', true);
    Notification::fake();
});

it('allows a team admin to invite a user', function (): void {
    $admin = User::factory()->create();
    $team = Team::factory()->create();
    $team->users()->attach($admin->id, ['role' => 'admin']);

    $this->actingAs($admin);

    $invitation = app(InviteUserToTeam::class)($team, 'newuser@example.com', 'member');

    expect($invitation)->toBeInstanceOf(TeamInvitation::class);
    expect($invitation->team_id)->toBe($team->id);
    expect($invitation->email)->toBe('newuser@example.com');
    expect($invitation->role)->toBe('member');
    expect($invitation->token)->not->toBeEmpty();
    expect($invitation->expires_at->isFuture())->toBeTrue();

    Notification::assertSentOnDemand(
        TeamInvitationNotification::class,
        fn ($notification, $channels, $notifiable): bool => $notifiable->routes['mail'] === 'newuser@example.com'
    );
});

it('prevents a non-admin from inviting users', function (): void {
    $member = User::factory()->create();
    $team = Team::factory()->create();
    $team->users()->attach($member->id, ['role' => 'member']);

    $this->actingAs($member);

    expect(fn () => app(InviteUserToTeam::class)($team, 'newuser@example.com'))
        ->toThrow(AuthorizationException::class);

    Notification::assertNothingSent();
});

it('rejects an invalid role', function (): void {
    $admin = User::factory()->create();
    $team = Team::factory()->create();
    $team->users()->attach($admin->id, ['role' => 'admin']);

    $this->actingAs($admin);

    expect(fn () => app(InviteUserToTeam::class)($team, 'newuser@example.com', 'owner'))
        ->toThrow(ValidationException::class);
});

it('blocks inviting a user who is already a member', function (): void {
    $admin = User::factory()->create();
    $member = User::factory()->create();
    $team = Team::factory()->create();
    $team->users()->attach($admin->id, ['role' => 'admin']);
    $team->users()->attach($member->id, ['role' => 'member']);

    $this->actingAs($admin);

    expect(fn () => app(InviteUserToTeam::class)($team, $member->email))
        ->toThrow(ValidationException::class);

    Notification::assertNothingSent();
});

it('blocks a duplicate pending invitation', function (): void {
    $admin = User::factory()->create();
    $team = Team::factory()->create();
    $team->users()->attach($admin->id, ['role' => 'admin']);

    $this->actingAs($admin);

    app(InviteUserToTeam::class)($team, 'newuser@example.com');

    expect(fn () => app(InviteUserToTeam::class)($team, 'newuser@example.com'))
        ->toThrow(ValidationException::class);

    expect(TeamInvitation::query()->where('email', 'newuser@example.com')->count())->toBe(1);
});

it('replaces an expired invitation with a new one', function (): void {
    $admin = User::factory()->create();
    $team = Team::factory()->create();
    $team->users()->attach($admin->id, ['role' => 'admin']);

    $expiredInvitation = TeamInvitation::query()->create([
        'team_id' => $team->id,
        'email' => 'newuser@example.com',
        'role' => 'member',
        'expires_at' => now()->subDay(),
    ]);

    $this->actingAs($admin);

    $invitation = app(InviteUserToTeam::class)($team, 'newuser@example.com');

    expect($invitation->exists)->toBeTrue();
    expect($invitation->expires_at->isFuture())->toBeTrue();
    expect(TeamInvitation::query()->find($expiredInvitation->id))->toBeNull();
    expect(TeamInvitation::query()->where('email', 'newuser@example.com')->count())->toBe(1);
});
