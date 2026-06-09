@props([
    'action',
    'placeholder' => 'Search...',
])

<form method="GET" action="{{ $action }}" class="mb-4 rounded-2xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
    <div class="flex flex-col gap-3 sm:flex-row">
        <input
            type="search"
            name="search"
            value="{{ request('search') }}"
            placeholder="{{ $placeholder }}"
            class="min-w-0 flex-1 rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm text-gray-900 placeholder:text-gray-400 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-900 dark:text-white"
        >
        <div class="flex gap-2">
            <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-indigo-700">
                Search
            </button>
            @if (request()->filled('search'))
                <a href="{{ $action }}" class="inline-flex items-center justify-center rounded-xl border border-gray-300 px-4 py-2.5 text-sm font-semibold text-gray-700 transition-colors hover:bg-gray-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700">
                    Reset
                </a>
            @endif
        </div>
    </div>
</form>
