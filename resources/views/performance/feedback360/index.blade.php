@extends('layouts.app')

@section('title', '360 Feedback - ' . config('app.name'))

@section('content')
<div class="space-y-8">
    <div class="flex items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">360 Feedback</h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Give and receive feedback from your colleagues.</p>
        </div>
        @if ($employee)
            <a href="{{ route('performance.feedback360.create') }}" class="inline-flex items-center rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-indigo-700">
                Give Feedback
            </a>
        @endif
    </div>

    @session('success')
        <div class="rounded-xl border border-green-200 bg-green-100 px-4 py-3 text-sm text-green-700 dark:border-green-800 dark:bg-green-900/30 dark:text-green-400">
            {{ $value }}
        </div>
    @endsession

    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Feedback I've Received</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-900">
                        <th class="px-6 py-4 text-left font-semibold text-gray-900 dark:text-white">Reviewer</th>
                        <th class="px-6 py-4 text-left font-semibold text-gray-900 dark:text-white">Type</th>
                        <th class="px-6 py-4 text-left font-semibold text-gray-900 dark:text-white">Period</th>
                        <th class="px-6 py-4 text-center font-semibold text-gray-900 dark:text-white">Overall Score</th>
                        <th class="px-6 py-4 text-left font-semibold text-gray-900 dark:text-white">Status</th>
                        <th class="px-6 py-4 text-left font-semibold text-gray-900 dark:text-white">Date</th>
                        <th class="px-6 py-4 text-right font-semibold text-gray-900 dark:text-white"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse ($receivedFeedbacks as $feedback)
                        <tr class="transition-colors hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900 dark:text-white">{{ $feedback->reviewerEmployee?->name ?? $feedback->reviewer_name }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $feedback->reviewerEmployee?->jobPosition?->name ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 text-gray-600 dark:text-gray-400">{{ ucfirst($feedback->reviewer_type) }}</td>
                            <td class="px-6 py-4 text-gray-600 dark:text-gray-400">{{ $feedback->period }}</td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center justify-center rounded-full px-3 py-1 text-sm font-semibold {{ $feedback->overall_score >= 80 ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : ($feedback->overall_score >= 60 ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400' : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400') }}">
                                    {{ $feedback->overall_score }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium {{ $feedback->status === 'submitted' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300' }}">
                                    {{ ucfirst($feedback->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-600 dark:text-gray-400">{{ $feedback->created_at->format('d M Y') }}</td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('performance.feedback360.show', $feedback) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">No feedback received yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800 mt-8">
        <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Feedback I've Given</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-900">
                        <th class="px-6 py-4 text-left font-semibold text-gray-900 dark:text-white">Employee</th>
                        <th class="px-6 py-4 text-left font-semibold text-gray-900 dark:text-white">Type</th>
                        <th class="px-6 py-4 text-left font-semibold text-gray-900 dark:text-white">Period</th>
                        <th class="px-6 py-4 text-center font-semibold text-gray-900 dark:text-white">Overall Score</th>
                        <th class="px-6 py-4 text-left font-semibold text-gray-900 dark:text-white">Status</th>
                        <th class="px-6 py-4 text-left font-semibold text-gray-900 dark:text-white">Date</th>
                        <th class="px-6 py-4 text-right font-semibold text-gray-900 dark:text-white"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse ($givenFeedbacks as $feedback)
                        <tr class="transition-colors hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900 dark:text-white">{{ $feedback->employee?->name }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $feedback->employee?->jobPosition?->name ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 text-gray-600 dark:text-gray-400">{{ ucfirst($feedback->reviewer_type) }}</td>
                            <td class="px-6 py-4 text-gray-600 dark:text-gray-400">{{ $feedback->period }}</td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center justify-center rounded-full px-3 py-1 text-sm font-semibold {{ $feedback->overall_score >= 80 ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : ($feedback->overall_score >= 60 ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400' : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400') }}">
                                    {{ $feedback->overall_score }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium {{ $feedback->status === 'submitted' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300' }}">
                                    {{ ucfirst($feedback->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-600 dark:text-gray-400">{{ $feedback->created_at->format('d M Y') }}</td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('performance.feedback360.show', $feedback) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">You haven't given any feedback yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
