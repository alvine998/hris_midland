@extends('layouts.app')

@section('title', 'Check-In History')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Check-In History</h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Your selfie attendance records</p>
        </div>
        <a href="{{ route('attendances.check-in') }}" class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-indigo-700">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            New Check-In
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
        @if ($attendances->count())
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                        <th class="text-left px-6 py-3 font-semibold text-gray-600 dark:text-gray-400">Date</th>
                        <th class="text-left px-6 py-3 font-semibold text-gray-600 dark:text-gray-400">Check In</th>
                        <th class="text-left px-6 py-3 font-semibold text-gray-600 dark:text-gray-400">Check Out</th>
                        <th class="text-left px-6 py-3 font-semibold text-gray-600 dark:text-gray-400">Hours</th>
                        <th class="text-left px-6 py-3 font-semibold text-gray-600 dark:text-gray-400">Location</th>
                        <th class="text-left px-6 py-3 font-semibold text-gray-600 dark:text-gray-400">Selfie</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach ($attendances as $attendance)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <td class="px-6 py-4 text-gray-900 dark:text-white whitespace-nowrap">
                            {{ $attendance->clock_in?->format('d M Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-gray-900 dark:text-white">{{ $attendance->clock_in?->format('H:i') }}</span>
                            @if ($attendance->is_mock_location_in)
                                <span class="ml-1.5 inline-flex items-center rounded-full bg-red-100 px-1.5 py-0.5 text-[10px] font-medium text-red-700 dark:bg-red-900/30 dark:text-red-300">Suspicious GPS</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-gray-600 dark:text-gray-400 whitespace-nowrap">
                            {{ $attendance->clock_out?->format('H:i') ?? '-' }}
                            @if ($attendance->clock_out && $attendance->is_mock_location_out)
                                <span class="ml-1.5 inline-flex items-center rounded-full bg-red-100 px-1.5 py-0.5 text-[10px] font-medium text-red-700 dark:bg-red-900/30 dark:text-red-300">Suspicious GPS</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-gray-600 dark:text-gray-400 whitespace-nowrap">
                            {{ $attendance->work_hours ? $attendance->work_hours.'h' : '-' }}
                        </td>
                        <td class="px-6 py-4 text-gray-600 dark:text-gray-400 text-xs max-w-[200px] truncate">
                            @if ($attendance->location_in)
                                {{ $attendance->location_in['latitude'] }}, {{ $attendance->location_in['longitude'] }}
                                @if ($attendance->gps_accuracy_in)
                                    <span class="text-gray-400">(±{{ $attendance->gps_accuracy_in }}m)</span>
                                @endif
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if ($attendance->selfie_in)
                            <a href="{{ Storage::url($attendance->selfie_in) }}" target="_blank" class="inline-flex items-center gap-1.5 text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 dark:hover:text-indigo-300 text-xs font-medium">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                View
                            </a>
                            @else
                            -
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
            {{ $attendances->links() }}
        </div>
        @else
        <div class="px-6 py-12 text-center">
            <svg class="w-12 h-12 mx-auto text-gray-300 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <p class="text-sm text-gray-500 dark:text-gray-400">No check-in records found.</p>
            <a href="{{ route('attendances.check-in') }}" class="mt-3 inline-flex items-center text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 dark:hover:text-indigo-300 transition-colors">
                Check in now
            </a>
        </div>
        @endif
    </div>
</div>
@endsection
