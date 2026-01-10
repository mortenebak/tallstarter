<section class="w-full">
    <x-page-heading>
        <x-slot:title>{{ __('teams.create_team') }}</x-slot:title>
        <x-slot:subtitle>
            {{ __('teams.create_team_description') }}
        </x-slot:subtitle>
    </x-page-heading>

    <x-form wire:submit="create" class="space-y-6">
        <flux:input 
            wire:model="name" 
            label="{{ __('teams.name') }}" 
            placeholder="{{ __('teams.name_placeholder') }}" 
        />
        
        <flux:textarea 
            wire:model="description" 
            label="{{ __('teams.description') }}" 
            placeholder="{{ __('teams.description_placeholder') }}" 
            rows="4"
        />

        <div class="flex gap-2">
            <flux:button type="submit" icon="save" variant="primary" wire:loading.attr="disabled">
                <span wire:loading.remove>{{ __('teams.create_team') }}</span>
                <span wire:loading>{{ __('teams.creating') }}...</span>
            </flux:button>
            <flux:button href="{{ route('teams.index') }}" variant="ghost">
                {{ __('global.cancel') }}
            </flux:button>
        </div>
    </x-form>
</section>
