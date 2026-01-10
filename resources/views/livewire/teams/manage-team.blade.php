<section class="w-full space-y-6">
    <x-page-heading>
        <x-slot:title>{{ $team->name }}</x-slot:title>
        <x-slot:subtitle>
            {{ __('teams.manage_team_description') }}
        </x-slot:subtitle>
        <x-slot:buttons>
            @if(Route::has('teams.index'))
                <flux:button href="{{ route('teams.index') }}" variant="ghost">
                    {{ __('global.back') }}
                </flux:button>
            @endif
        </x-slot:buttons>
    </x-page-heading>

    @if($isAdmin)
        {{-- Invite User Section --}}
        <x-form wire:submit="inviteUser" class="space-y-4 border p-4 rounded-lg dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-900/50">
            <flux:heading size="md">{{ __('teams.invite_user') }}</flux:heading>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <flux:input
                    wire:model="inviteEmail"
                    label="{{ __('teams.email') }}"
                    placeholder="{{ __('teams.email_placeholder') }}"
                    type="email"
                />

                <flux:select wire:model="inviteRole" label="{{ __('teams.role') }}">
                    <flux:select.option value="member">{{ __('teams.member') }}</flux:select.option>
                    <flux:select.option value="admin">{{ __('teams.admin') }}</flux:select.option>
                </flux:select>
            </div>

            <flux:button type="submit" icon="plus" variant="primary" wire:loading.attr="disabled">
                <span wire:loading.remove>{{ __('teams.send_invitation') }}</span>
                <span wire:loading>{{ __('teams.sending') }}...</span>
            </flux:button>
        </x-form>

        {{-- Pending Invitations --}}
        @if($invitations->isNotEmpty())
            <div class="border rounded-lg dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-900/50 p-4">
                <flux:heading size="md" class="mb-4">{{ __('teams.pending_invitations') }}</flux:heading>
                <x-table>
                    <x-slot:head>
                        <x-table.row>
                            <x-table.heading>{{ __('teams.email') }}</x-table.heading>
                            <x-table.heading>{{ __('teams.role') }}</x-table.heading>
                            <x-table.heading>{{ __('teams.expires_at') }}</x-table.heading>
                        </x-table.row>
                    </x-slot:head>
                    <x-slot:body>
                        @foreach($invitations as $invitation)
                            <x-table.row wire:key="invitation-{{ $invitation->id }}">
                                <x-table.cell>{{ $invitation->email }}</x-table.cell>
                                <x-table.cell>
                                    <flux:badge>{{ $invitation->role }}</flux:badge>
                                </x-table.cell>
                                <x-table.cell>{{ $invitation->expires_at->diffForHumans() }}</x-table.cell>
                            </x-table.row>
                        @endforeach
                    </x-slot:body>
                </x-table>
            </div>
        @endif
    @endif

    {{-- Team Members --}}
    <div class="border rounded-lg dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-900/50 p-4">
        <flux:heading size="md" class="mb-4">{{ __('teams.members') }}</flux:heading>
        <x-table>
            <x-slot:head>
                <x-table.row>
                    <x-table.heading>{{ __('users.name') }}</x-table.heading>
                    <x-table.heading>{{ __('users.email') }}</x-table.heading>
                    <x-table.heading>{{ __('teams.role') }}</x-table.heading>
                    @if($isAdmin)
                        <x-table.heading class="text-right">{{ __('global.actions') }}</x-table.heading>
                    @endif
                </x-table.row>
            </x-slot:head>
            <x-slot:body>
                @foreach($members as $member)
                    <x-table.row wire:key="member-{{ $member->id }}">
                        <x-table.cell>{{ $member->name }}</x-table.cell>
                        <x-table.cell>{{ $member->email }}</x-table.cell>
                        <x-table.cell>
                            @if($editingUserId === $member->id)
                                <div class="flex gap-2 items-center">
                                    <flux:select wire:model="newRole" size="sm">
                                        <flux:select.option value="member">{{ __('teams.member') }}</flux:select.option>
                                        <flux:select.option value="admin">{{ __('teams.admin') }}</flux:select.option>
                                    </flux:select>
                                    <flux:button wire:click="updateRole" size="sm" variant="primary">
                                        {{ __('global.save') }}
                                    </flux:button>
                                    <flux:button wire:click="cancelEditRole" size="sm" variant="ghost">
                                        {{ __('global.cancel') }}
                                    </flux:button>
                                </div>
                            @else
                                <flux:badge variant="{{ $team->getRoleForUser($member) === 'admin' ? 'success' : 'default' }}">
                                    {{ $team->getRoleForUser($member) }}
                                </flux:badge>
                            @endif
                        </x-table.cell>
                        @if($isAdmin)
                            <x-table.cell class="gap-2 flex justify-end">
                                @if($editingUserId !== $member->id)
                                    <flux:button
                                        wire:click="editRole({{ $member->id }})"
                                        size="sm"
                                        variant="ghost">
                                        {{ __('global.edit') }}
                                    </flux:button>
                                    @if($member->id !== auth()->id())
                                        <flux:modal.trigger name="remove-member-{{ $member->id }}">
                                            <flux:button size="sm" variant="danger">
                                                {{ __('teams.remove') }}
                                            </flux:button>
                                        </flux:modal.trigger>
                                        <flux:modal name="remove-member-{{ $member->id }}" class="min-w-[22rem] space-y-6 flex flex-col justify-between">
                                            <div>
                                                <flux:heading size="lg">{{ __('teams.remove_member') }}?</flux:heading>
                                                <flux:subheading>
                                                    <p>{{ __('teams.you_are_about_to_remove', ['name' => $member->name]) }}</p>
                                                    <p>{{ __('global.this_action_is_irreversible') }}</p>
                                                </flux:subheading>
                                            </div>
                                            <div class="flex gap-2 !mt-auto mb-0">
                                                <flux:modal.close>
                                                    <flux:button variant="ghost">
                                                        {{ __('global.cancel') }}
                                                    </flux:button>
                                                </flux:modal.close>
                                                <flux:spacer/>
                                                <flux:button
                                                    type="submit"
                                                    variant="danger"
                                                    wire:click.prevent="removeUser({{ $member->id }})">
                                                    {{ __('teams.remove') }}
                                                </flux:button>
                                            </div>
                                        </flux:modal>
                                    @endif
                                @endif
                            </x-table.cell>
                        @endif
                    </x-table.row>
                @endforeach
            </x-slot:body>
        </x-table>
    </div>
</section>
