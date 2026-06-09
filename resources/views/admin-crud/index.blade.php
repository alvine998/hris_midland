@extends('layouts.app')

@section('title', $config['title'] . ' - ' . config('app.name'))

@section('content')
<div x-data="{ open: false, edit: false, item: {}, deleteId: null }" class="space-y-6">
    <div class="flex items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $config['title'] }}</h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Manage {{ Str::lower($config['title']) }}.</p>
        </div>
        <button type="button" @click="open = true; edit = false; item = {}" class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">
            Add New
        </button>
    </div>

    @session('success')
    <div class="rounded-xl border border-green-200 bg-green-50 p-4 text-sm text-green-700 dark:border-green-800 dark:bg-green-900/30 dark:text-green-300">{{ $value }}</div>
    @endsession

    <x-list-search :action="route('admin-crud.index', $resource)" placeholder="Search {{ Str::lower($config['title']) }}" />

    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-900">
                        @foreach ($config['columns'] as $label)
                            <th class="px-6 py-4 text-left font-semibold text-gray-900 dark:text-white">{{ $label }}</th>
                        @endforeach
                        <th class="px-6 py-4 text-right font-semibold text-gray-900 dark:text-white">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse ($items as $item)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            @foreach ($config['columns'] as $key => $label)
                                @php($cell = \App\Http\Controllers\AdminCrudController::value($item, $key))
                                <td class="px-6 py-4 text-gray-600 dark:text-gray-400">
                                    @if (is_array($cell))
                                        {{ implode(', ', $cell) }}
                                    @elseif (is_bool($cell))
                                        {{ $cell ? 'Yes' : 'No' }}
                                    @else
                                        {{ Str::limit((string) ($cell ?? '-'), 80) }}
                                    @endif
                                </td>
                            @endforeach
                            <td class="px-6 py-4 text-right">
                                <button type="button" @click="open = true; edit = true; item = @js(\App\Http\Controllers\AdminCrudController::jsItem($item, $config['fields']))" class="mr-3 font-medium text-indigo-600 dark:text-indigo-400">Edit</button>
                                <button type="button" @click="deleteId = {{ $item->id }}" class="font-medium text-red-600 dark:text-red-400">Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($config['columns']) + 1 }}" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">No records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($items->hasPages())
            <div class="border-t border-gray-200 px-6 py-4 dark:border-gray-700">{{ $items->links() }}</div>
        @endif
    </div>

    <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-gray-900/50" @click="open = false"></div>
        <div class="relative max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-2xl border border-gray-200 bg-white p-6 shadow-xl dark:border-gray-700 dark:bg-gray-800">
            <h3 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white" x-text="edit ? 'Edit {{ $config['singular'] }}' : 'Add {{ $config['singular'] }}'"></h3>
            <form method="POST" :action="edit ? `{{ url('/admin-crud/'.$resource) }}/${item.id}` : '{{ route('admin-crud.store', $resource) }}'">
                @csrf
                <input type="hidden" name="_method" :value="edit ? 'PUT' : 'POST'">
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    @foreach ($config['fields'] as $field)
                        @php($type = $field['type'] ?? 'text')
                        <label class="{{ in_array($type, ['textarea', 'json', 'multiselect']) ? 'md:col-span-2' : '' }}">
                            <span class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ $field['label'] }}</span>
                            @if ($type === 'textarea' || $type === 'json')
                                <textarea name="{{ $field['name'] }}" x-model="item.{{ $field['name'] }}" rows="4" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-gray-900 dark:border-gray-600 dark:bg-gray-700 dark:text-white"></textarea>
                            @elseif ($type === 'select')
                                <select name="{{ $field['name'] }}" x-model="item.{{ $field['name'] }}" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-gray-900 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                    <option value="">Select...</option>
                                    @foreach ($options[$field['options']] ?? [] as $option)
                                        <option value="{{ $option->id }}">{{ $option->name }}</option>
                                    @endforeach
                                </select>
                            @elseif ($type === 'multiselect')
                                <select name="{{ $field['name'] }}[]" x-model="item.{{ $field['name'] }}" multiple class="min-h-32 w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-gray-900 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                    @foreach ($options[$field['options']] ?? [] as $option)
                                        <option value="{{ $option->id }}">{{ $option->name }}</option>
                                    @endforeach
                                </select>
                            @elseif ($type === 'select_static')
                                <select name="{{ $field['name'] }}" x-model="item.{{ $field['name'] }}" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-gray-900 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                    @foreach ($field['choices'] as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            @elseif ($type === 'checkbox')
                                <input type="hidden" name="{{ $field['name'] }}" value="0">
                                <input type="checkbox" name="{{ $field['name'] }}" value="1" x-model="item.{{ $field['name'] }}" class="h-5 w-5 rounded border-gray-300 text-indigo-600">
                            @else
                                <input type="{{ $type }}" name="{{ $field['name'] }}" x-model="item.{{ $field['name'] }}" class="w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-gray-900 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                            @endif
                        </label>
                    @endforeach
                </div>
                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" @click="open = false" class="rounded-lg bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300">Cancel</button>
                    <button class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700" x-text="edit ? 'Update' : 'Save'"></button>
                </div>
            </form>
        </div>
    </div>

    <div x-show="deleteId" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-gray-900/50" @click="deleteId = null"></div>
        <div class="relative w-full max-w-md rounded-2xl border border-gray-200 bg-white p-6 text-center shadow-xl dark:border-gray-700 dark:bg-gray-800">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Confirm Delete</h3>
            <form :action="`{{ url('/admin-crud/'.$resource) }}/${deleteId}`" method="POST" class="mt-6 flex justify-center gap-3">
                @csrf
                @method('DELETE')
                <button type="button" @click="deleteId = null" class="rounded-lg bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700 dark:bg-gray-700 dark:text-gray-300">Cancel</button>
                <button class="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700">Delete</button>
            </form>
        </div>
    </div>
</div>
@endsection
