<div class="flex flex-col gap-6">
    <x-auth-header title="{{ __('global.two_factor_authentication') }}"
                   description="{{ $recovery ? __('global.confirm_access_with_recovery_code') : __('global.confirm_access_with_authentication_code') }}"/>

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')"/>

    <form wire:submit.prevent="challenge" class="flex flex-col gap-6">
        <!-- Code Input -->
        <flux:input 
            wire:model="code" 
            :label="$recovery ? __('global.recovery_code') : __('global.code')"
            type="text" 
            name="code" 
            required
            autofocus
            autocomplete="one-time-code"
            :placeholder="$recovery ? 'XXXX-XXXX-XXXX' : '000000'"
        />

        <div class="flex items-center justify-between">
            <flux:button type="button" variant="ghost" size="sm" wire:click="toggleRecovery">
                {{ $recovery ? __('global.use_authentication_code') : __('global.use_recovery_code') }}
            </flux:button>
        </div>

        <div class="flex items-center justify-end">
            <flux:button variant="primary" type="submit" class="w-full">
                {{ __('global.verify') }}
            </flux:button>
        </div>
    </form>

    <div class="space-x-1 text-center text-sm text-zinc-600 dark:text-zinc-400">
        <flux:link :href="route('login')" wire:navigate>
            {{ __('global.back_to_login') }}
        </flux:link>
    </div>
</div>
