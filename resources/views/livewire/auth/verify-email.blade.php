<div class="mt-4 flex flex-col gap-6">
    <flux:text class="text-center">
        {{ __('global.please_verify_your_email_address') }}
    </flux:text>

    @if (session('status') == 'verification-link-sent')
        <flux:text class="text-center font-medium !dark:text-green-400 !text-green-600">
            {{ __('global.verification_link_sent') }}
        </flux:text>
    @endif

    <div class="flex flex-col items-center justify-between space-y-3">
        <flux:button wire:click="sendVerification" variant="primary" class="w-full">
            {{ __('global.resend_verification_email') }}
        </flux:button>

        <flux:link class="text-sm cursor-pointer" wire:click="logout">
            {{ __('global.log_out') }}
        </flux:link>
    </div>
</div>
