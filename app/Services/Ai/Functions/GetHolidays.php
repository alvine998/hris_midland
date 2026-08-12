<?php

namespace App\Services\Ai\Functions;

use App\Models\Holiday;
use App\Models\User;
use App\Services\Ai\Attributes\WAFunction;
use App\Services\Ai\Attributes\WAFunctionParam;
use Illuminate\Support\Carbon;

class GetHolidays
{
    /**
     * List upcoming or all holidays with optional limit.
     *
     * @param  User  $user  The authenticated user (injected by dispatcher).
     * @param  string|null  $from  Start date in YYYY-MM-DD format (defaults to today).
     * @return array{count: int, holidays: array}
     */
    #[WAFunction('get_holidays', 'List upcoming holidays starting from a given date (defaults to today)', 'holiday.view')]
    public function execute(
        User $user,
        #[WAFunctionParam('from', 'string', 'Start date in YYYY-MM-DD format. Defaults to today.', required: false)]
        ?string $from = null,
    ): array {
        $startDate = $from !== null ? Carbon::parse($from) : Carbon::today();

        $holidays = Holiday::where('start_date', '>=', $startDate)
            ->orderBy('start_date')
            ->limit(20)
            ->get();

        return [
            'count' => $holidays->count(),
            'holidays' => $holidays->map(fn (Holiday $h) => [
                'name' => $h->name,
                'start_date' => $h->start_date?->toDateString(),
                'end_date' => $h->end_date?->toDateString(),
                'type' => $h->type,
            ])->toArray(),
        ];
    }
}
