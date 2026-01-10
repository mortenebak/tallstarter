<?php

namespace App\Livewire\Teams;

use App\Livewire\Actions\ChangeTeamMemberRole;
use App\Livewire\Actions\InviteUserToTeam;
use App\Livewire\Actions\RemoveUserFromTeam;
use App\Models\Team;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

class ManageTeam extends Component
{
    use LivewireAlert;

    public Team $team;

    #[Validate('required|email|max:255')]
    public string $inviteEmail = '';

    #[Validate('required|in:admin,member')]
    public string $inviteRole = 'member';

    public ?int $editingUserId = null;

    #[Validate('required|in:admin,member')]
    public string $newRole = 'member';

    public function mount(Team $team): void
    {
        // Teams feature must be enabled
        if (! config('teams.enabled')) {
            abort(404);
        }

        $user = Auth::user();

        $this->team = $team->load(['users', 'invitations']);

        // Check if user is member of team
        if (! $user->belongsToTeam($this->team)) {
            abort(403);
        }
    }

    public function inviteUser(): void
    {
        $user = Auth::user();

        if (! $this->team->isAdmin($user)) {
            $this->addError('authorization', __('teams.must_be_admin'));

            return;
        }

        $this->validate([
            'inviteEmail' => 'required|email|max:255',
            'inviteRole' => 'required|in:admin,member',
        ]);

        try {
            app(InviteUserToTeam::class)(
                team: $this->team,
                email: $this->inviteEmail,
                role: $this->inviteRole,
            );

            $this->alert('success', __('teams.invitation_sent'));

            $this->inviteEmail = '';
            $this->inviteRole = 'member';

            $this->team->refresh();
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->addError('inviteEmail', $e->getMessage());
        } catch (\Exception $e) {
            $this->addError('error', $e->getMessage());
        }
    }

    public function editRole(User $user): void
    {
        $currentRole = $this->team->getRoleForUser($user);
        $this->editingUserId = $user->id;
        $this->newRole = $currentRole ?? 'member';
    }

    public function updateRole(): void
    {
        $currentUser = Auth::user();

        if (! $this->team->isAdmin($currentUser)) {
            $this->addError('authorization', __('teams.must_be_admin'));

            return;
        }

        if (! $this->editingUserId) {
            return;
        }

        $user = User::query()->findOrFail($this->editingUserId);

        $this->validate([
            'newRole' => 'required|in:admin,member',
        ]);

        try {
            app(ChangeTeamMemberRole::class)(
                team: $this->team,
                member: $user,
                role: $this->newRole,
            );

            $this->alert('success', __('teams.role_updated'));

            $this->editingUserId = null;
            $this->newRole = 'member';

            $this->team->refresh();
        } catch (\Illuminate\Validation\ValidationException $e) {
            $errors = $e->errors();
            foreach ($errors as $key => $messages) {
                $this->addError($key, $messages[0] ?? $e->getMessage());
            }
        } catch (\Exception $e) {
            $this->addError('role', $e->getMessage());
        }
    }

    public function cancelEditRole(): void
    {
        $this->editingUserId = null;
        $this->newRole = 'member';
    }

    public function removeUser(User $user): void
    {
        $currentUser = Auth::user();

        if (! $this->team->isAdmin($currentUser)) {
            $this->addError('authorization', __('teams.must_be_admin'));

            return;
        }

        try {
            app(RemoveUserFromTeam::class)(
                team: $this->team,
                userToRemove: $user,
            );

            $this->alert('success', __('teams.user_removed'));

            $this->team->refresh();
        } catch (\Illuminate\Validation\ValidationException $e) {
            $errors = $e->errors();
            foreach ($errors as $key => $messages) {
                $this->addError($key, $messages[0] ?? $e->getMessage());
            }
        } catch (\Exception $e) {
            $this->addError('user', $e->getMessage());
        }
    }

    #[Layout('components.layouts.app')]
    public function render(): View
    {
        $user = Auth::user();

        return view('livewire.teams.manage-team', [
            'isAdmin' => $this->team->isAdmin($user),
            'members' => $this->team->users()->get(),
            'invitations' => $this->team->invitations()->where('expires_at', '>', now())->get(),
        ]);
    }
}
