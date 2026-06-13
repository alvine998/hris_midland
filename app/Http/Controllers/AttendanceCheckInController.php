<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Services\AttendanceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AttendanceCheckInController extends Controller
{
    public function __construct(
        private readonly AttendanceService $attendanceService
    ) {}

    public function create(): View
    {
        $employee = Auth::user()->employee;

        if (! $employee) {
            abort(403, 'You are not registered as an employee.');
        }

        $todayAttendance = $this->attendanceService->getTodayAttendance($employee);
        $checkedIn = $todayAttendance !== null && $todayAttendance->clock_out === null;
        $checkedOut = $todayAttendance !== null && $todayAttendance->clock_out !== null;

        $workLocationCheck = null;
        if ($employee->workLocation) {
            $workLocationCheck = [
                'name' => $employee->workLocation->name,
                'latitude' => $employee->workLocation->latitude,
                'longitude' => $employee->workLocation->longitude,
                'radius' => $employee->workLocation->radius ?? 100,
                'address' => $employee->workLocation->address,
            ];
        }

        return view('attendances.check-in', compact(
            'employee',
            'todayAttendance',
            'checkedIn',
            'checkedOut',
            'workLocationCheck'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $employee = Auth::user()->employee;

        if (! $employee) {
            abort(403, 'You are not registered as an employee.');
        }

        $validated = $request->validate([
            'selfie' => ['required', 'image', 'max:5120', 'mimes:jpeg,png,jpg'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'gps_accuracy' => ['nullable', 'numeric', 'min:0', 'max:10000'],
            'is_mock_location' => ['nullable', 'in:true,false,1,0'],
            'action' => ['required', 'in:check_in,check_out'],
        ]);

        $validated['is_mock_location'] = filter_var($validated['is_mock_location'] ?? false, FILTER_VALIDATE_BOOLEAN);

        $existing = $this->attendanceService->hasCheckedInToday($employee);

        if ($validated['action'] === 'check_in') {
            if ($existing) {
                return back()->withErrors(['You have already checked in today.']);
            }

            $attendance = $this->attendanceService->checkIn($employee, $validated);

            return redirect()->route('attendances.check-in')
                ->with('success', 'Check-in recorded successfully.');
        }

        if ($validated['action'] === 'check_out') {
            if (! $existing) {
                return back()->withErrors(['You have not checked in today.']);
            }

            if ($existing->clock_out) {
                return back()->withErrors(['You have already checked out today.']);
            }

            $this->attendanceService->checkOut($existing, $validated);

            return redirect()->route('attendances.check-in')
                ->with('success', 'Check-out recorded successfully.');
        }

        return back()->withErrors(['Invalid action.']);
    }

    public function history(): View
    {
        $employee = Auth::user()->employee;

        if (! $employee) {
            abort(403, 'You are not registered as an employee.');
        }

        $attendances = Attendance::where('employee_id', $employee->id)
            ->where('check_in_method', 'selfie')
            ->latest('clock_in')
            ->paginate(10);

        return view('attendances.check-in-history', compact('attendances'));
    }
}
