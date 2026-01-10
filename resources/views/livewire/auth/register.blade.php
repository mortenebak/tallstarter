<div class="flex flex-col gap-6">
    <x-auth-header title="{{ __('global.create_an_account') }}" description="{{ __('global.create_an_account_description') }}" />
    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />
    <form wire:submit.prevent="register" class="flex flex-col gap-6">
        <!-- Name -->
        <flux:input wire:model="name" id="name" :label="__('users.name')" type="text" name="name" required autofocus autocomplete="name" placeholder="{{ __('users.your_full_name') }}" />
        <!-- Email Address -->
        <flux:input wire:model="email" id="email" :label="__('global.email_address')" type="email" name="email" required autocomplete="email" placeholder="email@example.com" />
        <!-- Password -->
        <flux:input wire:model="password" id="password" :label="__('global.password')" :type="$this->passwordVisible ? 'text' : 'password'" name="password" required autocomplete="new-password" placeholder="{{ __('global.password') }}">
            <x-slot name="iconTrailing">
                <flux:button size="sm" variant="subtle" icon="{{ $this->passwordVisible ? 'eye-slash' : 'eye' }}" class="-mr-1" wire:click.prevent="$toggle('passwordVisible')"/>
            </x-slot>
        </flux:input>
        <!-- Confirm Password -->
        <flux:input wire:model="password_confirmation" id="password_confirmation" :label="__('global.confirm_password')" :type="$this->ConfirmationPasswordVisible ? 'text' : 'password'" name="password_confirmation" required autocomplete="new-password" placeholder="{{ __('global.confirm_password') }}">
            <x-slot name="iconTrailing">
                <flux:button size="sm" variant="subtle" icon="{{ $this->ConfirmationPasswordVisible ? 'eye-slash' : 'eye' }}" class="-mr-1" wire:click.prevent="$toggle('ConfirmationPasswordVisible')"/>
            </x-slot>
        </flux:input>
        <div class="flex items-center justify-end">
            <flux:button type="submit" variant="primary" class="w-full"> {{ __('global.create_an_account') }} </flux:button>
        </div>

        @php
            $hasGoogle = config('services.google.client_id') && config('services.google.client_secret');
            $hasFacebook = config('services.facebook.client_id') && config('services.facebook.client_secret');
            $hasTwitter = config('services.twitter.client_id') && config('services.twitter.client_secret');
            $hasSocialLogin = $hasGoogle || $hasFacebook || $hasTwitter;
            $providerCount = (int) $hasGoogle + (int) $hasFacebook + (int) $hasTwitter;
        @endphp

        @if ($hasSocialLogin)
            <div class="relative flex items-center py-4">
                <div class="flex-grow border-t border-zinc-200 dark:border-zinc-700"></div>
                <span class="mx-4 flex-shrink text-sm text-zinc-500">{{ __('global.or_continue_with') }}</span>
                <div class="flex-grow border-t border-zinc-200 dark:border-zinc-700"></div>
            </div>

            <div class="grid gap-3 {{ $providerCount === 1 ? 'grid-cols-1' : ($providerCount === 2 ? 'grid-cols-2' : 'grid-cols-3') }}">
                @if ($hasGoogle)
                    <flux:button href="{{ route('social.redirect', 'google') }}" variant="subtle" class="w-full">
                        <flux:icon.google class="size-4" />
                    </flux:button>
                @endif
                @if ($hasFacebook)
                    <flux:button href="{{ route('social.redirect', 'facebook') }}" variant="subtle" class="w-full">
                        <flux:icon.facebook class="size-4" />
                    </flux:button>
                @endif
                @if ($hasTwitter)
                    <flux:button href="{{ route('social.redirect', 'twitter') }}" variant="subtle" class="w-full">
                        <flux:icon.twitter class="size-4" />
                    </flux:button>
                @endif
            </div>
        @endif
    </form>
    <div class="space-x-1 text-center text-sm text-zinc-600 dark:text-zinc-400">
        <span> {{ __('global.already_have_an_account') }} </span>
        <flux:link :href="route('login')" wire:navigate> {{ __('global.sign_in') }} </flux:link>
    </div>
</div>
