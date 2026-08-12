<?php

namespace App\Services\Ai\Functions;

use App\Models\ActivityLog;
use App\Models\User;
use App\Services\Ai\Attributes\WAFunction;
use App\Services\Ai\Attributes\WAFunctionParam;

class GetActivityLogs
{
    /**
     * List recent activity logs across the system.
     *
     * @param  User  $user  The authenticated user (injected by dispatcher).
     * @param  string|null  $action  Filter by action keyword.
     * @return array{count: int, logs: array}
     */
    #[WAFunction('get_activity_logs', 'List recent system activity logs with optional action filter', 'activity-log.view')]
    public function execute(
        User $user,
        #[WAFunctionParam('action', 'string', 'Filter by action keyword (e.g. create, update, delete)', required: false)]
        ?string $action = null,
    ): array {
        $query = ActivityLog::with(['user', 'module'])
            ->latest();

        if ($action !== null && $action !== '') {
            $query->where('action', 'like', '%'.$action.'%');
        }

        $logs = $query->limit(20)->get();

        return [
            'count' => $logs->count(),
            'logs' => $logs->map(fn (ActivityLog $log) => [
                'action' => $log->action,
                'user' => $log->user?->name,
                'module' => $log->module?->name,
                'created_at' => $log->created_at?->toDateTimeString(),
            ])->toArray(),
        ];
    }
}
