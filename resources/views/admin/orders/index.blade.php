@extends('admin.layouts.admin')

@section('title', 'Orders')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div><h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Orders</h2><p class="text-sm text-gray-500">Incoming package orders from landing page.</p></div>
    </div>

    <form method="GET" action="{{ route('admin.orders.index') }}" class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-4">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search code, company, email..." class="px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-sm">
            <select name="status" class="px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-sm">
                <option value="all">All Status</option>
                @foreach(['pending','approved','rejected','expired'] as $s)<option value="{{ $s }}" @selected(request('status')===$s)>{{ ucfirst($s) }}</option>@endforeach
            </select>
            <div class="flex gap-2"><button type="submit" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl text-sm">Filter</button>@if(request()->hasAny(['search','status']))<a href="{{ route('admin.orders.index') }}" class="px-4 py-2.5 text-sm text-gray-600">Clear</a>@endif</div>
        </div>
    </form>

    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="text-left text-xs font-semibold text-gray-500 uppercase border-b border-gray-200 dark:border-gray-700"><th class="px-5 py-3">Order</th><th class="px-5 py-3">Company</th><th class="px-5 py-3">Package</th><th class="px-5 py-3">Price</th><th class="px-5 py-3">Status</th><th class="px-5 py-3 text-right">Actions</th></tr></thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($orders as $o)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40">
                        <td class="px-5 py-4"><span class="font-mono text-xs font-medium">{{ $o->order_code }}</span><div class="text-xs text-gray-500">{{ $o->created_at->format('d M Y H:i') }}</div></td>
                        <td class="px-5 py-4"><div class="font-medium">{{ $o->company_name }}</div><div class="text-xs text-gray-500">{{ $o->email }}</div></td>
                        <td class="px-5 py-4">{{ $o->package->name ?? '-' }} <span class="text-xs text-gray-500">({{ $o->plan }})</span></td>
                        <td class="px-5 py-4">Rp {{ number_format($o->price,0,',','.') }}</td>
                        <td class="px-5 py-4"><span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium {{ $o->status==='pending'?'bg-amber-100 text-amber-700':($o->status==='approved'?'bg-green-100 text-green-700':($o->status==='rejected'?'bg-red-100 text-red-700':'bg-gray-100 text-gray-700')) }}">{{ ucfirst($o->status) }}</span></td>
                        <td class="px-5 py-4 text-right">
                            <a href="{{ route('admin.orders.show', $o) }}" class="inline-flex px-3 py-1.5 text-xs font-medium text-indigo-600 hover:bg-indigo-50 rounded-lg">View</a>
                            <form method="POST" action="{{ route('admin.orders.destroy', $o) }}" class="inline" onsubmit="return confirm('Delete order?')">@csrf @method('DELETE')<button type="submit" class="inline-flex px-3 py-1.5 text-xs font-medium text-red-600 hover:bg-red-50 rounded-lg">Delete</button></form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-5 py-10 text-center text-sm text-gray-500">No orders yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($orders->hasPages())<div>{{ $orders->links() }}</div>@endif
</div>
@endsection
