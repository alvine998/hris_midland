<?php

namespace App\Http\Controllers;

use App\Models\Feedback360;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class Feedback360Controller extends Controller
{
    public function index(): View
    {
        $employee = auth()->user()->employee;

        $receivedFeedbacks = Feedback360::with(['reviewerEmployee.jobPosition'])
            ->where('employee_id', $employee?->id)
            ->latest()
            ->get();

        $givenFeedbacks = Feedback360::with(['employee.jobPosition'])
            ->where('reviewer_employee_id', $employee?->id)
            ->latest()
            ->get();

        return view('performance.feedback360.index', [
            'receivedFeedbacks' => $receivedFeedbacks,
            'givenFeedbacks' => $givenFeedbacks,
            'employee' => $employee,
        ]);
    }

    public function create(): View
    {
        return view('performance.feedback360.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'employee_id' => ['required', 'integer', 'exists:employees,id'],
            'reviewer_type' => ['required', 'string', 'in:manager,peer,subordinate'],
            'period' => ['required', 'string', 'max:100'],
            'communication_score' => ['required', 'integer', 'min:1', 'max:100'],
            'teamwork_score' => ['required', 'integer', 'min:1', 'max:100'],
            'leadership_score' => ['required', 'integer', 'min:1', 'max:100'],
            'technical_score' => ['required', 'integer', 'min:1', 'max:100'],
            'strengths' => ['nullable', 'string'],
            'improvements' => ['nullable', 'string'],
            'comments' => ['nullable', 'string'],
        ]);

        $data['reviewer_employee_id'] = auth()->user()->employee?->id;
        $data['reviewer_name'] = auth()->user()->name;
        $data['overall_score'] = (int) round((
            $data['communication_score']
            + $data['teamwork_score']
            + $data['leadership_score']
            + $data['technical_score']
        ) / 4);
        $data['status'] = 'submitted';
        $data['reviewed_at'] = now();

        $feedback = Feedback360::create($data);

        $this->logCreated($feedback, 'Feedback 360');

        return redirect()->route('performance.feedback360.index')
            ->with('success', 'Feedback submitted successfully.');
    }

    public function show(Feedback360 $feedback): View
    {
        $feedback->load(['employee.jobPosition', 'reviewerEmployee.jobPosition']);

        return view('performance.feedback360.show', [
            'feedback' => $feedback,
        ]);
    }
}
