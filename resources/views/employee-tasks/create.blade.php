@extends('layouts.app')

@section('title', 'Create Task - ' . config('app.name'))

@section('content')
<div class="mb-6">
    <a href="{{ route('employee-tasks.index') }}" class="text-sm font-medium text-indigo-600 transition-colors hover:text-indigo-500 dark:text-indigo-400 dark:hover:text-indigo-300">&larr; Back to Tasks</a>
    <h2 class="mt-2 text-2xl font-bold text-gray-900 dark:text-white">Create Task</h2>
    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ $canAssign ? 'Create a personal task or assign work to an employee.' : 'Create your own daily, weekly, monthly, or yearly task.' }}</p>
</div>

<div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800 sm:p-8">
    <form method="POST" action="{{ route('employee-tasks.store') }}">
        @csrf

        @include('employee-tasks._form', [
            'task' => $task,
            'employees' => $employees,
            'canAssign' => $canAssign,
            'submitLabel' => 'Create Task',
        ])
    </form>
</div>
@endsection
