<?php

namespace App\Services\Ai\Functions;

use App\Models\Transfer;
use App\Models\User;
use App\Services\Ai\Attributes\WAFunction;
use App\Services\Ai\Attributes\WAFunctionParam;

class GetTransfers
{
    /**
     * List employee transfer records with optional filters.
     *
     * @param  User  $user  The authenticated user (injected by dispatcher).
     * @param  string|null  $status  Filter by transfer status.
     * @param  string|null  $employee  Filter by employee name or NIP.
     * @return array{count: int, transfers: array}
     */
    #[WAFunction('get_transfers', 'List employee transfer records with optional status and employee filters', 'transfer.view')]
    public function execute(
        User $user,
        #[WAFunctionParam('status', 'string', 'Transfer status filter', required: false)]
        ?string $status = null,
        #[WAFunctionParam('employee', 'string', 'Filter by employee name or NIP', required: false)]
        ?string $employee = null,
    ): array {
        $query = Transfer::with(['employee.department', 'transferType']);

        if ($status !== null && $status !== '') {
            $query->where('status', $status);
        }

        if ($employee !== null && $employee !== '') {
            $query->whereHas('employee', function ($q) use ($employee): void {
                $q->where('name', 'like', '%'.$employee.'%')
                    ->orWhere('nip', 'like', '%'.$employee.'%');
            });
        }

        $transfers = $query->latest()->limit(20)->get();

        return [
            'count' => $transfers->count(),
            'transfers' => $transfers->map(fn (Transfer $t) => [
                'employee' => $t->employee?->name,
                'department' => $t->employee?->department?->name,
                'transfer_type' => $t->transferType?->name,
                'from' => $t->transfer_from,
                'to' => $t->transfer_to,
                'reason' => $t->reason,
                'status' => $t->status,
            ])->toArray(),
        ];
    }
}
