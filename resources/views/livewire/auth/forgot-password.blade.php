 <div class="flex flex-col gap-6">
    <x-auth-header title="{{ __('global.forgot_password') }}" description="{{ __('global.forgot_password_description') }}" />

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

    <form wire:submit.prevent="sendPasswordResetLink" class="flex flex-col gap-6">
        <!-- Email Address -->
        <flux:input
            wire:model="email"
            :label="__('global.email_address')"
            type="email"
            name="email"
            required
            autofocus
            placeholder="email@example.com"
        />

        <flux:button variant="primary" type="submit" class="w-full">{{ __('global.send_password_reset_link') }}</flux:button>
    </form>

    <div class="space-x-1 text-center text-sm text-zinc-400">
        <span>{{ __('global.or_return_to') }}</span>
        <flux:link :href="route('login')" wire:navigate>
            {{ __('global.login') }}
        </flux:link>
    </div>
</div>
