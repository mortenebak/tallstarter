@props([
'sortable' => null,
'direction' => null
])

<th scope="col" {{ $attributes->merge(['class' => 'py-3 px-6 text-left dark:bg-zinc-700 border-b dark:dark:border-zinc-500'])->only('class') }}
>
    @unless ($sortable)
        <span class="text-left text-xs leading-4 font-medium uppercase tracking-wider">{{ $slot }}</span>
    @else
        <button
            {{ $attributes->except('class') }} class="flex items-center space-x-1 text-left text-xs leading-4 font-medium">

            <span> {{ $slot }}</span>

            <span>
                @if ($direction === 'asc')
                    <svg viewBox="0 0 8 6" width="8" height="6" fill="none"
                         class="absolute inset-y-0 right-3.5 h-full pointer-events-none">
                      <path d="M7 1.5l-3 3-3-3" stroke="#9CA3AF" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round"></path>
                    </svg>
                @elseif ($direction === 'desc')
                    DESC
                @else

                @endif
            </span>

        </button>

    @endif
</th>
