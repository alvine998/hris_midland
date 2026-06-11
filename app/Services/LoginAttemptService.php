<?php

namespace App\Services;

use App\Models\LoginAttempt;
use App\Models\User;
use Carbon\CarbonInterface;

class LoginAttemptService
{
    private const MAX_FAILED_ATTEMPTS = 3;

    private const LOCKOUT_MINUTES = 5;

    public function isLocked(User $user): bool
    {
        return $this->failedAttempts($user) >= self::MAX_FAILED_ATTEMPTS;
    }

    public function remainingSeconds(User $user): int
    {
        $oldestAttempt = LoginAttempt::query()
            ->where('user_id', $user->id)
            ->where('success', false)
            ->where('created_at', '>=', $this->windowStart())
            ->oldest()
            ->first();

        if (! $oldestAttempt) {
            return 0;
        }

        return max(0, (int) now()->diffInSeconds($oldestAttempt->created_at->addMinutes(self::LOCKOUT_MINUTES), false));
    }

    public function recordFailed(?User $user): void
    {
        if (! $user) {
            return;
        }

        LoginAttempt::create([
            'user_id' => $user->id,
            'success' => false,
        ]);
    }

    public function recordSuccessful(User $user): void
    {
        LoginAttempt::where('user_id', $user->id)
            ->where('success', false)
            ->forceDelete();

        LoginAttempt::create([
            'user_id' => $user->id,
            'success' => true,
        ]);
    }

    public function failedAttempts(User $user): int
    {
        return LoginAttempt::query()
            ->where('user_id', $user->id)
            ->where('success', false)
            ->where('created_at', '>=', $this->windowStart())
            ->count();
    }

    private function windowStart(): CarbonInterface
    {
        return now()->subMinutes(self::LOCKOUT_MINUTES);
    }
}
