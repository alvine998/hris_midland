<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWorkHistoryRequest;
use App\Models\WorkHistory;
use Illuminate\Http\RedirectResponse;

class WorkHistoryController extends Controller
{
    public function store(StoreWorkHistoryRequest $request): RedirectResponse
    {
        $workHistory = WorkHistory::create($request->validated());
        $this->logCreated($workHistory);

        return redirect()->back()->with('success', 'Work history created successfully.');
    }

    public function update(StoreWorkHistoryRequest $request, WorkHistory $workHistory): RedirectResponse
    {
        $oldData = $workHistory->attributesToArray();
        $workHistory->update($request->validated());
        $this->logUpdated($workHistory, $oldData);

        return redirect()->back()->with('success', 'Work history updated successfully.');
    }

    public function destroy(WorkHistory $workHistory): RedirectResponse
    {
        $oldData = $workHistory->attributesToArray();
        $workHistory->delete();
        $this->logDeleted($workHistory, $oldData);

        return redirect()->back()->with('success', 'Work history deleted successfully.');
    }
}
