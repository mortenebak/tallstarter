<section class="w-full">
    <x-page-heading>
        <x-slot:title>{{ __('permissions.title') }}</x-slot:title>
        <x-slot:subtitle>{{ __('permissions.title_description') }}</x-slot:subtitle>
        <x-slot:buttons>
            @can('create permissions')
                <flux:button href="{{ route('admin.permissions.create') }}" variant="primary" icon="plus">
                    {{ __('permissions.create_permission') }}
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
                <x-table.heading>{{ __('permissions.name') }}</x-table.heading>
                <x-table.heading class="text-right">{{ __('global.actions') }}</x-table.heading>
            </x-table.row>
        </x-slot:head>
        <x-slot:body>
            @foreach($permissions as $permission)
                <x-table.row wire:key="user-{{ $permission->id }}">
                    <x-table.cell>{{ $permission->id }}</x-table.cell>
                    <x-table.cell>{{ $permission->name }}</x-table.cell>
                    <x-table.cell class="space-x-2 flex justify-end">

                        @can('update permissions')
                            <flux:button href="{{ route('admin.permissions.edit', $permission) }}" size="sm">
                                {{ __('global.edit') }}
                            </flux:button>
                        @endcan
                        @can('delete permissions')
                            <flux:modal.trigger name="delete-profile-{{ $permission->id }}">
                                <flux:button size="sm" variant="danger">{{ __('global.delete') }}</flux:button>
                            </flux:modal.trigger>
                            <flux:modal name="delete-profile-{{ $permission->id }}"
                                        class="min-w-[22rem] space-y-6 flex flex-col justify-between">
                                <div>
                                    <flux:heading size="lg">{{ __('permissions.delete_permission') }}?</flux:heading>
                                    <flux:subheading>
                                        <p>{{ __('permissions.you_are_about_to_delete') }}</p>
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
                                                 wire:click.prevent="deletePermission('{{ $permission->id }}')">
                                        {{ __('permissions.delete_permission') }}
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
        {{ $permissions->links() }}
    </div>

</section>
