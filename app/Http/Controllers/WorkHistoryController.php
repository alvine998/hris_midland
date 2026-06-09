<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWorkHistoryRequest;
use App\Models\WorkHistory;
use Illuminate\Http\RedirectResponse;

class WorkHistoryController extends Controller
{
    public function store(StoreWorkHistoryRequest $request): RedirectResponse
    {
        WorkHistory::create($request->validated());

        return redirect()->back()->with('success', 'Work history created successfully.');
    }

    public function update(StoreWorkHistoryRequest $request, WorkHistory $workHistory): RedirectResponse
    {
        $workHistory->update($request->validated());

        return redirect()->back()->with('success', 'Work history updated successfully.');
    }

    public function destroy(WorkHistory $workHistory): RedirectResponse
    {
        $workHistory->delete();

        return redirect()->back()->with('success', 'Work history deleted successfully.');
    }
}
