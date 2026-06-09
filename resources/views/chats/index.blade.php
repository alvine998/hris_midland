@extends('layouts.app')

@section('title', 'Chats - ' . config('app.name'))

@section('content')
<div class="max-w-2xl">
    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Chats</h2>
    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Use the chat panel in the bottom-right corner from this page or any other page.</p>

    @session('success')
    <div class="mt-4 rounded-xl border border-green-200 bg-green-50 p-4 text-sm text-green-700 dark:border-green-800 dark:bg-green-900/30 dark:text-green-300">{{ $value }}</div>
    @endsession

    @if ($errors->any())
        <div class="mt-4 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700 dark:border-red-800 dark:bg-red-900/30 dark:text-red-300">{{ $errors->first() }}</div>
    @endif
</div>
@endsection
