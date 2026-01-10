<?php

namespace App\Livewire\Teams;

use App\Livewire\Actions\CreateTeam as CreateTeamAction;
use Illuminate\Contracts\View\View;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

class CreateTeam extends Component
{
    use LivewireAlert;

    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('nullable|string|max:1000')]
    public string $description = '';

    public function mount(): void
    {
        // Teams feature must be enabled
        if (! config('teams.enabled')) {
            abort(404);
        }
    }

    public function create(): void
    {
        $this->validate();

        $team = app(CreateTeamAction::class)(
            name: $this->name,
            description: $this->description ?: null,
        );

        $this->flash('success', __('teams.team_created'));

        $this->redirect(route('teams.manage', $team), navigate: true);
    }

    #[Layout('components.layouts.app')]
    public function render(): View
    {
        return view('livewire.teams.create-team');
    }
}
