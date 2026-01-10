<section class="w-full">
    <x-page-heading>
        <x-slot:title>{{ __('settings.title') }}</x-slot:title>
        <x-slot:subtitle>{{ __('settings.subtitle') }}</x-slot:subtitle>
    </x-page-heading>

    <x-settings.layout :heading="__('Two-Factor Authentication')" :subheading="__('Add additional security to your account using two-factor authentication.')">
        <div class="mt-6 space-y-6">
            @if (!auth()->user()->hasTwoFactorEnabled())
                {{-- Enable 2FA --}}
                @if (!$showingQrCode)
                    <div class="space-y-4">
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            {{ __('When two-factor authentication is enabled, you will be prompted for a secure, random token during authentication. You may retrieve this token from your phone\'s Google Authenticator application.') }}
                        </p>
                        <flux:button wire:click="enableTwoFactorAuthentication" variant="primary">
                            {{ __('Enable Two-Factor Authentication') }}
                        </flux:button>
                    </div>
                @else
                    {{-- Show QR Code for Setup --}}
                    <div class="space-y-4">
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            {{ __('Two-factor authentication is now enabled. Scan the following QR code using your phone\'s authenticator application or enter the setup key.') }}
                        </p>

                        <div class="flex justify-center">
                            <img src="{{ $this->qrCodeUrl }}" alt="QR Code" class="border border-gray-300 dark:border-gray-700 rounded-lg">
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg">
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Setup Key:') }}</p>
                            <code class="text-sm font-mono text-gray-900 dark:text-gray-100">{{ $this->manualEntrySecret }}</code>
                        </div>

                        <form wire:submit="confirmTwoFactorAuthentication" class="space-y-4">
                            <flux:input
                                wire:model="code"
                                id="two_factor_code"
                                label="{{ __('Enter the code from your authenticator app') }}"
                                type="text"
                                name="code"
                                required
                                placeholder="000000"
                                maxlength="6"
                            />
                            <div class="flex gap-4">
                                <flux:button type="submit" variant="primary">
                                    {{ __('Confirm') }}
                                </flux:button>
                                <flux:button type="button" wire:click="disableTwoFactorAuthentication" variant="ghost">
                                    {{ __('Cancel') }}
                                </flux:button>
                            </div>
                        </form>
                    </div>
                @endif
            @else
                {{-- 2FA is enabled --}}
                <div class="space-y-4">
                    <div class="flex items-center gap-2">
                        <flux:badge color="green" size="sm">
                            {{ __('Enabled') }}
                        </flux:badge>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            {{ __('Two-factor authentication is currently enabled.') }}
                        </p>
                    </div>

                    @if ($showingRecoveryCodes)
                        <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                            <p class="text-sm font-medium text-yellow-800 dark:text-yellow-300 mb-3">
                                {{ __('Store these recovery codes in a secure password manager. They can be used to recover access to your account if your two-factor authentication device is lost.') }}
                            </p>
                            <div class="grid grid-cols-2 gap-2 font-mono text-sm">
                                @foreach ($recoveryCodes as $code)
                                    <div class="bg-white dark:bg-gray-800 px-3 py-2 rounded border border-gray-200 dark:border-gray-700">
                                        {{ $code }}
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="flex flex-wrap gap-3">
                        @if (!$showingRecoveryCodes && !auth()->user()->two_factor_recovery_codes_viewed_at)
                            <flux:button wire:click="showRecoveryCodes" variant="outline">
                                {{ __('Show Recovery Codes') }}
                            </flux:button>
                        @endif

                        <flux:button wire:click="regenerateRecoveryCodes" variant="outline">
                            {{ __('Regenerate Recovery Codes') }}
                        </flux:button>

                        <flux:button wire:click="disableTwoFactorAuthentication" variant="danger">
                            {{ __('Disable Two-Factor Authentication') }}
                        </flux:button>
                    </div>
                </div>
            @endif
        </div>
    </x-settings.layout>
</section>
