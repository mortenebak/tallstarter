<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />

        <title>@yield('code') | {{ config('app.name') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        @vite(['resources/css/app.css'])
        @fluxAppearance
    </head>
    <body class="min-h-screen bg-white antialiased dark:bg-linear-to-b dark:from-neutral-950 dark:to-neutral-900">
        <div class="flex min-h-svh flex-col items-center justify-center gap-6 p-6 text-center md:p-10">
            <a href="{{ route('home') }}" class="flex flex-col items-center gap-2 font-medium">
                <span class="flex h-9 w-9 items-center justify-center rounded-md">
                    <x-app-logo-icon class="size-9 fill-current text-black dark:text-white" />
                </span>
                <span class="sr-only">{{ config('app.name', 'Laravel') }}</span>
            </a>

            <div class="flex max-w-md flex-col items-center gap-2">
                <p class="text-7xl font-semibold tracking-tight text-zinc-900 dark:text-white">@yield('code')</p>
                <h1 class="text-xl font-medium text-zinc-900 dark:text-white">@yield('title')</h1>
                <p class="text-sm text-zinc-600 dark:text-zinc-400">@yield('message')</p>
            </div>

            <a
                href="{{ route('home') }}"
                class="inline-flex items-center justify-center rounded-lg bg-zinc-900 px-4 py-2 text-sm font-medium text-white hover:bg-zinc-700 dark:bg-white dark:text-zinc-900 dark:hover:bg-zinc-200"
            >
                {{ __('errors.back_to_home') }}
            </a>
        </div>
    </body>
</html>
