<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\LeaveSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LeaveSettingsController extends Controller
{
    public function index(): View
    {
        $leaveSettings = LeaveSetting::with('company')->latest()->get();
        $companies = Company::orderBy('name')->get();

        return view('leave-settings.index', [
            'leaveSettings' => $leaveSettings,
            'companies' => $companies,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'company_id' => ['nullable', 'exists:companies,id'],
            'is_advance_leave' => ['boolean'],
            'max_advance_leave' => ['nullable', 'integer', 'min:0'],
            'is_rollover' => ['boolean'],
            'max_rollover' => ['nullable', 'integer', 'min:0'],
        ]);

        $data['is_advance_leave'] = $request->boolean('is_advance_leave');
        $data['is_rollover'] = $request->boolean('is_rollover');

        LeaveSetting::create($data);

        return redirect()->route('leave-settings.index')->with('success', 'Leave setting created successfully.');
    }

    public function update(Request $request, LeaveSetting $leaveSetting): RedirectResponse
    {
        $data = $request->validate([
            'company_id' => ['nullable', 'exists:companies,id'],
            'is_advance_leave' => ['boolean'],
            'max_advance_leave' => ['nullable', 'integer', 'min:0'],
            'is_rollover' => ['boolean'],
            'max_rollover' => ['nullable', 'integer', 'min:0'],
        ]);

        $data['is_advance_leave'] = $request->boolean('is_advance_leave');
        $data['is_rollover'] = $request->boolean('is_rollover');

        $leaveSetting->update($data);

        return redirect()->route('leave-settings.index')->with('success', 'Leave setting updated successfully.');
    }

    public function destroy(LeaveSetting $leaveSetting): RedirectResponse
    {
        $leaveSetting->delete();

        return back()->with('success', 'Leave setting deleted successfully.');
    }
}
