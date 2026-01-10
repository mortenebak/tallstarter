<div class="flex flex-col gap-6">
    <x-auth-header title="{{ __('global.reset_password') }}" description="{{ __('auth.reset_password_description') }}" />

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

    <form wire:submit.prevent="resetPassword" class="flex flex-col gap-6">
        <!-- Email Address -->
        <flux:input
            wire:model="email"
            id="email"
            :label="__('global.email_address')"
            type="email"
            name="email"
            required
            autocomplete="email"
        />

        <!-- Password -->
        <flux:input
            wire:model="password"
            id="password"
            :label="__('global.password')"
            type="password"
            name="password"
            required
            autocomplete="new-password"
            placeholder="{{ __('global.password') }}"
        />

        <!-- Confirm Password -->
        <flux:input
            wire:model="password_confirmation"
            id="password_confirmation"
            :label="__('global.confirm_password')"
            type="password"
            name="password_confirmation"
            required
            autocomplete="new-password"
            placeholder="{{ __('global.confirm_password') }}"
        />

        <div class="flex items-center justify-end">
            <flux:button type="submit" variant="primary" class="w-full">
                {{ __('global.reset_password') }}
            </flux:button>
        </div>
    </form>
</div>
