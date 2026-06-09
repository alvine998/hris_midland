<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEducationHistoryRequest;
use App\Models\EducationHistory;
use Illuminate\Http\RedirectResponse;

class EducationHistoryController extends Controller
{
    public function store(StoreEducationHistoryRequest $request): RedirectResponse
    {
        EducationHistory::create($request->validated());

        return redirect()->back()->with('success', 'Education record created successfully.');
    }

    public function update(StoreEducationHistoryRequest $request, EducationHistory $educationHistory): RedirectResponse
    {
        $educationHistory->update($request->validated());

        return redirect()->back()->with('success', 'Education record updated successfully.');
    }

    public function destroy(EducationHistory $educationHistory): RedirectResponse
    {
        $educationHistory->delete();

        return redirect()->back()->with('success', 'Education record deleted successfully.');
    }
}
