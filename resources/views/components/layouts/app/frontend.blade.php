<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    @include('partials.head')
</head>
<body class="min-h-screen bg-white dark:bg-zinc-800">
<flux:header container class="border-b border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
    <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left"/>

    <a href="{{ route('home') }}" class="ml-2 mr-5 flex items-center space-x-2 lg:ml-0">
        <x-app-logo class="size-8" href="#"></x-app-logo>
    </a>

    <flux:navbar class="-mb-px max-lg:hidden">
        <flux:navbar.item icon="layout-grid" href="{{ route('dashboard') }}" :current="request()->routeIs('dashboard')">
            Dashboard
        </flux:navbar.item>
    </flux:navbar>

    <flux:spacer/>
    @if (Route::has('login'))
        <nav class="flex items-center justify-end gap-4">
            @guest
                <flux:button href="{{ route('login') }}" variant="primary">
                    {{ __('global.log_in') }}
                </flux:button>
                @if (Route::has('register'))
                    <flux:button href="{{ route('register') }}">
                        {{ __('global.register') }}
                    </flux:button>
                @endif
            @endguest
        </nav>
    @endif
    {{--            <flux:navbar class="mr-1.5 space-x-0.5 py-0!">--}}
    {{--                <flux:tooltip content="Search" position="bottom">--}}
    {{--                    <flux:navbar.item class="!h-10 [&>div>svg]:size-5" icon="magnifying-glass" href="#" label="Search" />--}}
    {{--                </flux:tooltip>--}}
    {{--                <flux:tooltip content="Repository" position="bottom">--}}
    {{--                    <flux:navbar.item--}}
    {{--                        class="h-10 max-lg:hidden [&>div>svg]:size-5"--}}
    {{--                        icon="folder-git-2"--}}
    {{--                        href="https://github.com/laravel/livewire-starter-kit"--}}
    {{--                        target="_blank"--}}
    {{--                        label="Repository"--}}
    {{--                    />--}}
    {{--                </flux:tooltip>--}}
    {{--                <flux:tooltip content="Documentation" position="bottom">--}}
    {{--                    <flux:navbar.item--}}
    {{--                        class="h-10 max-lg:hidden [&>div>svg]:size-5"--}}
    {{--                        icon="book-open-text"--}}
    {{--                        href="https://laravel.com/docs/starter-kits"--}}
    {{--                        target="_blank"--}}
    {{--                        label="Documentation"--}}
    {{--                    />--}}
    {{--                </flux:tooltip>--}}
    {{--            </flux:navbar>--}}

    @auth
        @if (Session::has('admin_user_id'))
            <div class="py-2 flex items-center justify-center dark:text-white rounded mr-4">
                <form id="stop-impersonating" class="flex flex-col items-center gap-3" action="{{ route('impersonate.destroy') }}"
                      method="POST">
                    @csrf
                    @method('DELETE')
                    <flux:button type="submit" size="sm" variant="danger" form="stop-impersonating" class="!w-full !flex !flex-row cursor-pointer">
                        <div class="flex items-center gap-2">
                            <flux:icon.loader-circle class="animate-spin mr-2"/>
                            {{ __('users.stop_impersonating') }}
                        </div>
                    </flux:button>
                </form>
            </div>
        @endif
        <!-- Desktop User Menu -->
        <flux:dropdown position="top" align="end">
            <flux:profile
                class="cursor-pointer"
                :initials="auth()->user()->initials()"
            />

            <flux:menu>
                <flux:menu.radio.group>
                    <div class="p-0 text-sm font-normal">
                        <div class="flex items-center gap-2 px-1 py-1.5 text-left text-sm">
                                <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                    <span
                                        class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white"
                                    >
                                        {{ auth()->user()->initials() }}
                                    </span>
                                </span>

                            {{--                                <div class="grid flex-1 text-left text-sm leading-tight">--}}
                            {{--                                    <span class="truncate font-semibold">{{ auth()->user()->name }}</span>--}}
                            {{--                                    <span class="truncate text-xs">{{ auth()->user()->email }}</span>--}}
                            {{--                                </div>--}}
                        </div>
                    </div>
                </flux:menu.radio.group>

                @can('access dashboard')
                    <flux:menu.separator/>
                    <flux:menu.radio.group>
                        <flux:menu.item href="{{ route('admin.index') }}" icon="shield">
                            {{ __('global.admin_dashboard') }}
                        </flux:menu.item>
                    </flux:menu.radio.group>
                @endcan

                <flux:menu.separator/>

                @if (config('teams.enabled'))
                    <flux:menu.radio.group>
                        <flux:menu.item href="{{ route('teams.index') }}" icon="users">
                            {{ __('teams.title') }}
                        </flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator/>
                @endif

                <flux:menu.radio.group>
                    <flux:menu.item href="/settings/profile" icon="cog">
                        {{ __('settings.title') }}
                    </flux:menu.item>
                </flux:menu.radio.group>

                <flux:menu.separator/>

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                        {{ __('global.log_out') }}
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    @endauth
</flux:header>

<!-- Mobile Menu -->
<flux:sidebar stashable sticky class="lg:hidden border-r border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
    <flux:sidebar.toggle class="lg:hidden" icon="x-mark"/>

    <a href="{{ route('dashboard') }}" class="ml-1 flex items-center space-x-2">
        <x-app-logo class="size-8" href="#"></x-app-logo>
    </a>
    @auth
        <flux:navlist variant="outline">
            <flux:navlist.group heading="Platform">
                <flux:navlist.item icon="layout-grid" href="{{ route('dashboard') }}" :current="request()->routeIs('dashboard')">
                    Dashboard
                </flux:navlist.item>
            </flux:navlist.group>
        </flux:navlist>
    @endauth

    <flux:spacer/>

</flux:sidebar>

<flux:main container class="flex flex-col">
    <div class="">
        {{ $slot }}
    </div>

    @include('partials.footer')
</flux:main>







@fluxScripts
</body>
</html>
