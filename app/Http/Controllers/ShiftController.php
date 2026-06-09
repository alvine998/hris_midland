<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreShiftRequest;
use App\Models\Shift;
use App\Services\ListSearchService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShiftController extends Controller
{
    public function index(Request $request): View
    {
        $shifts = ListSearchService::apply(Shift::query(), $request, ['name'])
            ->paginate(10)
            ->withQueryString();

        return view('master-data.shifts', ['shifts' => $shifts]);
    }

    public function store(StoreShiftRequest $request): RedirectResponse
    {
        Shift::create($request->validated());

        return back()->with('success', 'Shift created successfully.');
    }

    public function update(StoreShiftRequest $request, Shift $shift): RedirectResponse
    {
        $shift->update($request->validated());

        return back()->with('success', 'Shift updated successfully.');
    }

    public function destroy(Shift $shift): RedirectResponse
    {
        $shift->delete();

        return back()->with('success', 'Shift deleted successfully.');
    }
}
