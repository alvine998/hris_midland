<?php

namespace App\Http\Controllers;

use App\Models\Holiday;
use App\Services\LeaveInclusiveDayService;
use App\Services\ListSearchService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class HolidayController extends Controller
{
    public function index(Request $request): View
    {
        $holidays = ListSearchService::apply(Holiday::query(), $request, ['name', 'type'])
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('holidays.index', ['holidays' => $holidays]);
    }

    public function store(Request $request, LeaveInclusiveDayService $service): RedirectResponse
    {
        $holiday = Holiday::create($this->validated($request));
        $service->recalculateApprovedOverlapping($holiday->start_date, $holiday->end_date);

        return back()->with('success', 'Holiday created successfully.');
    }

    public function update(Request $request, Holiday $holiday, LeaveInclusiveDayService $service): RedirectResponse
    {
        $oldStart = $holiday->start_date;
        $oldEnd = $holiday->end_date;

        $holiday->update($this->validated($request));
        $service->recalculateApprovedOverlapping($oldStart, $oldEnd);
        $service->recalculateApprovedOverlapping($holiday->start_date, $holiday->end_date);

        return back()->with('success', 'Holiday updated successfully.');
    }

    public function destroy(Holiday $holiday, LeaveInclusiveDayService $service): RedirectResponse
    {
        $start = $holiday->start_date;
        $end = $holiday->end_date;
        $holiday->delete();
        $service->recalculateApprovedOverlapping($start, $end);

        return back()->with('success', 'Holiday deleted successfully.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'type' => ['required', Rule::in(['company', 'national'])],
        ]);
    }
}
