<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEmployeeTaskEvidenceRequest;
use App\Http\Requests\StoreEmployeeTaskRequest;
use App\Models\Employee;
use App\Models\EmployeeTask;
use App\Services\EmployeeTaskService;
use App\Services\ListSearchService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class EmployeeTaskController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'employee_id' => ['nullable', 'integer', 'exists:employees,id'],
            'period_type' => ['nullable', Rule::in(['daily', 'weekly', 'monthly', 'yearly'])],
            'status' => ['nullable', Rule::in(['pending', 'in_progress', 'completed', 'cancelled'])],
            'priority' => ['nullable', Rule::in(['low', 'normal', 'high', 'urgent'])],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
        ]);
        $user = $request->user();
        $canAssign = $this->canAssign($user);

        $tasks = EmployeeTask::query()
            ->with(['employee.department', 'createdBy', 'assignedBy'])
            ->when(! $canAssign, fn ($query) => $query->where('employee_id', $user->employee_id ?? 0))
            ->when($request->filled('search'), function ($query) use ($request): void {
                $search = ListSearchService::searchTerm($request);

                $query->where(function ($query) use ($search): void {
                    $query->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhereHas('employee', function ($query) use ($search): void {
                            $query->where('name', 'like', "%{$search}%")
                                ->orWhere('nip', 'like', "%{$search}%");
                        });
                });
            })
            ->when($canAssign && filled($filters['employee_id'] ?? null), fn ($query) => $query->where('employee_id', $filters['employee_id']))
            ->when(filled($filters['period_type'] ?? null), fn ($query) => $query->where('period_type', $filters['period_type']))
            ->when(filled($filters['status'] ?? null), fn ($query) => $query->where('status', $filters['status']))
            ->when(filled($filters['priority'] ?? null), fn ($query) => $query->where('priority', $filters['priority']))
            ->when(filled($filters['date_from'] ?? null), fn ($query) => $query->whereDate('period_end', '>=', $filters['date_from']))
            ->when(filled($filters['date_to'] ?? null), fn ($query) => $query->whereDate('period_start', '<=', $filters['date_to']))
            ->latest('period_start')
            ->paginate(10)
            ->withQueryString();

        return view('employee-tasks.index', [
            'tasks' => $tasks,
            'employees' => $canAssign ? Employee::orderBy('name')->get() : collect(),
            'canAssign' => $canAssign,
        ]);
    }

    public function create(Request $request): View
    {
        $canAssign = $this->canAssign($request->user());

        return view('employee-tasks.create', [
            'task' => null,
            'employees' => $canAssign ? Employee::orderBy('name')->get() : collect(),
            'canAssign' => $canAssign,
        ]);
    }

    public function store(StoreEmployeeTaskRequest $request, EmployeeTaskService $service): RedirectResponse
    {
        $task = $service->create($request->validated(), $request->user(), $this->canAssign($request->user()));
        $this->logCreated($task, 'Tasks');

        return redirect()->route('employee-tasks.index')->with('success', 'Task created successfully.');
    }

    public function edit(Request $request, EmployeeTask $employeeTask): View
    {
        $this->authorizeTaskAccess($request, $employeeTask);
        $canAssign = $this->canAssign($request->user());

        return view('employee-tasks.edit', [
            'task' => $employeeTask,
            'employees' => $canAssign ? Employee::orderBy('name')->get() : collect(),
            'canAssign' => $canAssign,
        ]);
    }

    public function update(StoreEmployeeTaskRequest $request, EmployeeTask $employeeTask, EmployeeTaskService $service): RedirectResponse
    {
        $this->authorizeTaskAccess($request, $employeeTask);
        $oldData = $employeeTask->attributesToArray();
        $task = $service->update($employeeTask, $request->validated(), $request->user(), $this->canAssign($request->user()));
        $this->logUpdated($task, $oldData, 'Tasks');

        return redirect()->route('employee-tasks.index')->with('success', 'Task updated successfully.');
    }

    public function destroy(Request $request, EmployeeTask $employeeTask): RedirectResponse
    {
        $this->authorizeTaskAccess($request, $employeeTask);
        $oldData = $employeeTask->attributesToArray();
        $employeeTask->delete();
        $this->logDeleted($employeeTask, $oldData, 'Tasks');

        return back()->with('success', 'Task deleted successfully.');
    }

    public function complete(StoreEmployeeTaskEvidenceRequest $request, EmployeeTask $employeeTask): RedirectResponse
    {
        $this->authorizeTaskAccess($request, $employeeTask);
        $oldData = $employeeTask->attributesToArray();
        $evidenceFiles = collect($employeeTask->evidence_files ?? [])
            ->merge($this->storeEvidenceFiles($request, $employeeTask))
            ->values()
            ->all();

        $employeeTask->update([
            'status' => 'completed',
            'completed_at' => $employeeTask->completed_at ?? now(),
            'evidence_files' => $evidenceFiles,
        ]);

        $this->logUpdated($employeeTask, $oldData, 'Tasks');

        return back()->with('success', 'Task marked as done.');
    }

    public function reopen(Request $request, EmployeeTask $employeeTask): RedirectResponse
    {
        $this->authorizeTaskAccess($request, $employeeTask);
        $oldData = $employeeTask->attributesToArray();

        $employeeTask->update([
            'status' => 'pending',
            'completed_at' => null,
        ]);

        $this->logUpdated($employeeTask, $oldData, 'Tasks');

        return back()->with('success', 'Task moved back to pending.');
    }

    private function canAssign(?object $user): bool
    {
        return $user?->hasPermission('*') || $user?->hasPermission('task.assign') || $user?->hasPermission('task.manage');
    }

    private function storeEvidenceFiles(StoreEmployeeTaskEvidenceRequest $request, EmployeeTask $task): array
    {
        return collect($request->file('evidence_files', []))
            ->map(function ($file) use ($task): array {
                $path = $file->store("employee-task-evidence/{$task->id}", 'public');

                return [
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'url' => Storage::disk('public')->url($path),
                    'size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                    'uploaded_at' => now()->toISOString(),
                ];
            })
            ->all();
    }

    private function authorizeTaskAccess(Request $request, EmployeeTask $task): void
    {
        if ($this->canAssign($request->user())) {
            return;
        }

        abort_unless($request->user()?->employee_id === $task->employee_id, 403, 'Unauthorized action.');
    }
}
