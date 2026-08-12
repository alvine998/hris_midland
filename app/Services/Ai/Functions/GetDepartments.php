<?php

namespace App\Services\Ai\Functions;

use App\Models\Department;
use App\Models\User;
use App\Services\Ai\Attributes\WAFunction;

class GetDepartments
{
    /**
     * List all departments with their active employee counts.
     *
     * @param  User  $user  The authenticated user (injected by dispatcher).
     * @return array{count: int, departments: array}
     */
    #[WAFunction('get_departments', 'List all departments with active employee counts', 'department.view')]
    public function execute(
        User $user,
    ): array {
        $departments = Department::withCount(['employees as active_employees_count' => function ($query): void {
            $query->where('status', 'active');
        }])
            ->with('company')
            ->orderBy('name')
            ->get();

        return [
            'count' => $departments->count(),
            'departments' => $departments->map(fn (Department $d) => [
                'name' => $d->name,
                'company' => $d->company?->name,
                'active_employees' => $d->active_employees_count,
            ])->toArray(),
        ];
    }
}
