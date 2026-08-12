@extends('layouts.guest')

@section('title', 'Order ' . $order->order_code . ' — ' . config('app.name'))

@section('content')
<div class="bg-[#FCFBF8] dark:bg-[#101413] min-h-screen">
    <div class="max-w-[640px] mx-auto px-6 py-16 text-center">
        <div class="w-14 h-14 rounded-full bg-emerald-600 text-white flex items-center justify-center mx-auto text-xl">✓</div>
        <h1 class="mt-4 text-2xl font-bold tracking-tight">Order received</h1>
        <p class="mt-2 text-sm text-stone-600 dark:text-stone-400">Order <span class="font-mono font-semibold text-stone-900 dark:text-stone-100">{{ $order->order_code }}</span> for <strong>{{ $order->package->name }}</strong></p>

        <div class="mt-6 text-left bg-white dark:bg-stone-900 rounded-2xl border border-stone-200 dark:border-stone-800 p-5 text-sm space-y-2">
            <div class="flex justify-between"><span class="text-stone-500">Company</span><span class="font-medium">{{ $order->company_name }}</span></div>
            <div class="flex justify-between"><span class="text-stone-500">Email</span><span class="font-medium">{{ $order->email }}</span></div>
            <div class="flex justify-between"><span class="text-stone-500">Plan</span><span class="font-medium">{{ ucfirst($order->plan) }}</span></div>
            <div class="flex justify-between"><span class="text-stone-500">Amount</span><span class="font-bold">Rp {{ number_format($order->price, 0, ',', '.') }}</span></div>
            <div class="flex justify-between"><span class="text-stone-500">Status</span><span class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 text-xs font-medium">{{ ucfirst($order->status) }}</span></div>
            @if($order->price > 0 && $order->bankAccount)
                <div class="pt-3 mt-3 border-t border-stone-200 dark:border-stone-700 space-y-1">
                    <div class="text-xs uppercase tracking-widest text-stone-500">Transfer to</div>
                    <div class="font-medium">{{ $order->bankAccount->bank_name }} — {{ $order->bankAccount->account_number }}</div>
                    <div class="text-stone-500">a.n. {{ $order->bankAccount->account_holder }}</div>
                </div>
            @endif
        </div>

        <p class="mt-6 text-sm text-stone-500">We will contact you at {{ $order->email }} to activate your workspace.</p>
        @if($order->status === 'pending')
        <a href="{{ route('orders.upload-proof', $order->order_code) }}" class="mt-4 inline-flex h-10 px-6 rounded-full bg-[#12372A] text-white text-sm font-semibold items-center hover:bg-[#1a4d3a] transition-colors">Upload payment proof</a>
        <div class="mt-2"></div>
        @endif
        <a href="{{ route('landing') }}" class="mt-2 inline-flex h-10 px-6 rounded-full border border-stone-300 dark:border-stone-600 text-sm font-semibold items-center hover:bg-stone-100 dark:hover:bg-stone-800 transition-colors">Back to home</a>
    </div>
</div>
@endsection
