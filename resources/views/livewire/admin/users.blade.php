<section class="w-full">
    <x-page-heading>
        <x-slot:title>
            {{ __('users.title') }}
        </x-slot:title>
        <x-slot:subtitle>
            {{ __('users.title_description') }}
        </x-slot:subtitle>
        <x-slot:buttons>
            @can('create users')
                <flux:button href="{{ route('admin.users.create') }}" variant="primary" icon="plus">
                    {{ __('users.create_user') }}
                </flux:button>
            @endcan
        </x-slot:buttons>
    </x-page-heading>

    <div class="flex items-center justify-between w-full mb-6 gap-2">
        <flux:input wire:model.live="search" placeholder="{{ __('global.search_here') }}" aria-label="{{ __('global.search_here') }}" class="!w-auto"/>
        <flux:spacer/>
        <flux:select wire:model.live="role" class="!w-auto">
            <flux:select.option value="">{{ __('users.all_roles') }}</flux:select.option>
            @foreach($roles as $role)
                <flux:select.option value="{{ $role->name }}">{{ $role->name }}</flux:select.option>
            @endforeach
        </flux:select>

        <flux:select wire:model.live="perPage" class="!w-auto">
            <flux:select.option value="10">{{ __('global.10_per_page') }}</flux:select.option>
            <flux:select.option value="25">{{ __('global.25_per_page') }}</flux:select.option>
            <flux:select.option value="50">{{ __('global.50_per_page') }}</flux:select.option>
            <flux:select.option value="100">{{ __('global.100_per_page') }}</flux:select.option>
        </flux:select>
    </div>

    <x-table>
        <x-slot:head>
            <x-table.row>
                <x-table.heading>{{ __('global.id') }}</x-table.heading>
                <x-table.heading>{{ __('users.name') }}</x-table.heading>
                <x-table.heading>{{ __('users.email') }}</x-table.heading>
                <x-table.heading>{{ __('users.roles') }}</x-table.heading>
                <x-table.heading class="text-right">{{ __('global.actions') }}</x-table.heading>
            </x-table.row>
        </x-slot:head>
        <x-slot:body>
            @foreach($users as $user)
                <x-table.row wire:key="user-{{ $user->id }}">
                    <x-table.cell>{{ $user->id }}</x-table.cell>
                    <x-table.cell>{{ $user->name }}</x-table.cell>
                    <x-table.cell>{{ $user->email }}</x-table.cell>
                    <x-table.cell>
                        @foreach($user->roles as $role)
                            <flux:badge size="sm">
                                {{ $role->name }}
                            </flux:badge>
                        @endforeach
                    </x-table.cell>
                    <x-table.cell class="gap-2 flex justify-end">

                        <flux:button href="{{ route('admin.users.show', $user) }}" size="sm" variant="ghost">
                            {{ __('global.view') }}
                        </flux:button>

                        @can('impersonate')
                            @if(auth()->user()->id !== $user->id)
                                <form action="{{ route('impersonate.store', $user) }}" method="POST">
                                    @csrf
                                    <flux:button type="submit" size="sm">
                                        {{ __('users.impersonate') }}
                                    </flux:button>
                                </form>
                            @endif
                        @endcan

                        @can('update users')
                            <flux:button href="{{ route('admin.users.edit', $user) }}" size="sm">
                                {{ __('global.edit') }}
                            </flux:button>
                        @endcan

                        @can('delete users')
                            <flux:modal.trigger name="delete-profile-{{ $user->id }}">
                                <flux:button size="sm" variant="danger">{{ __('global.delete') }}</flux:button>
                            </flux:modal.trigger>
                            <flux:modal name="delete-profile-{{ $user->id }}"
                                        class="min-w-[22rem] space-y-6 flex flex-col justify-between">
                                <div>
                                    <flux:heading size="lg">{{ __('users.delete_user') }}?</flux:heading>
                                    <flux:subheading>
                                        <p>{{ __('users.you_are_about_to_delete') }}</p>
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
                                    <flux:button type="submit" variant="danger"
                                                 wire:click.prevent="deleteUser('{{ $user->id }}')">
                                        {{ __('users.delete_user') }}
                                    </flux:button>
                                </div>
                            </flux:modal>
                        @endcan
                    </x-table.cell>
                </x-table.row>
            @endforeach
        </x-slot:body>
    </x-table>

    <div>
        {{ $users->links() }}
    </div>
</section>
