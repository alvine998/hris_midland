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
        $shift = Shift::create($request->validated());
        $this->logCreated($shift);

        return back()->with('success', 'Shift created successfully.');
    }

    public function update(StoreShiftRequest $request, Shift $shift): RedirectResponse
    {
        $oldData = $shift->attributesToArray();
        $shift->update($request->validated());
        $this->logUpdated($shift, $oldData);

        return back()->with('success', 'Shift updated successfully.');
    }

    public function destroy(Shift $shift): RedirectResponse
    {
        $oldData = $shift->attributesToArray();
        $shift->delete();
        $this->logDeleted($shift, $oldData);

        return back()->with('success', 'Shift deleted successfully.');
    }
}
