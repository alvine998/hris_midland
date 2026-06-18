<?php

namespace App\Services\Ai\Functions;

use App\Models\Employee;
use App\Models\User;
use App\Services\Ai\Attributes\WAFunction;
use App\Services\Ai\Attributes\WAFunctionParam;
use Illuminate\Database\Eloquent\Builder;

class GetEmployees
{
    /**
     * Search and list employees in the company.
     *
     * @param  User|null  $user  The authenticated user (injected by dispatcher).
     * @param  string|null  $search  Optional search keyword (matches name or NIP).
     * @param  string|null  $status  Optional status filter (active/inactive).
     * @return array{count: int, employees: array}
     */
    #[WAFunction('get_employees', 'Search and list employees in the company')]
    public function execute(
        User $user,
        #[WAFunctionParam('search', 'string', 'Search keyword to match employee name or NIP', required: false)]
        ?string $search = null,
        #[WAFunctionParam('status', 'string', 'Employee status filter', enum: ['active', 'inactive'], required: false)]
        ?string $status = null,
    ): array {
        $query = Employee::query()->with('department');

        // Apply search filter on name or NIP
        if ($search !== null && $search !== '') {
            $query->where(function (Builder $q) use ($search): void {
                $q->where('name', 'like', '%'.$search.'%')
                    ->orWhere('nip', 'like', '%'.$search.'%');
            });
        }

        // Apply status filter
        if ($status !== null && $status !== '') {
            $query->where('status', $status);
        }

        $employees = $query->limit(10)->get();

        return [
            'count' => $employees->count(),
            'employees' => $employees->map(fn (Employee $emp) => [
                'name' => $emp->name,
                'nip' => $emp->nip,
                'department' => $emp->department?->name,
                'status' => $emp->status,
            ])->toArray(),
        ];
    }
}
