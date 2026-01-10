<?php

namespace App\Livewire\Actions;

use App\Models\Team;
use Illuminate\Support\Facades\Auth;

class SwitchTeam
{
    /**
     * Switch the user's current team.
     */
    public function __invoke(Team $team): void
    {
        $user = Auth::user();

        if (! $user->belongsToTeam($team)) {
            throw new \Illuminate\Auth\Access\AuthorizationException(__('teams.not_a_member'));
        }

        $user->update(['current_team_id' => $team->id]);
    }
}
