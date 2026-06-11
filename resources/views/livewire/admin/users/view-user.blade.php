<section class="w-full">
    <x-page-heading>
        <x-slot:title>{{ __('users.view_user') }}</x-slot:title>
        <x-slot:subtitle>{{ __('users.viewing_user', ['name' => $user->name]) }}</x-slot:subtitle>
    </x-page-heading>


</section>
