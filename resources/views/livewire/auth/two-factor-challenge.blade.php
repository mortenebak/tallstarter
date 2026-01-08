<div class="flex flex-col gap-6">
    <x-auth-header title="{{ __('Two-Factor Authentication') }}"
                   description="{{ $recovery ? __('Please confirm access to your account by entering one of your recovery codes.') : __('Please confirm access to your account by entering the authentication code provided by your authenticator application.') }}"/>

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')"/>

    <form wire:submit="challenge" class="flex flex-col gap-6">
        <!-- Code Input -->
        <flux:input 
            wire:model="code" 
            :label="$recovery ? __('Recovery Code') : __('Code')" 
            type="text" 
            name="code" 
            required
            autofocus
            autocomplete="one-time-code"
            :placeholder="$recovery ? 'XXXX-XXXX-XXXX' : '000000'"
        />

        <div class="flex items-center justify-between">
            <flux:button type="button" variant="ghost" size="sm" wire:click="toggleRecovery">
                {{ $recovery ? __('Use authentication code') : __('Use recovery code') }}
            </flux:button>
        </div>

        <div class="flex items-center justify-end">
            <flux:button variant="primary" type="submit" class="w-full">
                {{ __('Verify') }}
            </flux:button>
        </div>
    </form>

    <div class="space-x-1 text-center text-sm text-zinc-600 dark:text-zinc-400">
        <flux:link :href="route('login')" wire:navigate>
            {{ __('Back to login') }}
        </flux:link>
    </div>
</div>
