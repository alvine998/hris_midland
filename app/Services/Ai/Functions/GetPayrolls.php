<?php

namespace App\Services\Ai\Functions;

use App\Models\Payroll;
use App\Models\User;
use App\Services\Ai\Attributes\WAFunction;
use App\Services\Ai\Attributes\WAFunctionParam;

class GetPayrolls
{
    /**
     * List payroll records across the organisation with optional filters.
     *
     * @param  User  $user  The authenticated user (injected by dispatcher).
     * @param  string|null  $status  Filter by payroll status (draft/approved/paid).
     * @param  string|null  $employee  Filter by employee name or NIP.
     * @return array{count: int, payrolls: array}
     */
    #[WAFunction('get_payrolls', 'List payroll records across the organisation with optional status and employee filters', 'payroll.view')]
    public function execute(
        User $user,
        #[WAFunctionParam('status', 'string', 'Payroll status filter', enum: ['draft', 'approved', 'paid'], required: false)]
        ?string $status = null,
        #[WAFunctionParam('employee', 'string', 'Filter by employee name or NIP', required: false)]
        ?string $employee = null,
    ): array {
        $query = Payroll::with(['employee.department', 'payrollPeriod']);

        if ($status !== null && $status !== '') {
            $query->where('status', $status);
        }

        if ($employee !== null && $employee !== '') {
            $query->whereHas('employee', function ($q) use ($employee): void {
                $q->where('name', 'like', '%'.$employee.'%')
                    ->orWhere('nip', 'like', '%'.$employee.'%');
            });
        }

        $payrolls = $query->latest()->limit(20)->get();

        return [
            'count' => $payrolls->count(),
            'payrolls' => $payrolls->map(fn (Payroll $p) => [
                'employee' => $p->employee?->name,
                'nip' => $p->employee?->nip,
                'department' => $p->employee?->department?->name,
                'period' => $p->payrollPeriod?->month.'/'.$p->payrollPeriod?->year,
                'basic_salary' => (float) $p->basic_salary,
                'allowance_total' => (float) $p->allowance_total,
                'deduction_total' => (float) $p->deduction_total,
                'bpjs_total' => (float) $p->bpjs_total,
                'tax_pph21' => (float) $p->tax_pph21,
                'take_home_pay' => (float) $p->take_home_pay,
                'status' => $p->status,
            ])->toArray(),
        ];
    }
}
