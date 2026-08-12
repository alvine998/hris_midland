@extends('layouts.guest')

@section('title', 'Upload Payment Proof — ' . config('app.name'))

@section('content')
<div class="bg-[#FCFBF8] dark:bg-[#101413] min-h-screen">
    <div class="max-w-[640px] mx-auto px-6 py-10 sm:py-14">
        <a href="{{ route('orders.success', $order->order_code) }}" class="text-sm text-stone-500 hover:text-stone-700 dark:text-stone-400">&larr; Back to order</a>
        <h1 class="mt-3 text-2xl font-bold tracking-tight">Upload Payment Proof</h1>
        <p class="mt-2 text-sm text-stone-600 dark:text-stone-400">Order <span class="font-mono font-semibold">{{ $order->order_code }}</span> — Rp {{ number_format($order->price, 0, ',', '.') }}</p>

        @if($order->payment_proof)
        <div class="mt-4 p-4 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 text-sm text-emerald-800 dark:text-emerald-200">
            Payment proof already uploaded. Upload again to replace.
        </div>
        @endif

        <form method="POST" action="{{ route('orders.upload-proof.store', $order->order_code) }}" enctype="multipart/form-data" class="mt-6 bg-white dark:bg-stone-900 rounded-2xl border border-stone-200 dark:border-stone-800 p-6 space-y-4">
            @csrf
            <div>
                <label for="payment_proof" class="block text-sm font-medium text-stone-700 dark:text-stone-300 mb-1.5">Payment proof image *</label>
                <input id="payment_proof" type="file" name="payment_proof" accept="image/*" required class="w-full px-4 py-2.5 rounded-xl border border-stone-300 dark:border-stone-600 bg-white dark:bg-stone-900 text-sm file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-[#12372A] file:text-white hover:file:bg-[#1a4d3a] @error('payment_proof') border-red-500 @enderror">
                @error('payment_proof')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                <p class="mt-1.5 text-xs text-stone-500">Max 5 MB. Accepted formats: JPG, PNG.</p>
            </div>
            <button type="submit" class="w-full h-11 rounded-full bg-[#12372A] text-white font-semibold hover:bg-[#1a4d3a] transition-colors">Upload proof</button>
        </form>
    </div>
</div>
@endsection
