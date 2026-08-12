@extends('admin.layouts.admin')
@section('title', 'FAQs')
@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between"><div><h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">FAQs</h2><p class="text-sm text-gray-500 dark:text-gray-400">Manage frequently asked questions.</p></div><a href="{{ route('admin.faqs.create') }}" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl text-sm">New FAQ</a></div>
    <form method="GET" action="{{ route('admin.faqs.index') }}" class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="lg:col-span-2"><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Search</label><input type="text" name="search" value="{{ request('search') }}" placeholder="question or answer..." class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 text-sm focus:ring-2 focus:ring-indigo-500"></div>
            <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Category</label><select name="category" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 text-sm"><option value="all">All</option>@foreach($categories as $c)<option value="{{ $c }}" @selected(request('category')===$c)>{{ $c }}</option>@endforeach</select></div>
            <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Status</label><select name="status" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 text-sm"><option value="all">All</option><option value="1" @selected(request('status')==='1')>Active</option><option value="0" @selected(request('status')==='0')>Inactive</option></select></div>
        </div>
        <div class="mt-4 flex gap-3"><button type="submit" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl text-sm">Filter</button>@if(request()->hasAny(['search','category','status']))<a href="{{ route('admin.faqs.index') }}" class="px-4 py-2.5 text-sm font-medium text-gray-600 dark:text-gray-300">Clear</a>@endif</div>
    </form>
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto"><table class="w-full text-sm">
            <thead><tr class="text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-200 dark:border-gray-700"><th class="px-5 py-3">#</th><th class="px-5 py-3">Question</th><th class="px-5 py-3">Category</th><th class="px-5 py-3">Status</th><th class="px-5 py-3 text-right">Actions</th></tr></thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
            @forelse($faqs as $faq)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40">
                    <td class="px-5 py-4 text-gray-500">{{ $faq->sort_order }}</td>
                    <td class="px-5 py-4"><p class="font-medium text-gray-900 dark:text-gray-100">{{ $faq->question }}</p><p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-2">{{ Str::limit($faq->answer, 120) }}</p></td>
                    <td class="px-5 py-4 text-gray-600 dark:text-gray-300">{{ $faq->category ?? '—' }}</td>
                    <td class="px-5 py-4"><span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium {{ $faq->is_active ? 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300' : 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300' }}">{{ $faq->is_active ? 'Active' : 'Inactive' }}</span></td>
                    <td class="px-5 py-4 text-right whitespace-nowrap">
                        <a href="{{ route('admin.faqs.edit', $faq) }}" class="inline-flex px-3 py-1.5 text-xs font-medium text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 rounded-lg">Edit</a>
                        <form method="POST" action="{{ route('admin.faqs.toggle-active', $faq) }}" class="inline">@csrf @method('PATCH')<button type="submit" class="inline-flex px-3 py-1.5 text-xs font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 rounded-lg">{{ $faq->is_active ? 'Deactivate' : 'Activate' }}</button></form>
                        <form method="POST" action="{{ route('admin.faqs.destroy', $faq) }}" class="inline" onsubmit="return confirm('Delete?')">@csrf @method('DELETE')<button type="submit" class="inline-flex px-3 py-1.5 text-xs font-medium text-red-600 hover:bg-red-50 rounded-lg">Delete</button></form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-5 py-10 text-center text-sm text-gray-500">No FAQs.</td></tr>
            @endforelse
            </tbody>
        </table></div>
    </div>
    @if($faqs->hasPages())<div>{{ $faqs->links() }}</div>@endif
</div>
@endsection
