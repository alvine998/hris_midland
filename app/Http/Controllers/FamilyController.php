<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFamilyRequest;
use App\Models\Family;
use Illuminate\Http\RedirectResponse;

class FamilyController extends Controller
{
    public function store(StoreFamilyRequest $request): RedirectResponse
    {
        $family = Family::create($request->validated());
        $this->logCreated($family);

        return redirect()->back()->with('success', 'Family member created successfully.');
    }

    public function update(StoreFamilyRequest $request, Family $family): RedirectResponse
    {
        $oldData = $family->attributesToArray();
        $family->update($request->validated());
        $this->logUpdated($family, $oldData);

        return redirect()->back()->with('success', 'Family member updated successfully.');
    }

    public function destroy(Family $family): RedirectResponse
    {
        $oldData = $family->attributesToArray();
        $family->delete();
        $this->logDeleted($family, $oldData);

        return redirect()->back()->with('success', 'Family member deleted successfully.');
    }
}
