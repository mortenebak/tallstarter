<?php

use App\Livewire\Actions\InviteUserToTeam;
use App\Models\Team;
use App\Models\TeamInvitation;
use App\Models\User;
use App\Notifications\TeamInvitationNotification;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Notification;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    Config::set('teams.enabled', true);
});

it('sends an invitation notification to the invited email', function (): void {
    Notification::fake();

    $admin = User::factory()->create();
    $team = Team::factory()->create();
    $team->users()->attach($admin->id, ['role' => 'admin']);

    $this->actingAs($admin);

    $invitation = app(InviteUserToTeam::class)($team, 'invitee@example.com', 'member');

    Notification::assertSentOnDemand(
        TeamInvitationNotification::class,
        fn (TeamInvitationNotification $notification, array $channels, AnonymousNotifiable $notifiable): bool => $notifiable->routes['mail'] === 'invitee@example.com'
            && $notification->invitation->is($invitation)
            && in_array('mail', $channels, true)
    );
});

it('contains the accept url in the mail message', function (): void {
    $team = Team::factory()->create();
    $invitation = TeamInvitation::query()->create([
        'team_id' => $team->id,
        'email' => 'invitee@example.com',
        'role' => 'member',
        'expires_at' => now()->addDays(7),
    ]);

    $mailMessage = (new TeamInvitationNotification($invitation))->toMail(new AnonymousNotifiable);

    $expectedUrl = route('teams.invitations.accept', [
        'invitation' => $invitation->id,
        'token' => $invitation->token,
    ], absolute: true);

    expect($mailMessage->actionUrl)->toBe($expectedUrl);
    expect($mailMessage->subject)->toContain($team->name);
});

it('includes the team details in the array representation', function (): void {
    $team = Team::factory()->create();
    $invitation = TeamInvitation::query()->create([
        'team_id' => $team->id,
        'email' => 'invitee@example.com',
        'role' => 'admin',
        'expires_at' => now()->addDays(7),
    ]);

    $payload = (new TeamInvitationNotification($invitation))->toArray(new AnonymousNotifiable);

    expect($payload)->toBe([
        'team_id' => $team->id,
        'team_name' => $team->name,
        'role' => 'admin',
    ]);
});
