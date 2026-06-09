@extends('layouts.app')

@section('title', 'Profile - ' . config('app.name'))

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Profile</h2>
    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Manage your account information.</p>
</div>

@if (session('success'))
<div class="mb-4 px-4 py-3 rounded-xl bg-green-100 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-sm text-green-700 dark:text-green-400">
    {{ session('success') }}
</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-1">
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm text-center">
            <span class="inline-flex items-center justify-center w-20 h-20 bg-indigo-100 dark:bg-indigo-900 text-indigo-600 dark:text-indigo-300 rounded-full text-2xl font-bold">
                {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
            </span>
            <h3 class="mt-4 text-lg font-semibold text-gray-900 dark:text-white">{{ Auth::user()->name }}</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ Auth::user()->email }}</p>
        </div>
    </div>

    <div class="lg:col-span-2">
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm p-6 sm:p-8">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Edit Profile</h3>
            <form method="POST" action="{{ route('profile.update') }}" class="space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name', Auth::user()->name) }}" required class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-indigo-500 text-sm @error('name') border-red-500 @enderror" placeholder="Your full name">
                    @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email', Auth::user()->email) }}" required class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-indigo-500 text-sm @error('email') border-red-500 @enderror" placeholder="you@company.com">
                    @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <hr class="border-gray-200 dark:border-gray-700">

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">New Password</label>
                    <input type="password" name="password" id="password" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-indigo-500 text-sm" placeholder="Leave blank to keep current">
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Confirm Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="w-full px-4 py-2.5 rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-indigo-500 text-sm" placeholder="Confirm new password">
                </div>

                <div class="flex items-center justify-end gap-3 pt-4">
                    <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl shadow-sm text-sm transition-colors">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
