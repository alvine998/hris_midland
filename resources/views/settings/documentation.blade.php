@extends('layouts.app')

@section('title', 'Documentation - ' . config('app.name'))

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
        <div>
            <p class="text-sm font-medium text-indigo-600 dark:text-indigo-400">Settings</p>
            <h2 class="mt-1 text-2xl font-bold text-gray-900 dark:text-white">Documentation</h2>
            <p class="mt-2 max-w-3xl text-sm text-gray-600 dark:text-gray-400">
                User guide for all HRIS modules in one page. Use the quick links to jump to the module you need.
            </p>
        </div>
        <a href="{{ route('settings.index') }}" class="inline-flex items-center justify-center rounded-lg bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600">
            Back to Settings
        </a>
    </div>

    <div class="grid gap-6 lg:grid-cols-[280px_1fr]">
        <aside class="h-fit rounded-2xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800 lg:sticky lg:top-24">
            <h3 class="px-2 text-sm font-semibold text-gray-900 dark:text-white">Modules</h3>
            <nav class="mt-3 max-h-[60vh] space-y-1 overflow-y-auto pr-1">
                @foreach ($modules as $module)
                    <a href="#{{ Str::slug($module['title']) }}" class="block rounded-lg px-2 py-2 text-sm text-gray-600 transition-colors hover:bg-gray-100 hover:text-gray-900 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-gray-100">
                        {{ $module['title'] }}
                    </a>
                @endforeach
            </nav>
        </aside>

        <div class="space-y-4">
            <section class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">How to Use This Guide</h3>
                <div class="mt-4 grid gap-3 md:grid-cols-3">
                    <div class="rounded-xl bg-gray-50 p-4 dark:bg-gray-900">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">Find</p>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Pick a module from the left menu.</p>
                    </div>
                    <div class="rounded-xl bg-gray-50 p-4 dark:bg-gray-900">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">Follow</p>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Use the listed workflow steps for daily operations.</p>
                    </div>
                    <div class="rounded-xl bg-gray-50 p-4 dark:bg-gray-900">
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">Review</p>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Check Activity Logs when you need to audit record changes.</p>
                    </div>
                </div>
            </section>

            @foreach ($modules as $module)
                <section id="{{ Str::slug($module['title']) }}" class="scroll-mt-24 rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $module['title'] }}</h3>
                            <p class="mt-2 text-sm leading-6 text-gray-600 dark:text-gray-400">{{ $module['description'] }}</p>
                        </div>
                        <span class="hidden rounded-full bg-indigo-50 px-3 py-1 text-xs font-medium text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300 sm:inline-flex">
                            Guide
                        </span>
                    </div>
                    <ol class="mt-5 space-y-3">
                        @foreach ($module['steps'] as $step)
                            <li class="flex gap-3 text-sm text-gray-700 dark:text-gray-300">
                                <span class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-gray-100 text-xs font-semibold text-gray-700 dark:bg-gray-700 dark:text-gray-200">{{ $loop->iteration }}</span>
                                <span class="leading-6">{{ $step }}</span>
                            </li>
                        @endforeach
                    </ol>
                </section>
            @endforeach
        </div>
    </div>
</div>
@endsection
