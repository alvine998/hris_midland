@extends('layouts.app')

@section('title', 'Give Feedback - ' . config('app.name'))

@section('content')
<div class="mx-auto max-w-3xl">
    <div class="mb-6">
        <a href="{{ route('performance.feedback360.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300">&larr; Back to Feedback</a>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Give Feedback</h2>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Submit 360-degree feedback for a colleague.</p>

        <form
            method="POST"
            action="{{ route('performance.feedback360.store') }}"
            x-data="{
                communication_score: 0,
                teamwork_score: 0,
                leadership_score: 0,
                technical_score: 0,
                get overall_score() {
                    const total = Number(this.communication_score) + Number(this.teamwork_score) + Number(this.leadership_score) + Number(this.technical_score);
                    return Math.round(total / 4);
                }
            }"
            class="mt-6 space-y-6"
        >
            @csrf

            <x-employee-async-select
                name="employee_id"
                placeholder="Search employee to review..."
                required
            />

            <div>
                <label for="reviewer_type" class="block text-sm font-medium text-gray-900 dark:text-gray-100">Reviewer Type</label>
                <select name="reviewer_type" id="reviewer_type" required
                    class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-gray-900 focus:ring-2 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    <option value="">Select type...</option>
                    <option value="manager">Manager</option>
                    <option value="peer">Peer</option>
                    <option value="subordinate">Subordinate</option>
                </select>
                @error('reviewer_type')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="period" class="block text-sm font-medium text-gray-900 dark:text-gray-100">Period</label>
                <input type="text" name="period" id="period" value="{{ old('period') }}" required placeholder="e.g. Q1 2026, January 2026"
                    class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-gray-900 focus:ring-2 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                @error('period')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label for="communication_score" class="block text-sm font-medium text-gray-900 dark:text-gray-100">Communication Score (1-100)</label>
                    <input type="number" name="communication_score" id="communication_score" x-model="communication_score" min="1" max="100" required
                        class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-gray-900 focus:ring-2 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    @error('communication_score')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="teamwork_score" class="block text-sm font-medium text-gray-900 dark:text-gray-100">Teamwork Score (1-100)</label>
                    <input type="number" name="teamwork_score" id="teamwork_score" x-model="teamwork_score" min="1" max="100" required
                        class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-gray-900 focus:ring-2 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    @error('teamwork_score')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="leadership_score" class="block text-sm font-medium text-gray-900 dark:text-gray-100">Leadership Score (1-100)</label>
                    <input type="number" name="leadership_score" id="leadership_score" x-model="leadership_score" min="1" max="100" required
                        class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-gray-900 focus:ring-2 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    @error('leadership_score')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="technical_score" class="block text-sm font-medium text-gray-900 dark:text-gray-100">Technical Score (1-100)</label>
                    <input type="number" name="technical_score" id="technical_score" x-model="technical_score" min="1" max="100" required
                        class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-gray-900 focus:ring-2 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    @error('technical_score')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 dark:border-gray-600 dark:bg-gray-900">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Overall Score: </span>
                <span class="text-lg font-bold text-indigo-600 dark:text-indigo-400" x-text="overall_score">0</span>
                <span class="text-sm text-gray-500 dark:text-gray-400">/ 100</span>
            </div>

            <div>
                <label for="strengths" class="block text-sm font-medium text-gray-900 dark:text-gray-100">Strengths</label>
                <textarea name="strengths" id="strengths" rows="3" placeholder="What are this employee's key strengths?"
                    class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-gray-900 focus:ring-2 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">{{ old('strengths') }}</textarea>
                @error('strengths')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="improvements" class="block text-sm font-medium text-gray-900 dark:text-gray-100">Areas for Improvement</label>
                <textarea name="improvements" id="improvements" rows="3" placeholder="What areas could be improved?"
                    class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-gray-900 focus:ring-2 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">{{ old('improvements') }}</textarea>
                @error('improvements')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="comments" class="block text-sm font-medium text-gray-900 dark:text-gray-100">Additional Comments</label>
                <textarea name="comments" id="comments" rows="3" placeholder="Any additional comments..."
                    class="mt-1 block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-gray-900 focus:ring-2 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">{{ old('comments') }}</textarea>
                @error('comments')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('performance.feedback360.index') }}" class="rounded-lg bg-gray-100 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">Cancel</a>
                <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700">Submit Feedback</button>
            </div>
        </form>
    </div>
</div>
@endsection
