@extends('layouts.guest')

@section('title', 'Contact Support — ' . config('app.name'))

@section('content')
<div class="bg-[#FCFBF8] dark:bg-[#101413] min-h-screen">
    <div class="max-w-[640px] mx-auto px-6 py-10 sm:py-14">
        <a href="{{ route('landing') }}" class="text-sm text-stone-500 hover:text-stone-700">&larr; Back to home</a>
        <h1 class="mt-3 text-[28px] font-bold tracking-tight">Contact Support</h1>
        <p class="mt-2 text-sm text-stone-600 dark:text-stone-400">Have a question? Open a ticket — our team will reply to your email.</p>

        @session('success')<div class="mt-4 rounded-xl border border-green-200 bg-green-50 p-4 text-sm text-green-700">{{ $value }}</div>@endsession

        <form method="POST" action="{{ route('tickets.store') }}" class="mt-6 bg-white dark:bg-stone-900 rounded-2xl border border-stone-200 dark:border-stone-800 p-6 space-y-4">
            @csrf
            <div>
                <label for="name" class="block text-sm font-medium mb-1.5">Your name *</label>
                <input id="name" name="name" value="{{ old('name') }}" required class="w-full px-4 py-2.5 rounded-xl border border-stone-300 dark:border-stone-600 bg-white dark:bg-stone-900 text-sm @error('name') border-red-500 @enderror">
                @error('name')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="email" class="block text-sm font-medium mb-1.5">Email *</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required class="w-full px-4 py-2.5 rounded-xl border border-stone-300 dark:border-stone-600 bg-white dark:bg-stone-900 text-sm @error('email') border-red-500 @enderror">
                @error('email')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="subject" class="block text-sm font-medium mb-1.5">Subject *</label>
                <input id="subject" name="subject" value="{{ old('subject') }}" required class="w-full px-4 py-2.5 rounded-xl border border-stone-300 dark:border-stone-600 bg-white dark:bg-stone-900 text-sm @error('subject') border-red-500 @enderror">
                @error('subject')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="message" class="block text-sm font-medium mb-1.5">Message *</label>
                <textarea id="message" name="message" rows="5" required class="w-full px-4 py-2.5 rounded-xl border border-stone-300 dark:border-stone-600 bg-white dark:bg-stone-900 text-sm @error('message') border-red-500 @enderror">{{ old('message') }}</textarea>
                @error('message')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <button type="submit" class="w-full h-11 rounded-full bg-[#12372A] text-white font-semibold hover:bg-[#1a4d3a]">Submit ticket</button>
        </form>
    </div>
</div>
@endsection
