<div class="flex flex-col gap-6">
    <x-auth-header title="{{ __('global.log_into_your_account') }}"
                   description="{{ __('global.log_into_your_account_text') }}"/>
    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')"/>
    <form wire:submit.prevent="login" class="flex flex-col gap-6">
        <!-- Email Address -->
        <flux:input wire:model="email" :label="__('global.email_address')" type="email" name="email" required
                    autocomplete="email" placeholder="email@example.com"/>
        <!-- Password -->
        <flux:input wire:model="password" :label="__('global.password')"
                    :type="$this->passwordVisible ? 'text' : 'password'" name="password" required
                    autocomplete="current-password" placeholder="Password">
            <x-slot name="iconTrailing">
                <flux:button size="sm" variant="subtle" icon="{{ $this->passwordVisible ? 'eye-slash' : 'eye' }}"
                             class="-mr-1" wire:click.prevent="$toggle('passwordVisible')"/>
            </x-slot>
        </flux:input>
        @if (Route::has('password.request'))
            <flux:link class="text-sm" :href="route('password.request')" wire:navigate>
                {{ __('global.forgot_password') }}
            </flux:link>
        @endif
        <!-- Remember Me -->
        <flux:checkbox wire:model="remember" :label="__('global.remember_me')"/>
        <div class="flex items-center justify-end">
            <flux:button variant="primary" type="submit" class="w-full">{{ __('global.log_in') }}</flux:button>
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
    @if (Route::has('register'))
        <div class="space-x-1 text-center text-sm text-zinc-600 dark:text-zinc-400">
            <span>{{ __('global.dont_have_an_account') }}</span>
            <flux:link :href="route('register')" wire:navigate>
                {{ __('global.sign_up') }}
            </flux:link>
        </div>
    @endif
</div>

