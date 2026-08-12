<?php

namespace App\Services\Ai\Functions;

use App\Models\Kpi;
use App\Models\User;
use App\Services\Ai\Attributes\WAFunction;
use App\Services\Ai\Attributes\WAFunctionParam;

class GetKpis
{
    /**
     * List KPI records for any employee by name or NIP.
     *
     * @param  User  $user  The authenticated user (injected by dispatcher).
     * @param  string|null  $employee  Employee name or NIP.
     * @param  string|null  $status  Filter by KPI status.
     * @return array{count: int, kpis: array}
     */
    #[WAFunction('get_kpis', 'List KPI (performance indicator) records for any employee by name or NIP', 'kpi.view')]
    public function execute(
        User $user,
        #[WAFunctionParam('employee', 'string', 'Employee name or NIP', required: false)]
        ?string $employee = null,
        #[WAFunctionParam('status', 'string', 'KPI status filter', required: false)]
        ?string $status = null,
    ): array {
        $query = Kpi::with('employee.department');

        if ($employee !== null && $employee !== '') {
            $query->whereHas('employee', function ($q) use ($employee): void {
                $q->where('name', 'like', '%'.$employee.'%')
                    ->orWhere('nip', 'like', '%'.$employee.'%');
            });
        }

        if ($status !== null && $status !== '') {
            $query->where('status', $status);
        }

        $kpis = $query->latest()->limit(20)->get();

        return [
            'count' => $kpis->count(),
            'kpis' => $kpis->map(fn (Kpi $k) => [
                'employee' => $k->employee?->name,
                'department' => $k->employee?->department?->name,
                'title' => $k->title,
                'period' => $k->period,
                'target' => (float) $k->target,
                'actual' => (float) $k->actual,
                'weight' => (float) $k->weight,
                'score' => (float) $k->score,
                'status' => $k->status,
                'start_date' => $k->start_date?->toDateString(),
                'end_date' => $k->end_date?->toDateString(),
            ])->toArray(),
        ];
    }
}
