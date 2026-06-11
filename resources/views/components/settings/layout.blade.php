<div class="flex items-start max-md:flex-col">
    <div class="mr-10 w-full pb-4 md:w-[220px]">
        <flux:navlist>
            <flux:navlist.item href="{{ route('settings.profile') }}">{{ __('settings.profile') }}</flux:navlist.item>
            <flux:navlist.item href="{{ route('settings.password') }}">{{ __('settings.password') }}</flux:navlist.item>
            <flux:navlist.item href="{{ route('settings.two-factor') }}">{{ __('global.two_factor_authentication') }}</flux:navlist.item>
            <flux:navlist.item href="{{ route('settings.appearance') }}">{{ __('settings.appearance') }}</flux:navlist.item>
            <flux:navlist.item href="{{ route('settings.locale') }}">{{ __('users.locale') }}</flux:navlist.item>
        </flux:navlist>
    </div>

    <flux:separator class="md:hidden" />

    <div class="flex-1 self-stretch max-md:pt-6">
        <flux:heading>{{ $heading ?? '' }}</flux:heading>
        <flux:subheading>{{ $subheading ?? '' }}</flux:subheading>

        <div class="mt-5 w-full max-w-lg">
            {{ $slot }}
        </div>
    </div>
</div>
