<section class="w-full">
    <x-page-heading>
        <x-slot:title>View User</x-slot:title>
        <x-slot:subtitle>Viewing user {{ $user->name }}</x-slot:subtitle>
        <x-slot:buttons>
            @can('impersonate')
                @if(auth()->user()->id !== $user->id)
                    <form action="{{ route('impersonate.store', $user) }}" method="POST">
                        @csrf
                        <flux:button type="submit" size="sm">
                            {{ __('users.impersonate') }}
                        </flux:button>
                    </form>
                @endif
            @endcan
        </x-slot:buttons>
    </x-page-heading>


</section>
