<?php

namespace App\Livewire\Actions;

use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class RemoveUserFromTeam
{
    /**
     * Remove a user from a team.
     */
    public function __invoke(Team $team, User $userToRemove): void
    {
        $user = Auth::user();

        // Validate the user can remove from this team
        if (! $team->isAdmin($user)) {
            throw new \Illuminate\Auth\Access\AuthorizationException(__('teams.must_be_admin'));
        }

        // Cannot remove self
        if ($user->id === $userToRemove->id) {
            throw ValidationException::withMessages([
                'user' => __('teams.cannot_remove_self'),
            ]);
        }

        // Check if user is a member
        if (! $team->hasMember($userToRemove)) {
            throw ValidationException::withMessages([
                'user' => __('teams.user_not_member'),
            ]);
        }

        // Check if removing the last admin
        $adminCount = $team->admins()->count();
        $isAdmin = $team->isAdmin($userToRemove);

        if ($isAdmin && $adminCount === 1) {
            throw ValidationException::withMessages([
                'user' => __('teams.cannot_remove_last_admin'),
            ]);
        }

        // Remove user from team
        $team->users()->detach($userToRemove->id);

        // If this was their current team, clear it
        if ($userToRemove->current_team_id === $team->id) {
            $firstTeam = $userToRemove->teams()->first();
            $userToRemove->update([
                'current_team_id' => $firstTeam?->id,
            ]);
        }
    }
}
