<section class="w-full">
    <x-page-heading>
        <x-slot:title>
            {{ __('teams.title') }}
        </x-slot:title>
        <x-slot:subtitle>
            {{ __('teams.title_description') }}
        </x-slot:subtitle>
        <x-slot:buttons>
            <flux:button href="{{ route('teams.create') }}" variant="primary" icon="plus">
                {{ __('teams.create_team') }}
            </flux:button>
        </x-slot:buttons>
    </x-page-heading>

    @if($teams->isEmpty())
        <flux:callout variant="info">
            {{ __('teams.no_teams') }}
        </flux:callout>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($teams as $team)
                <div wire:key="team-{{ $team->id }}" class="border rounded-lg p-4 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-900/50 {{ $currentTeam?->id === $team->id ? 'ring-2 ring-primary-500' : '' }}">
                    <div class="flex items-start justify-between mb-2">
                        <div>
                            <h3 class="text-lg font-semibold">{{ $team->name }}</h3>
                            @if($team->description)
                                <p class="text-sm text-zinc-600 dark:text-zinc-400 mt-1">{{ $team->description }}</p>
                            @endif
                        </div>
                        @if($currentTeam?->id === $team->id)
                            <flux:badge variant="success">{{ __('teams.current_team') }}</flux:badge>
                        @endif
                    </div>

                    <div class="mt-4 flex gap-2">
                        <flux:button 
                            href="{{ route('teams.manage', $team) }}" 
                            size="sm" 
                            variant="ghost">
                            {{ __('teams.manage') }}
                        </flux:button>
                        @if($currentTeam?->id !== $team->id)
                            <flux:button 
                                wire:click="switchTeam({{ $team->id }})" 
                                size="sm" 
                                wire:loading.attr="disabled">
                                <span wire:loading.remove>{{ __('teams.switch_team') }}</span>
                                <span wire:loading>{{ __('teams.switching') }}...</span>
                            </flux:button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</section>
