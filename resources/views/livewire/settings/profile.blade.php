<section class="w-full">
    <x-page-heading>
        <x-slot:title>{{ __('settings.title') }}</x-slot:title>
        <x-slot:subtitle>{{ __('settings.subtitle') }}</x-slot:subtitle>
    </x-page-heading>

    <x-settings.layout :heading="__('settings.profile')" :subheading="__('settings.profile_description')">
        <div class="mt-6 flex items-center gap-4">
            <flux:avatar size="xl" :src="auth()->user()->avatarUrl()" :initials="auth()->user()->initials()" />

            <div class="flex flex-col gap-2">
                <flux:input
                    type="file"
                    wire:model="avatar"
                    :label="__('settings.avatar')"
                    accept="image/jpeg,image/png,image/webp"
                />

                <flux:text size="sm" wire:loading wire:target="avatar">
                    {{ __('settings.avatar_uploading') }}
                </flux:text>

                <div class="flex items-center gap-4">
                    @if (auth()->user()->avatar)
                        <flux:button
                            variant="subtle"
                            size="sm"
                            wire:click="removeAvatar"
                            wire:loading.attr="disabled"
                        >
                            {{ __('settings.remove_avatar') }}
                        </flux:button>
                    @endif

                    <x-action-message on="avatar-updated">
                        {{ __('settings.avatar_updated') }}
                    </x-action-message>
                </div>
            </div>
        </div>

        <form wire:submit="updateProfileInformation" class="my-6 w-full space-y-6">
            <flux:input wire:model="name" :label="__('users.name')" type="text" name="name" required autofocus autocomplete="name" />

            <div>
                <flux:input wire:model="email" :label="__('users.email')" type="email" name="email" required autocomplete="email" />

                @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail &&! auth()->user()->hasVerifiedEmail())
                    <div>
                        <flux:text class="mt-4">
                            {{ __('settings.your_email_is_unverified') }}

                            <flux:link class="text-sm cursor-pointer" wire:click.prevent="resendVerificationNotification">
                                {{ __('settings.click_here_to_request_another') }}
                            </flux:link>
                        </flux:text>

                        @if (session('status') === 'verification-link-sent')
                            <flux:text class="mt-2 font-medium !dark:text-green-400 !text-green-600">
                                {{ __('settings.verification_link_sent') }}
                            </flux:text>
                        @endif
                    </div>
                @endif
            </div>

            <div class="flex items-center gap-4">
                <div class="flex items-center justify-end">
                    <flux:button variant="primary" type="submit" class="w-full">{{ __('global.save') }}</flux:button>
                </div>

                <x-action-message class="me-3" on="profile-updated">
                    {{ __('global.saved') }}
                </x-action-message>
            </div>
        </form>

        <livewire:settings.delete-user-form />
    </x-settings.layout>
</section>
