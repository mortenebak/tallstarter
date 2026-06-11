@props([
    'align' => 'end',
])

<flux:dropdown x-data :align="$align" {{ $attributes }}>
    <flux:button variant="subtle" square class="group" aria-label="{{ __('settings.appearance') }}">
        <flux:icon.sun x-show="$flux.appearance === 'light'" variant="mini" class="text-zinc-500 dark:text-white" />
        <flux:icon.moon x-show="$flux.appearance === 'dark'" variant="mini" class="text-zinc-500 dark:text-white" />
        <flux:icon.moon x-show="$flux.appearance === 'system' && $flux.dark" variant="mini" class="text-zinc-500 dark:text-white" />
        <flux:icon.sun x-show="$flux.appearance === 'system' && ! $flux.dark" variant="mini" class="text-zinc-500 dark:text-white" />
    </flux:button>

    <flux:menu>
        <flux:menu.radio.group x-model="$flux.appearance">
            <flux:menu.radio value="light" icon="sun">{{ __('settings.light') }}</flux:menu.radio>
            <flux:menu.radio value="dark" icon="moon">{{ __('settings.dark') }}</flux:menu.radio>
            <flux:menu.radio value="system" icon="computer-desktop">{{ __('settings.system') }}</flux:menu.radio>
        </flux:menu.radio.group>
    </flux:menu>
</flux:dropdown>
