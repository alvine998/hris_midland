@extends('admin.layouts.admin')

@section('title', 'Order ' . $order->order_code)

@section('content')
<div class="space-y-6 max-w-3xl">
    <a href="{{ route('admin.orders.index') }}" class="text-sm text-gray-500 hover:text-gray-700">&larr; Back to orders</a>
    <h2 class="text-lg font-semibold">Order {{ $order->order_code }}</h2>

    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 space-y-3 text-sm">
        <div class="flex justify-between"><span class="text-gray-500">Company</span><span class="font-medium">{{ $order->company_name }}</span></div>
        <div class="flex justify-between"><span class="text-gray-500">Email</span><span class="font-medium">{{ $order->email }}</span></div>
        <div class="flex justify-between"><span class="text-gray-500">Phone</span><span class="font-medium">{{ $order->phone ?? '-' }}</span></div>
        <div class="flex justify-between"><span class="text-gray-500">Package</span><span class="font-medium">{{ $order->package->name ?? '-' }} ({{ $order->plan }})</span></div>
        <div class="flex justify-between"><span class="text-gray-500">Price</span><span class="font-bold">Rp {{ number_format($order->price,0,',','.') }}</span></div>
        <div class="flex justify-between"><span class="text-gray-500">Status</span><span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $order->status==='pending'?'bg-amber-100 text-amber-700':($order->status==='approved'?'bg-green-100 text-green-700':'bg-gray-100 text-gray-700') }}">{{ ucfirst($order->status) }}</span></div>
        <div class="flex justify-between"><span class="text-gray-500">Created</span><span>{{ $order->created_at->format('d M Y H:i') }}</span></div>
        @if($order->notes)<div class="pt-3 border-t border-gray-100 dark:border-gray-700"><div class="text-gray-500 mb-1">Customer notes</div><div class="bg-gray-50 dark:bg-gray-900 rounded-xl p-3">{{ $order->notes }}</div></div>@endif
        @if($order->voucher_code)<div class="flex justify-between"><span class="text-gray-500">Voucher code</span><span class="font-mono text-xs font-medium">{{ $order->voucher_code }}</span></div>@endif
        @if($order->payment_proof)<div class="pt-3 border-t border-gray-100 dark:border-gray-700"><div class="text-gray-500 mb-1">Payment proof</div><a href="{{ Storage::url($order->payment_proof) }}" target="_blank" class="text-indigo-600 hover:underline text-sm">View upload</a></div>@endif
        @if($order->admin_notes)<div><div class="text-gray-500 mb-1">Admin notes</div><div class="bg-gray-50 dark:bg-gray-900 rounded-xl p-3">{{ $order->admin_notes }}</div></div>@endif
        @if($order->company_id)<div class="flex justify-between"><span class="text-gray-500">Linked company</span><a href="{{ route('admin.customers.show', $order->company_id) }}" class="text-indigo-600 hover:underline">{{ $order->company->name ?? $order->company_id }}</a></div>@endif
    </div>

    <form method="POST" action="{{ route('admin.orders.update-status', $order) }}" class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 space-y-4">
        @csrf @method('PATCH')
        <div>
            <label class="block text-sm font-medium mb-1.5">Status</label>
            <select name="status" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-sm">
                @foreach(['pending','approved','rejected','expired'] as $s)<option value="{{ $s }}" @selected($order->status===$s)>{{ ucfirst($s) }}</option>@endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1.5">Link to company (when approving)</label>
            <select name="company_id" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-sm">
                <option value="">— No link —</option>
                @foreach($companies as $c)<option value="{{ $c->id }}" @selected($order->company_id==$c->id)>{{ $c->name }}</option>@endforeach
            </select>
            <p class="text-xs text-gray-500 mt-1">Approving will set company plan/max_employees/subscription_expires_at from package.</p>
        </div>
        <div>
            <label class="block text-sm font-medium mb-1.5">Admin notes</label>
            <textarea name="admin_notes" rows="3" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-sm">{{ old('admin_notes', $order->admin_notes) }}</textarea>
        </div>
        <button type="submit" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl text-sm">Update</button>
    </form>
</div>
@endsection
