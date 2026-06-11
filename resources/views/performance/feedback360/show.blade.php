@extends('layouts.app')

@section('title', 'Feedback Detail - ' . config('app.name'))

@section('content')
<div class="mx-auto max-w-3xl">
    <div class="mb-6">
        <a href="{{ route('performance.feedback360.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300">&larr; Back to Feedback</a>
    </div>

    <div class="space-y-6">
        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white">Feedback Detail</h2>

            <div class="mt-6 grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div>
                    <h4 class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Reviewer</h4>
                    <p class="mt-1 text-sm font-medium text-gray-900 dark:text-white">{{ $feedback->reviewerEmployee?->name ?? $feedback->reviewer_name }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $feedback->reviewerEmployee?->jobPosition?->name ?? '-' }}</p>
                </div>
                <div>
                    <h4 class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Employee</h4>
                    <p class="mt-1 text-sm font-medium text-gray-900 dark:text-white">{{ $feedback->employee?->name }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $feedback->employee?->jobPosition?->name ?? '-' }}</p>
                </div>
                <div>
                    <h4 class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Reviewer Type</h4>
                    <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ ucfirst($feedback->reviewer_type) }}</p>
                </div>
                <div>
                    <h4 class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Period</h4>
                    <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $feedback->period }}</p>
                </div>
                <div>
                    <h4 class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Status</h4>
                    <span class="mt-1 inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium {{ $feedback->status === 'submitted' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300' }}">
                        {{ ucfirst($feedback->status) }}
                    </span>
                </div>
                <div>
                    <h4 class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Submitted At</h4>
                    <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $feedback->created_at->format('d M Y H:i') }}</p>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Scores</h3>
            <div class="mt-4 grid grid-cols-2 gap-4 sm:grid-cols-5">
                <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 text-center dark:border-gray-600 dark:bg-gray-900">
                    <p class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">{{ $feedback->communication_score }}</p>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Communication</p>
                </div>
                <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 text-center dark:border-gray-600 dark:bg-gray-900">
                    <p class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">{{ $feedback->teamwork_score }}</p>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Teamwork</p>
                </div>
                <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 text-center dark:border-gray-600 dark:bg-gray-900">
                    <p class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">{{ $feedback->leadership_score }}</p>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Leadership</p>
                </div>
                <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 text-center dark:border-gray-600 dark:bg-gray-900">
                    <p class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">{{ $feedback->technical_score }}</p>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Technical</p>
                </div>
                <div class="rounded-xl border-2 border-indigo-200 bg-indigo-50 p-4 text-center dark:border-indigo-800 dark:bg-indigo-900/20 sm:col-span-1">
                    <p class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">{{ $feedback->overall_score }}</p>
                    <p class="mt-1 text-xs font-semibold text-indigo-500 dark:text-indigo-300">Overall</p>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Strengths</h3>
            <div class="mt-2 rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 dark:border-gray-600 dark:bg-gray-900">
                <p class="text-sm text-gray-700 dark:text-gray-300">{{ $feedback->strengths ?? 'No strengths provided.' }}</p>
            </div>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Areas for Improvement</h3>
            <div class="mt-2 rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 dark:border-gray-600 dark:bg-gray-900">
                <p class="text-sm text-gray-700 dark:text-gray-300">{{ $feedback->improvements ?? 'No improvements provided.' }}</p>
            </div>
        </div>

        @if ($feedback->comments)
            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Additional Comments</h3>
                <div class="mt-2 rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 dark:border-gray-600 dark:bg-gray-900">
                    <p class="text-sm text-gray-700 dark:text-gray-300">{{ $feedback->comments }}</p>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
