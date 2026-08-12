<?php

namespace App\Services\Ai\Functions;

use App\Models\PayrollPeriod;
use App\Models\User;
use App\Services\Ai\Attributes\WAFunction;
use App\Services\Ai\Attributes\WAFunctionParam;

class GetPayrollPeriods
{
    /**
     * List payroll periods with optional status filter.
     *
     * @param  User  $user  The authenticated user (injected by dispatcher).
     * @param  string|null  $status  Filter by period status (open/approved/closed).
     * @return array{count: int, periods: array}
     */
    #[WAFunction('get_payroll_periods', 'List payroll periods with optional status filter', 'payroll-period.view')]
    public function execute(
        User $user,
        #[WAFunctionParam('status', 'string', 'Payroll period status filter', enum: ['open', 'approved', 'closed'], required: false)]
        ?string $status = null,
    ): array {
        $query = PayrollPeriod::with('company');

        if ($status !== null && $status !== '') {
            $query->where('status', $status);
        }

        $periods = $query->latest()->limit(20)->get();

        return [
            'count' => $periods->count(),
            'periods' => $periods->map(fn (PayrollPeriod $pp) => [
                'company' => $pp->company?->name,
                'month' => $pp->month,
                'year' => $pp->year,
                'start_date' => $pp->start_date?->toDateString(),
                'end_date' => $pp->end_date?->toDateString(),
                'status' => $pp->status,
            ])->toArray(),
        ];
    }
}
