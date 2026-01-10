<?php

namespace App\Livewire\Teams;

use App\Livewire\Actions\SwitchTeam as SwitchTeamAction;
use App\Models\Team;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

class Index extends Component
{
    public function mount(): void
    {
        // Teams feature must be enabled
        if (! config('teams.enabled')) {
            abort(404);
        }
    }

    public function switchTeam(Team $team): void
    {
        try {
            app(SwitchTeamAction::class)($team);

            $this->dispatch('team-switched');
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            $this->addError('team', $e->getMessage());
        } catch (\Exception $e) {
            $this->addError('team', $e->getMessage());
        }
    }

    #[Layout('components.layouts.app')]
    public function render(): View
    {
        $user = Auth::user()->fresh();

        $currentTeam = $user->current_team_id ? $user->currentTeam : null;

        return view('livewire.teams.index', [
            'teams' => $user->teams()->with(['users'])->get(),
            'currentTeam' => $currentTeam,
        ]);
    }
}
