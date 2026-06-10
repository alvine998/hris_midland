<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEducationHistoryRequest;
use App\Models\EducationHistory;
use Illuminate\Http\RedirectResponse;

class EducationHistoryController extends Controller
{
    public function store(StoreEducationHistoryRequest $request): RedirectResponse
    {
        $educationHistory = EducationHistory::create($request->validated());
        $this->logCreated($educationHistory);

        return redirect()->back()->with('success', 'Education record created successfully.');
    }

    public function update(StoreEducationHistoryRequest $request, EducationHistory $educationHistory): RedirectResponse
    {
        $oldData = $educationHistory->attributesToArray();
        $educationHistory->update($request->validated());
        $this->logUpdated($educationHistory, $oldData);

        return redirect()->back()->with('success', 'Education record updated successfully.');
    }

    public function destroy(EducationHistory $educationHistory): RedirectResponse
    {
        $oldData = $educationHistory->attributesToArray();
        $educationHistory->delete();
        $this->logDeleted($educationHistory, $oldData);

        return redirect()->back()->with('success', 'Education record deleted successfully.');
    }
}
