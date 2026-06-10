<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEmergencyContactRequest;
use App\Models\EmergencyContact;
use Illuminate\Http\RedirectResponse;

class EmergencyContactController extends Controller
{
    public function store(StoreEmergencyContactRequest $request): RedirectResponse
    {
        $emergencyContact = EmergencyContact::create($request->validated());
        $this->logCreated($emergencyContact);

        return back()->with('success', 'Emergency contact created successfully.');
    }

    public function update(StoreEmergencyContactRequest $request, EmergencyContact $emergencyContact): RedirectResponse
    {
        $oldData = $emergencyContact->attributesToArray();
        $emergencyContact->update($request->validated());
        $this->logUpdated($emergencyContact, $oldData);

        return back()->with('success', 'Emergency contact updated successfully.');
    }

    public function destroy(EmergencyContact $emergencyContact): RedirectResponse
    {
        $oldData = $emergencyContact->attributesToArray();
        $emergencyContact->delete();
        $this->logDeleted($emergencyContact, $oldData);

        return back()->with('success', 'Emergency contact deleted successfully.');
    }
}
