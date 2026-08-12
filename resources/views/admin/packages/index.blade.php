@extends('admin.layouts.admin')
@section('title', 'Packages')
@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div><h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Packages</h2><p class="text-sm text-gray-500 dark:text-gray-400">Manage plans with price & discount/sale window.</p></div>
        <a href="{{ route('admin.packages.create') }}" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl shadow-sm text-sm">New Package</a>
    </div>
    <form method="GET" action="{{ route('admin.packages.index') }}" class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="lg:col-span-2"><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Search</label><input type="text" name="search" value="{{ request('search') }}" placeholder="name or slug..." class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 text-sm focus:ring-2 focus:ring-indigo-500"></div>
            <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Plan</label><select name="plan" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 text-sm"><option value="all">All Plans</option>@foreach($plans as $p)<option value="{{ $p }}" @selected(request('plan')===$p)>{{ ucfirst($p) }}</option>@endforeach</select></div>
            <div><label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Status</label><select name="status" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 text-sm"><option value="all">All</option><option value="1" @selected(request('status')==='1')>Active</option><option value="0" @selected(request('status')==='0')>Inactive</option></select></div>
        </div>
        <div class="mt-4 flex gap-3"><button type="submit" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl text-sm">Filter</button>@if(request()->hasAny(['search','plan','status']))<a href="{{ route('admin.packages.index') }}" class="px-4 py-2.5 text-sm font-medium text-gray-600 dark:text-gray-300">Clear</a>@endif</div>
    </form>
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto"><table class="w-full text-sm">
            <thead><tr class="text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider border-b border-gray-200 dark:border-gray-700"><th class="px-5 py-3">Package</th><th class="px-5 py-3">Plan</th><th class="px-5 py-3">Price</th><th class="px-5 py-3">Sale</th><th class="px-5 py-3">Status</th><th class="px-5 py-3 text-right">Actions</th></tr></thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
            @forelse($packages as $pkg)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40">
                    <td class="px-5 py-4"><p class="font-medium text-gray-900 dark:text-gray-100">{{ $pkg->name }}</p><p class="text-xs text-gray-500 dark:text-gray-400">{{ $pkg->slug }}@if($pkg->duration_days) · {{ $pkg->duration_days }}d @endif @if($pkg->max_employees) · max {{ $pkg->max_employees }} @endif</p></td>
                    <td class="px-5 py-4"><span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium bg-indigo-100 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300">{{ ucfirst($pkg->plan) }}</span></td>
                    <td class="px-5 py-4">
                        @if($pkg->isOnSale())
                            <span class="line-through text-gray-400 text-xs">{{ number_format($pkg->price) }}</span> <span class="font-semibold text-green-600 dark:text-green-400">{{ number_format($pkg->effective_price) }}</span> <span class="text-xs text-green-600">-{{ $pkg->discount_percent }}%</span>
                        @else
                            <span class="font-medium text-gray-900 dark:text-gray-100">{{ number_format($pkg->price) }}</span>@if($pkg->discount_percent) <span class="text-xs text-gray-400">({{ $pkg->discount_percent }}% scheduled)</span>@endif
                        @endif
                    </td>
                    <td class="px-5 py-4 text-xs text-gray-500 dark:text-gray-400">@if($pkg->sale_starts_at || $pkg->sale_ends_at){{ $pkg->sale_starts_at?->format('d M Y H:i') ?? '—' }} → {{ $pkg->sale_ends_at?->format('d M Y H:i') ?? '—' }}@else <span class="text-gray-400">—</span>@endif</td>
                    <td class="px-5 py-4"><span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium {{ $pkg->is_active ? 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300' : 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300' }}">{{ $pkg->is_active ? 'Active' : 'Inactive' }}</span></td>
                    <td class="px-5 py-4 text-right whitespace-nowrap">
                        <a href="{{ route('admin.packages.edit', $pkg) }}" class="inline-flex px-3 py-1.5 text-xs font-medium text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 rounded-lg">Edit</a>
                        <form method="POST" action="{{ route('admin.packages.toggle-active', $pkg) }}" class="inline">@csrf @method('PATCH')<button type="submit" class="inline-flex px-3 py-1.5 text-xs font-medium text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">{{ $pkg->is_active ? 'Deactivate' : 'Activate' }}</button></form>
                        <form method="POST" action="{{ route('admin.packages.destroy', $pkg) }}" class="inline" onsubmit="return confirm('Delete this package?')">@csrf @method('DELETE')<button type="submit" class="inline-flex px-3 py-1.5 text-xs font-medium text-red-600 hover:bg-red-50 rounded-lg">Delete</button></form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-5 py-10 text-center text-sm text-gray-500">No packages. Create one.</td></tr>
            @endforelse
            </tbody>
        </table></div>
    </div>
    @if($packages->hasPages())<div>{{ $packages->links() }}</div>@endif
</div>
@endsection
