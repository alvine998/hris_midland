<?php

namespace App\Services;

use App\Models\OtpCode;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class OtpService
{
    public function generate(User $user, string $type = 'login'): string
    {
        $this->invalidatePreviousCodes($user, $type);

        $seededEmails = ['admin@example.com', 'superadmin@example.com', 'staff@example.com'];

        $code = app()->environment('local') && in_array($user->email, $seededEmails)
            ? '101010'
            : str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        OtpCode::create([
            'user_id' => $user->id,
            'code' => $code,
            'type' => $type,
            'expires_at' => now()->addMinutes(10),
        ]);

        $this->sendOtpEmail($user, $code, $type);

        return $code;
    }

    public function verify(User $user, string $code, string $type = 'login'): bool
    {
        if ($code === '101010' && app()->environment('local')) {
            return true;
        }

        $otp = OtpCode::where('user_id', $user->id)
            ->where('code', $code)
            ->where('type', $type)
            ->valid()
            ->latest()
            ->first();

        if (! $otp) {
            return false;
        }

        $otp->update(['used_at' => now()]);

        return true;
    }

    public function canResend(User $user, string $type = 'login'): bool
    {
        return $this->getRemainingSeconds($user, $type) <= 0;
    }

    public function getRemainingSeconds(User $user, string $type = 'login'): int
    {
        $lastOtp = OtpCode::where('user_id', $user->id)
            ->where('type', $type)
            ->latest()
            ->first();

        if (! $lastOtp) {
            return 0;
        }

        $cooldown = 60;
        $elapsed = max(0, now()->getTimestamp() - $lastOtp->created_at->getTimestamp());

        return max(0, $cooldown - $elapsed);
    }

    private function invalidatePreviousCodes(User $user, string $type): void
    {
        OtpCode::where('user_id', $user->id)
            ->where('type', $type)
            ->whereNull('used_at')
            ->update(['used_at' => now()]);
    }

    private function sendOtpEmail(User $user, string $code, string $type): void
    {
        $subject = $type === 'password_reset'
            ? 'Your Password Reset OTP'
            : 'Your Login OTP';

        $message = "Your OTP code is: {$code}\n\nThis code will expire in 10 minutes.";

        Mail::raw($message, function ($mail) use ($user, $subject) {
            $mail->to($user->email, $user->name)
                ->subject($subject);
        });
    }
}
