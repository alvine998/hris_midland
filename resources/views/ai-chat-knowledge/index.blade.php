@extends('layouts.app')

@section('title', 'AI Knowledge Base - ' . config('app.name'))

@section('content')
<div x-data="{ open: false, edit: false, item: {}, deleteId: null }" class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">AI Knowledge Base</h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Manage HR policies and knowledge articles used by the AI assistant.</p>
        </div>
        <button @click="open = true; edit = false; item = { is_active: true }" class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">Add Article</button>
    </div>

    @session('success')<div class="rounded-xl border border-green-200 bg-green-50 p-4 text-sm text-green-700 dark:border-green-800 dark:bg-green-900/30 dark:text-green-300">{{ $value }}</div>@endsession
    <x-list-search :action="route('ai-chat.knowledge.index')" placeholder="Search title or category" />

    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <table class="w-full text-sm">
            <thead><tr class="border-b border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-900">
                <th class="px-6 py-4 text-left font-semibold text-gray-900 dark:text-white">Category</th>
                <th class="px-6 py-4 text-left font-semibold text-gray-900 dark:text-white">Title</th>
                <th class="px-6 py-4 text-left font-semibold text-gray-900 dark:text-white">Content Preview</th>
                <th class="px-6 py-4 text-center font-semibold text-gray-900 dark:text-white">Active</th>
                <th class="px-6 py-4 text-right font-semibold text-gray-900 dark:text-white">Actions</th>
            </tr></thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse ($articles as $article)
                    <tr>
                        <td class="px-6 py-4 text-gray-600 dark:text-gray-400">{{ $article->category }}</td>
                        <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $article->title }}</td>
                        <td class="px-6 py-4 text-gray-500 dark:text-gray-400 max-w-xs truncate">{{ Str::limit($article->content, 80) }}</td>
                        <td class="px-6 py-4 text-center">
                            @if($article->is_active)
                                <span class="inline-flex items-center rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-700 dark:bg-green-900/30 dark:text-green-400">Active</span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-500 dark:bg-gray-700 dark:text-gray-400">Inactive</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <button @click="open = true; edit = true; item = { id: {{ $article->id }}, category: @js($article->category), title: @js($article->title), content: @js($article->content), is_active: {{ $article->is_active ? 'true' : 'false' }} }" class="mr-3 font-medium text-indigo-600 dark:text-indigo-400">Edit</button>
                            <button @click="deleteId = {{ $article->id }}" class="font-medium text-red-600 dark:text-red-400">Delete</button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-6 py-12 text-center text-gray-500">No articles yet. Add your first HR knowledge base article.</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($articles->hasPages())<div class="border-t border-gray-200 px-6 py-4 dark:border-gray-700">{{ $articles->links() }}</div>@endif
    </div>

    <div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-gray-900/50" @click="open = false"></div>
        <div class="relative w-full max-w-2xl rounded-2xl border border-gray-200 bg-white p-6 shadow-xl dark:border-gray-700 dark:bg-gray-800">
            <h3 class="mb-4 text-lg font-semibold text-gray-900 dark:text-white" x-text="edit ? 'Edit Article' : 'Add Article'"></h3>
            <form method="POST" :action="edit ? `/ai-chat/knowledge/${item.id}` : '{{ route('ai-chat.knowledge.store') }}'">
                @csrf
                <input type="hidden" name="_method" :value="edit ? 'PUT' : 'POST'">
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Category</label>
                            <input name="category" x-model="item.category" required placeholder="e.g. Leave Policy" list="categories" class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-600 dark:bg-gray-700">
                            <datalist id="categories">
                                @foreach($categories as $cat)<option value="{{ $cat }}">@endforeach
                            </datalist>
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Title</label>
                            <input name="title" x-model="item.title" required placeholder="Article title" class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:border-gray-600 dark:bg-gray-700">
                        </div>
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Content</label>
                        <textarea name="content" x-model="item.content" required rows="10" placeholder="Write the HR policy or knowledge content here..." class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm dark:border-gray-600 dark:bg-gray-700"></textarea>
                    </div>
                    <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" x-model="item.is_active" class="rounded border-gray-300 text-indigo-600">
                        Active (used by AI)
                    </label>
                </div>
                <div class="mt-6 flex justify-end gap-3"><button type="button" @click="open = false" class="rounded-lg bg-gray-100 px-4 py-2 text-sm dark:bg-gray-700">Cancel</button><button class="rounded-lg bg-indigo-600 px-4 py-2 text-sm text-white" x-text="edit ? 'Update' : 'Save'"></button></div>
            </form>
        </div>
    </div>

    <div x-show="deleteId" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-gray-900/50" @click="deleteId = null"></div>
        <div class="relative w-full max-w-md rounded-2xl border border-gray-200 bg-white p-6 text-center shadow-xl dark:border-gray-700 dark:bg-gray-800">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Confirm Delete</h3>
            <form :action="`/ai-chat/knowledge/${deleteId}`" method="POST" class="mt-6 flex justify-center gap-3">@csrf @method('DELETE')<button type="button" @click="deleteId = null" class="rounded-lg bg-gray-100 px-4 py-2 text-sm dark:bg-gray-700">Cancel</button><button class="rounded-lg bg-red-600 px-4 py-2 text-sm text-white">Delete</button></form>
        </div>
    </div>
</div>
@endsection
