<?php

namespace App\Livewire\Admin\Users;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Spatie\Permission\Models\Role;

class CreateUser extends Component
{
    use LivewireAlert;

    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('required|email|max:255|unique:users,email')]
    public string $email = '';

    #[Validate('required|string|max:2')]
    public string $locale = 'en';

    /** @var array<mixed> */
    #[Validate('nullable|array')]
    public array $selectedRoles = [];

    public function mount(): void
    {
        $this->authorize('create users');
    }

    public function createUser(): void
    {
        $this->validate();

        $user = User::query()->create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make(Str::random(16)),
            'locale' => $this->locale,
        ]);

        if ($this->selectedRoles !== []) {
            // Convert the userRoles to integers
            $userRoles = Arr::map($this->selectedRoles, fn ($role): int => (int) $role);

            // Sync the user roles
            $user->syncRoles($userRoles);
        }

        $this->flash('success', __('users.user_created'));

        $this->redirect(route('admin.users.index'), true);

    }

    #[Layout('components.layouts.admin')]
    public function render(): View
    {
        return view('livewire.admin.users.create-user', [
            'roles' => Role::all(),
            'locales' => [
                'en' => 'English',
                'da' => 'Danish',
            ],
        ]);
    }
}
