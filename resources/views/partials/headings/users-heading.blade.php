<div class="relative mb-6 w-full">
    <div class="flex justify-between items-center">
        <div>
            <flux:heading size="xl" level="1">{{ __('users.title') }}</flux:heading>
            <flux:subheading size="lg" class="mb-6">{{ __('users.title_description') }}</flux:subheading>
        </div>
        <div>
            <flux:button href="/users/create" variant="primary" icon="plus">{{ __('users.create_user') }}</flux:button>
        </div>
    </div>
    <flux:separator variant="subtle" />
</div>
