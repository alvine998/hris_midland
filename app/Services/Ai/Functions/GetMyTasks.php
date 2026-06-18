<?php

namespace App\Services\Ai\Functions;

use App\Models\EmployeeTask;
use App\Models\User;
use App\Services\Ai\Attributes\WAFunction;
use App\Services\Ai\Attributes\WAFunctionParam;

class GetMyTasks
{
    /**
     * Get the authenticated user's assigned tasks.
     *
     * @param  User  $user  The authenticated user (injected by dispatcher).
     * @param  string|null  $status  Optional status filter (pending/in_progress/completed).
     * @return array{count: int, tasks: array}
     */
    #[WAFunction('get_my_tasks', 'Get my assigned tasks')]
    public function execute(
        User $user,
        #[WAFunctionParam('status', 'string', 'Task status filter', enum: ['pending', 'in_progress', 'completed'], required: false)]
        ?string $status = null,
    ): array {
        $employee = $user->employee;

        if ($employee === null) {
            return [
                'count' => 0,
                'tasks' => [],
                'message' => 'No employee record found for your account.',
            ];
        }

        $query = EmployeeTask::where('employee_id', $employee->id);

        if ($status !== null && $status !== '') {
            $query->where('status', $status);
        }

        $tasks = $query->orderBy('period_end', 'asc')->get();

        return [
            'count' => $tasks->count(),
            'tasks' => $tasks->map(fn (EmployeeTask $task) => [
                'title' => $task->title,
                'status' => $task->status,
                'due_date' => $task->period_end?->toDateString(),
            ])->toArray(),
        ];
    }
}
