<?php

namespace App\Livewire\Actions;

use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ChangeTeamMemberRole
{
    /**
     * Change a team member's role.
     */
    public function __invoke(Team $team, User $member, string $role): void
    {
        $user = Auth::user();

        // Validate the user can change roles in this team
        if (! $team->isAdmin($user)) {
            throw new \Illuminate\Auth\Access\AuthorizationException(__('teams.must_be_admin'));
        }

        // Validate role
        if (! in_array($role, ['admin', 'member'], true)) {
            throw ValidationException::withMessages([
                'role' => __('teams.invalid_role'),
            ]);
        }

        // Check if user is a member
        if (! $team->hasMember($member)) {
            throw ValidationException::withMessages([
                'user' => __('teams.user_not_member'),
            ]);
        }

        // Get current admin count
        $adminCount = $team->admins()->count();
        $isCurrentlyAdmin = $team->isAdmin($member);

        // If changing from admin to member, check there's at least one admin left
        if ($isCurrentlyAdmin && $role === 'member' && $adminCount === 1) {
            throw ValidationException::withMessages([
                'role' => __('teams.cannot_remove_last_admin'),
            ]);
        }

        // Update role
        $team->users()->updateExistingPivot($member->id, ['role' => $role]);
    }
}
