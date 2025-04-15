<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    @include('partials.head')
</head>
<body class="min-h-screen bg-white dark:bg-zinc-800">
<flux:sidebar sticky stashable class="border-r border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900 lg:dark:bg-zinc-900/50">
    <flux:sidebar.toggle class="lg:hidden" icon="x-mark"/>

    <a href="{{ route('home') }}" class="mr-5 flex items-center space-x-2">
        <x-app-logo class="size-8"></x-app-logo>
    </a>

    <div>
        <flux:button href="{{ route('home') }}" icon="arrow-left" size="sm">
            {{ __('global.go_to_frontend') }}
        </flux:button>
    </div>

    <flux:navlist variant="outline">
        <flux:navlist.group heading="Platform" class="grid">
            <flux:navlist.item icon="home" :href="route('admin.index')" :current="request()->routeIs('admin.index')">Dashboard</flux:navlist.item>
        </flux:navlist.group>
        {{--        <flux:navlist.group heading="Monetization" class="grid">--}}
        {{--            <flux:navlist.item icon="arrow-right" :href="route('dashboard')" :current="request()->routeIs('dashboard')">Overview</flux:navlist.item>--}}
        {{--            <flux:navlist.item icon="arrow-right" :href="route('dashboard')" :current="request()->routeIs('dashboard')">Subscriptions</flux:navlist.item>--}}
        {{--            <flux:navlist.item icon="arrow-right" :href="route('dashboard')" :current="request()->routeIs('dashboard')">Invoices</flux:navlist.item>--}}
        {{--        </flux:navlist.group>--}}
        @canany(['view users', 'view roles', 'view permissions'])
            <flux:navlist.group heading="Users" class="grid">
                @can('view users')
                    <flux:navlist.item icon="user" :href="route('admin.users.index')" :current="request()->routeIs('admin.users.*')">
                        {{ __('users.title') }}
                    </flux:navlist.item>
                @endcan
                @can('view roles')
                    <flux:navlist.item icon="shield-user" :href="route('admin.roles.index')" :current="request()->routeIs('admin.roles.*')">
                        {{ __('roles.title') }}
                    </flux:navlist.item>
                @endcan
                @can('view permissions')
                    <flux:navlist.item icon="shield-check" :href="route('admin.permissions.index')" :current="request()->routeIs('admin.permissions.*')">
                        {{ __('permissions.title') }}
                    </flux:navlist.item>
                @endcan
            </flux:navlist.group>
        @endcanany
    </flux:navlist>

    <flux:spacer/>

    @if (Session::has('admin_user_id'))
        <div class="py-2 flex items-center justify-center bg-zinc-100 dark:bg-zinc-600 dark:text-white mb-6 rounded">
            <form id="stop-impersonating" class="flex flex-col items-center gap-3" action="{{ route('impersonate.destroy') }}"
                  method="POST">
                @csrf
                @method('DELETE')
                <p class="text-xs">
                    {{ __('users.you_are_impersonating') }}:
                    <strong>{{ auth()->user()->name }}</strong>
                </p>
                <flux:button type="submit" size="sm" variant="danger" form="stop-impersonating" class="!w-full !flex !flex-row">
                    <div>
                        {{ __('users.stop_impersonating') }}
                    </div>
                </flux:button>
            </form>
        </div>
    @endif

    <flux:navlist variant="outline">
        {{--                <flux:navlist.item icon="folder-git-2" href="https://github.com/laravel/livewire-starter-kit" target="_blank">--}}
        {{--                    Repository--}}
        {{--                </flux:navlist.item>--}}

        {{--                <flux:navlist.item icon="book-open-text" href="https://laravel.com/docs/starter-kits" target="_blank">--}}
        {{--                    Documentation--}}
        {{--                </flux:navlist.item>--}}
    </flux:navlist>

    @auth
        <!-- Desktop User Menu -->
        <flux:dropdown position="bottom" align="start">
            <flux:profile
                :name="auth()->user()->name"
                :initials="auth()->user()->initials()"
                icon-trailing="chevrons-up-down"
            />

            <flux:menu class="w-[220px]">
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

                            <div class="grid flex-1 text-left text-sm leading-tight">
                                <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                            </div>
                        </div>
                    </div>
                </flux:menu.radio.group>

                <flux:menu.separator/>

                <flux:menu.radio.group>
                    <flux:menu.item href="/settings/profile" icon="cog">{{ __('global.settings') }}</flux:menu.item>
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
</flux:sidebar>

<!-- Mobile User Menu -->
<flux:header class="lg:hidden">
    <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left"/>

    <flux:spacer/>

    @auth
        <flux:dropdown position="top" align="end">
            <flux:profile
                :initials="auth()->user()->initials()"
                icon-trailing="chevron-down"
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

                            <div class="grid flex-1 text-left text-sm leading-tight">
                                <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                            </div>
                        </div>
                    </div>
                </flux:menu.radio.group>

                <flux:menu.separator/>

                <flux:menu.radio.group>
                    <flux:menu.item href="/settings/profile" icon="cog">
                        {{ __('global.settings') }}
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

{{ $slot }}

@fluxScripts
<x-livewire-alert::scripts />
<x-livewire-alert::flash />

</body>
</html>
