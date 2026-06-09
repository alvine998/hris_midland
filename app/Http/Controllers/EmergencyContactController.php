<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEmergencyContactRequest;
use App\Models\EmergencyContact;
use Illuminate\Http\RedirectResponse;

class EmergencyContactController extends Controller
{
    public function store(StoreEmergencyContactRequest $request): RedirectResponse
    {
        EmergencyContact::create($request->validated());

        return back()->with('success', 'Emergency contact created successfully.');
    }

    public function update(StoreEmergencyContactRequest $request, EmergencyContact $emergencyContact): RedirectResponse
    {
        $emergencyContact->update($request->validated());

        return back()->with('success', 'Emergency contact updated successfully.');
    }

    public function destroy(EmergencyContact $emergencyContact): RedirectResponse
    {
        $emergencyContact->delete();

        return back()->with('success', 'Emergency contact deleted successfully.');
    }
}
