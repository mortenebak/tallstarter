<?php

namespace App\Livewire\Actions;

use App\Models\TeamInvitation;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class AcceptTeamInvitation
{
    /**
     * Accept a team invitation.
     */
    public function __invoke(TeamInvitation $invitation, string $token): void
    {
        // Verify token matches
        if ($invitation->token !== $token) {
            throw ValidationException::withMessages([
                'token' => __('teams.invalid_invitation_token'),
            ]);
        }

        // Check if invitation is expired
        if ($invitation->isExpired()) {
            throw ValidationException::withMessages([
                'invitation' => __('teams.invitation_expired'),
            ]);
        }

        // Get or create user by email
        $user = User::query()->where('email', $invitation->email)->first();

        if (! $user) {
            // User doesn't exist yet - this would typically redirect to registration
            // For now, we'll throw an exception
            throw ValidationException::withMessages([
                'email' => __('teams.user_not_found'),
            ]);
        }

        // Check if user is already a member
        if ($invitation->team->hasMember($user)) {
            $invitation->delete();

            throw ValidationException::withMessages([
                'team' => __('teams.user_already_member'),
            ]);
        }

        // Add user to team
        $invitation->team->users()->attach($user->id, ['role' => $invitation->role]);

        // Set as current team if user doesn't have one
        if (is_null($user->current_team_id)) {
            $user->update(['current_team_id' => $invitation->team->id]);
        }

        // Delete the invitation
        $invitation->delete();
    }
}
