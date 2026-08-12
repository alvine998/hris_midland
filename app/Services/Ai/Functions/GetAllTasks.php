<?php

namespace App\Services\Ai\Functions;

use App\Models\EmployeeTask;
use App\Models\User;
use App\Services\Ai\Attributes\WAFunction;
use App\Services\Ai\Attributes\WAFunctionParam;

class GetAllTasks
{
    /**
     * List all employee tasks across the organisation with optional filters.
     *
     * @param  User  $user  The authenticated user (injected by dispatcher).
     * @param  string|null  $status  Filter by task status (pending/in_progress/completed).
     * @param  string|null  $employee  Filter by employee name or NIP.
     * @return array{count: int, tasks: array}
     */
    #[WAFunction('get_all_tasks', 'List all employee tasks across the organisation with optional status and employee filters', 'task.manage')]
    public function execute(
        User $user,
        #[WAFunctionParam('status', 'string', 'Task status filter', enum: ['pending', 'in_progress', 'completed'], required: false)]
        ?string $status = null,
        #[WAFunctionParam('employee', 'string', 'Filter by employee name or NIP', required: false)]
        ?string $employee = null,
    ): array {
        $query = EmployeeTask::with(['employee.department', 'assignedBy']);

        if ($status !== null && $status !== '') {
            $query->where('status', $status);
        }

        if ($employee !== null && $employee !== '') {
            $query->whereHas('employee', function ($q) use ($employee): void {
                $q->where('name', 'like', '%'.$employee.'%')
                    ->orWhere('nip', 'like', '%'.$employee.'%');
            });
        }

        $tasks = $query->latest()->limit(20)->get();

        return [
            'count' => $tasks->count(),
            'tasks' => $tasks->map(fn (EmployeeTask $t) => [
                'employee' => $t->employee?->name,
                'department' => $t->employee?->department?->name,
                'title' => $t->title,
                'status' => $t->status,
                'priority' => $t->priority,
                'period_start' => $t->period_start?->toDateString(),
                'period_end' => $t->period_end?->toDateString(),
                'assigned_by' => $t->assignedBy?->name,
            ])->toArray(),
        ];
    }
}
