<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFamilyRequest;
use App\Models\Family;
use Illuminate\Http\RedirectResponse;

class FamilyController extends Controller
{
    public function store(StoreFamilyRequest $request): RedirectResponse
    {
        Family::create($request->validated());

        return redirect()->back()->with('success', 'Family member created successfully.');
    }

    public function update(StoreFamilyRequest $request, Family $family): RedirectResponse
    {
        $family->update($request->validated());

        return redirect()->back()->with('success', 'Family member updated successfully.');
    }

    public function destroy(Family $family): RedirectResponse
    {
        $family->delete();

        return redirect()->back()->with('success', 'Family member deleted successfully.');
    }
}
