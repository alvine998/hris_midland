@extends('layouts.app')

@section('title', 'Holidays - ' . config('app.name'))

@section('content')
<div x-data="{ open: false, edit: false, item: {}, deleteId: null }" class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Holidays</h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Manage company and national holidays.</p>
        </div>
        <button @click="open = true; edit = false; item = { type: 'national' }" class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">Add New</button>
    </div>

    @session('success')<div class="rounded-xl border border-green-200 bg-green-50 p-4 text-sm text-green-700 dark:border-green-800 dark:bg-green-900/30 dark:text-green-300">{{ $value }}</div>@endsession
    <x-list-search :action="route('leave-management.holidays.index')" placeholder="Search holiday name or type" />

    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <table class="w-full text-sm">
            <thead><tr class="border-b border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-900"><th class="px-6 py-4 text-left font-semibold text-gray-900 dark:text-white">Name</th><th class="px-6 py-4 text-left font-semibold text-gray-900 dark:text-white">Dates</th><th class="px-6 py-4 text-left font-semibold text-gray-900 dark:text-white">Type</th><th class="px-6 py-4 text-right font-semibold text-gray-900 dark:text-white">Actions</th></tr></thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse ($holidays as $holiday)
                    <tr>
                        <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $holiday->name }}</td>
                        <td class="px-6 py-4 text-gray-600 dark:text-gray-400">{{ $holiday->start_date->format('d M Y') }} - {{ $holiday->end_date->format('d M Y') }}</td>
                        <td class="px-6 py-4 text-gray-600 dark:text-gray-400">{{ ucfirst($holiday->type) }}</td>
                        <td class="px-6 py-4 text-right"><button @click="open = true; edit = true; item = { id: {{ $holiday->id }}, name: @js($holiday->name), start_date: @js($holiday->start_date->format('Y-m-d')), end_date: @js($holiday->end_date->format('Y-m-d')), type: @js($holiday->type) }" class="mr-3 font-medium text-indigo-600 dark:text-indigo-400">Edit</button><button @click="deleteId = {{ $holiday->id }}" class="font-medium text-red-600 dark:text-red-400">Delete</button></td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-6 py-12 text-center text-gray-500">No holidays found.</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($holidays->hasPages())<div class="border-t border-gray-200 px-6 py-4 dark:border-gray-700">{{ $holidays->links() }}</div>@endif
    </div>

    <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-gray-900/50" @click="open = false"></div>
        <div class="relative w-full max-w-md rounded-2xl border border-gray-200 bg-white p-6 shadow-xl dark:border-gray-700 dark:bg-gray-800">
            <h3 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white" x-text="edit ? 'Edit Holiday' : 'Add Holiday'"></h3>
            <form method="POST" :action="edit ? `/leave-management/holidays/${item.id}` : '{{ route('leave-management.holidays.store') }}'">
                @csrf
                <input type="hidden" name="_method" :value="edit ? 'PUT' : 'POST'">
                <div class="space-y-4">
                    <input name="name" x-model="item.name" required placeholder="Name" class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-600 dark:bg-gray-700">
                    <input type="date" name="start_date" x-model="item.start_date" required class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-600 dark:bg-gray-700">
                    <input type="date" name="end_date" x-model="item.end_date" required class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-600 dark:bg-gray-700">
                    <select name="type" x-model="item.type" required class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-600 dark:bg-gray-700"><option value="national">National</option><option value="company">Company</option></select>
                </div>
                <div class="mt-6 flex justify-end gap-3"><button type="button" @click="open = false" class="rounded-lg bg-gray-100 px-4 py-2 text-sm dark:bg-gray-700">Cancel</button><button class="rounded-lg bg-indigo-600 px-4 py-2 text-sm text-white" x-text="edit ? 'Update' : 'Save'"></button></div>
            </form>
        </div>
    </div>

    <div x-show="deleteId" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-gray-900/50" @click="deleteId = null"></div>
        <div class="relative w-full max-w-md rounded-2xl border border-gray-200 bg-white p-6 text-center shadow-xl dark:border-gray-700 dark:bg-gray-800">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Confirm Delete</h3>
            <form :action="`/leave-management/holidays/${deleteId}`" method="POST" class="mt-6 flex justify-center gap-3">@csrf @method('DELETE')<button type="button" @click="deleteId = null" class="rounded-lg bg-gray-100 px-4 py-2 text-sm dark:bg-gray-700">Cancel</button><button class="rounded-lg bg-red-600 px-4 py-2 text-sm text-white">Delete</button></form>
        </div>
    </div>
</div>
@endsection
