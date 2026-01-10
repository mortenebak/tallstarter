<?php

namespace App\Livewire\Actions;

use App\Models\Team;
use App\Models\TeamInvitation;
use App\Models\User;
use App\Notifications\TeamInvitationNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;

class InviteUserToTeam
{
    /**
     * Invite a user to a team.
     */
    public function __invoke(Team $team, string $email, string $role = 'member'): TeamInvitation
    {
        $user = Auth::user();

        // Validate the user can invite to this team
        if (! $team->isAdmin($user)) {
            throw new \Illuminate\Auth\Access\AuthorizationException(__('teams.must_be_admin'));
        }

        // Validate role
        if (! in_array($role, ['admin', 'member'], true)) {
            throw ValidationException::withMessages([
                'role' => __('teams.invalid_role'),
            ]);
        }

        // Check if user is already a member
        $existingUser = User::query()->where('email', $email)->first();
        if ($existingUser && $team->hasMember($existingUser)) {
            throw ValidationException::withMessages([
                'email' => __('teams.user_already_member'),
            ]);
        }

        // Check if there's already a pending invitation
        $existingInvitation = TeamInvitation::query()
            ->where('team_id', $team->id)
            ->where('email', $email)
            ->where('expires_at', '>', now())
            ->first();

        if ($existingInvitation) {
            throw ValidationException::withMessages([
                'email' => __('teams.invitation_already_sent'),
            ]);
        }

        // Create invitation
        $invitation = TeamInvitation::query()->create([
            'team_id' => $team->id,
            'email' => $email,
            'role' => $role,
            'expires_at' => now()->addDays(7),
        ]);

        // Send notification
        Notification::route('mail', $email)
            ->notify(new TeamInvitationNotification($invitation));

        return $invitation;
    }
}
