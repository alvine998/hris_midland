<?php

namespace App\Services\Ai\Functions;

use App\Models\Employee;
use App\Models\User;
use App\Services\Ai\Attributes\WAFunction;
use App\Services\Ai\Attributes\WAFunctionParam;
use Illuminate\Database\Eloquent\Builder;

class GetEmployeeDetail
{
    /**
     * Get detailed information about a specific employee by name or NIP.
     *
     * @param  User  $user  The authenticated user (injected by dispatcher).
     * @param  string  $search  Employee name or NIP to look up.
     * @return array{employee: array|null, message: string|null}
     */
    #[WAFunction('get_employee_detail', 'Get detailed information about a specific employee by name or NIP', 'employee.view')]
    public function execute(
        User $user,
        #[WAFunctionParam('search', 'string', 'Employee name or NIP to search for', required: true)]
        string $search,
    ): array {
        $employee = Employee::with([
            'department', 'division', 'section', 'jobPosition', 'company',
            'workLocation', 'religion', 'salary', 'contracts',
        ])
            ->where(function (Builder $q) use ($search): void {
                $q->where('name', 'like', '%'.$search.'%')
                    ->orWhere('nip', 'like', '%'.$search.'%')
                    ->orWhere('email', 'like', '%'.$search.'%');
            })
            ->first();

        if ($employee === null) {
            return [
                'employee' => null,
                'message' => "No employee found matching '{$search}'.",
            ];
        }

        $latestContract = $employee->contracts->sortByDesc('end_date')->first();

        return [
            'employee' => [
                'name' => $employee->name,
                'nip' => $employee->nip,
                'email' => $employee->email,
                'phone' => $employee->phone,
                'status' => $employee->status,
                'company' => $employee->company?->name,
                'department' => $employee->department?->name,
                'division' => $employee->division?->name,
                'section' => $employee->section?->name,
                'job_position' => $employee->jobPosition?->name,
                'work_location' => $employee->workLocation?->name,
                'join_date' => $employee->join_date?->toDateString(),
                'birth_date' => $employee->birth_date?->toDateString(),
                'marital_status' => $employee->marital_status,
                'blood_type' => $employee->blood_type,
                'address' => $employee->address,
                'basic_salary' => $employee->salary?->basic_salary,
                'contract_end' => $latestContract?->end_date?->toDateString(),
            ],
            'message' => null,
        ];
    }
}
