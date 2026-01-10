<div class="flex flex-col gap-6">
    <x-auth-header
        title="{{ __('global.confirm_password') }}"
        description="{{ __('auth.please_confirm_your_password_before_continuing') }}"
    />

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

    <form wire:submit.prevent="confirmPassword" class="flex flex-col gap-6">
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

        <flux:button variant="primary" type="submit" class="w-full">{{ __('global.confirm') }}</flux:button>
    </form>
</div>
