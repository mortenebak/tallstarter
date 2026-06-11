<section class="w-full">
    <x-page-heading>
        <x-slot:title>
            {{ __('roles.title') }}
        </x-slot:title>
        <x-slot:subtitle>
            {{ __('roles.title_description') }}
        </x-slot:subtitle>
        <x-slot:buttons>
            @can('create roles')
                <flux:button href="{{ route('admin.roles.create') }}" variant="primary" icon="plus">
                    {{ __('roles.create_role') }}
                </flux:button>
            @endcan
        </x-slot:buttons>
    </x-page-heading>

    <div class="flex items-center justify-between w-full mb-6 gap-2">
        <flux:input wire:model.live="search" placeholder="{{ __('global.search_here') }}" aria-label="{{ __('global.search_here') }}" class="!w-auto"/>
        <flux:spacer/>

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
                <x-table.heading>{{ __('roles.name') }}</x-table.heading>
                <x-table.heading>{{ __('roles.permissions') }}</x-table.heading>
                <x-table.heading class="text-right">{{ __('global.actions') }}</x-table.heading>
            </x-table.row>
        </x-slot:head>
        <x-slot:body>
            @foreach($roles as $role)
                <x-table.row wire:key="user-{{ $role->id }}">
                    <x-table.cell>{{ $role->id }}</x-table.cell>
                    <x-table.cell class="w-1/5 text-nowrap">{{ $role->name }}</x-table.cell>
                    <x-table.cell>
                        <div class="gap-2 inline-flex flex-wrap">
                            @foreach($role->permissions as $permission)
                                <flux:badge size="sm">
                                    {{ $permission->name }}
                                </flux:badge>
                            @endforeach
                        </div>
                    </x-table.cell>
                    <x-table.cell class="space-x-2 flex justify-end">
                        @can('update roles')
                            <flux:button href="{{ route('admin.roles.edit', $role) }}" size="sm">
                                {{ __('global.edit') }}
                            </flux:button>
                        @endcan
                        @can('delete roles')
                            <flux:modal.trigger name="delete-role-{{ $role->id }}">
                                <flux:button size="sm" variant="danger">{{ __('global.delete') }}</flux:button>
                            </flux:modal.trigger>
                            <flux:modal name="delete-role-{{ $role->id }}" class="min-w-[22rem] space-y-6 flex flex-col justify-between">
                                <div>
                                    <flux:heading size="lg">{{ __('roles.delete_role') }}?</flux:heading>
                                    <flux:subheading>
                                        <p>{{ __('roles.you_are_about_to_delete') }}</p>
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
                                                 wire:click.prevent="deleteRole('{{ $role->id }}')">
                                        {{ __('roles.delete_role') }}
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
        {{ $roles->links() }}
    </div>

</section>
