<x-layouts.app.sidebar>
    <flux:main>
        @include('partials.impersonation-banner')

        {{ $slot }}
    </flux:main>
</x-layouts.app.sidebar>
