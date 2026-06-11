<section class="w-full">
    <x-page-heading>
        <x-slot:title>{{ __('settings.title') }}</x-slot:title>
        <x-slot:subtitle>{{ __('settings.subtitle') }}</x-slot:subtitle>
    </x-page-heading>

    <x-settings.layout :heading="__('global.two_factor_authentication')" :subheading="__('settings.two_factor_description')">
        <div class="mt-6 space-y-6">
            @if (!auth()->user()->hasTwoFactorEnabled())
                {{-- Enable 2FA --}}
                @if (!$showingQrCode)
                    <div class="space-y-4">
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            {{ __('settings.two_factor_explanation') }}
                        </p>
                        <flux:button wire:click="enableTwoFactorAuthentication" variant="primary">
                            {{ __('settings.enable_two_factor') }}
                        </flux:button>
                    </div>
                @else
                    {{-- Show QR Code for Setup --}}
                    <div class="space-y-4">
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            {{ __('settings.two_factor_scan_qr') }}
                        </p>

                        <div class="flex justify-center">
                            <img src="{{ $this->qrCodeUrl }}" alt="QR Code" class="border border-gray-300 dark:border-gray-700 rounded-lg">
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg">
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('settings.setup_key') }}</p>
                            <code class="text-sm font-mono text-gray-900 dark:text-gray-100">{{ $this->manualEntrySecret }}</code>
                        </div>

                        <form wire:submit="confirmTwoFactorAuthentication" class="space-y-4">
                            <flux:input
                                wire:model="code"
                                id="two_factor_code"
                                label="{{ __('settings.enter_authenticator_code') }}"
                                type="text"
                                name="code"
                                required
                                placeholder="000000"
                                maxlength="6"
                            />
                            <div class="flex gap-4">
                                <flux:button type="submit" variant="primary">
                                    {{ __('global.confirm') }}
                                </flux:button>
                                <flux:button type="button" wire:click="disableTwoFactorAuthentication" variant="ghost">
                                    {{ __('global.cancel') }}
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
                            {{ __('settings.enabled') }}
                        </flux:badge>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            {{ __('settings.two_factor_currently_enabled') }}
                        </p>
                    </div>

                    @if ($showingRecoveryCodes)
                        <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                            <p class="text-sm font-medium text-yellow-800 dark:text-yellow-300 mb-3">
                                {{ __('settings.store_recovery_codes') }}
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
                        @if (!$showingRecoveryCodes && !auth()->user()->hasViewedRecoveryCodes())
                            <flux:button wire:click="showRecoveryCodes" variant="outline">
                                {{ __('settings.show_recovery_codes') }}
                            </flux:button>
                        @endif

                        <flux:button wire:click="regenerateRecoveryCodes" variant="outline">
                            {{ __('settings.regenerate_recovery_codes') }}
                        </flux:button>

                        <flux:button wire:click="disableTwoFactorAuthentication" variant="danger">
                            {{ __('settings.disable_two_factor') }}
                        </flux:button>
                    </div>
                </div>
            @endif
        </div>
    </x-settings.layout>
</section>
