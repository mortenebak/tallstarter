@if (Session::has('admin_user_id'))
    <div class="mb-6 flex w-full flex-wrap items-center justify-center gap-x-4 gap-y-2 rounded-lg border border-amber-300 bg-amber-100 px-4 py-2 text-sm text-amber-900 dark:border-amber-700 dark:bg-amber-950 dark:text-amber-100">
        <span>
            {{ __('users.you_are_impersonating') }}
            <strong>{{ auth()->user()->name }}</strong>
        </span>
        <form action="{{ route('impersonate.destroy') }}" method="POST">
            @csrf
            @method('DELETE')
            <flux:button type="submit" size="sm" variant="danger">
                {{ __('users.stop_impersonating') }}
            </flux:button>
        </form>
    </div>
@endif
