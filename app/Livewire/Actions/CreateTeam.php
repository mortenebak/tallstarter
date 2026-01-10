<?php

namespace App\Livewire\Actions;

use App\Models\Team;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CreateTeam
{
    /**
     * Create a new team.
     */
    public function __invoke(string $name, ?string $description = null): Team
    {
        $user = Auth::user();

        if (! $user) {
            throw new \Illuminate\Auth\AuthenticationException('User must be authenticated to create a team.');
        }

        $team = Team::query()->create([
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => $description,
        ]);

        // Add the creator as an admin
        $team->users()->attach($user->id, ['role' => 'admin']);

        // Set as current team if user doesn't have one
        $user->refresh();
        if (is_null($user->current_team_id)) {
            $user->update(['current_team_id' => $team->id]);
        }

        return $team;
    }
}
