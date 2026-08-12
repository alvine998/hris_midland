@extends('admin.layouts.admin')
@section('title', 'New Package')
@section('content')
<div class="max-w-2xl space-y-6">
    <div><a href="{{ route('admin.packages.index') }}" class="text-sm text-gray-500 hover:text-gray-700">&larr; Back to packages</a><h2 class="mt-1 text-lg font-semibold text-gray-900 dark:text-gray-100">New Package</h2></div>
    <form method="POST" action="{{ route('admin.packages.store') }}" class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 space-y-5">
        @csrf
        @include('admin.packages._form')
        <div class="flex gap-3 pt-2"><button type="submit" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl text-sm">Create</button><a href="{{ route('admin.packages.index') }}" class="px-4 py-2.5 text-sm font-medium text-gray-600 dark:text-gray-300">Cancel</a></div>
    </form>
</div>
@endsection
