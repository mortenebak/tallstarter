<section class="w-full">
    <x-page-heading>
        <x-slot:title>{{ __('users.create_user') }}</x-slot:title>
        <x-slot:subtitle>
            {{ __('users.create_user_description') }}
        </x-slot:subtitle>
    </x-page-heading>

    <x-form wire:submit="createUser" class="space-y-6">

        <flux:input wire:model.live="name" :label="__('users.name')" />
        <flux:input wire:model.live="email" :label="__('users.email')" />

        <flux:select wire:model="locale" label="{{ __('users.select_locale') }}" placeholder="{{ __('users.select_locale') }}" name="locale">
            @foreach($locales as $key => $locale)
                <flux:select.option value="{{ $key }}">{{ $locale }}</flux:select.option>
            @endforeach
        </flux:select>

        <flux:checkbox.group wire:model.live="selectedRoles" label="{{ __('users.roles') }}" description="{{ __('users.roles_description') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3">
            @foreach($roles as $role)
                <flux:checkbox label="{{$role->name}}" value="{{$role->id}}"/>
            @endforeach
        </flux:checkbox.group>

        <flux:button type="submit" icon="save" variant="primary">
            {{ __('users.create_user') }}
        </flux:button>
    </x-form>

</section>
