@extends('layouts.company')

@section('title', 'Login — ' . config('app.name'))

@section('content')
<div class="min-h-[80vh] flex items-center justify-center px-4">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold tracking-tight">{{ config('app.name') }}</h1>
            <p class="mt-2 text-sm text-stone-600 dark:text-stone-400">Sign in to your company workspace</p>
        </div>

        <div class="bg-white dark:bg-stone-900 rounded-2xl border border-stone-200 dark:border-stone-800 p-6">
            <form method="POST" action="{{ route('company.login.attempt') }}" class="space-y-4">
                @csrf
                <div>
                    <label for="email" class="block text-sm font-medium text-stone-700 dark:text-stone-300 mb-1.5">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus class="w-full px-4 py-2.5 rounded-xl border border-stone-300 dark:border-stone-600 bg-white dark:bg-stone-900 text-sm @error('email') border-red-500 @enderror" placeholder="company@example.com">
                    @error('email')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-stone-700 dark:text-stone-300 mb-1.5">Password</label>
                    <input id="password" type="password" name="password" required class="w-full px-4 py-2.5 rounded-xl border border-stone-300 dark:border-stone-600 bg-white dark:bg-stone-900 text-sm @error('password') border-red-500 @enderror" placeholder="••••••••">
                    @error('password')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div class="flex items-center gap-2">
                    <input id="remember" type="checkbox" name="remember" class="rounded border-stone-300 text-[#12372A]">
                    <label for="remember" class="text-sm text-stone-600 dark:text-stone-400">Remember me</label>
                </div>
                <button type="submit" class="w-full h-11 rounded-full bg-[#12372A] text-white font-semibold hover:bg-[#1a4d3a] transition-colors">Sign in</button>
            </form>
        </div>
    </div>
</div>
@endsection
