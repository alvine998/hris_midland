<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\OtpVerifyRequest;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class OtpVerificationController extends Controller
{
    public function showVerifyForm(Request $request, OtpService $otpService): View|RedirectResponse
    {
        if (! $request->session()->has('otp_user_id') || ! $request->session()->get('otp_required')) {
            return redirect()->route('login');
        }

        $user = User::find($request->session()->get('otp_user_id'));

        if (! $user) {
            return redirect()->route('login');
        }

        return view('auth.verify-otp', [
            'email' => $user->email,
            'resendRemainingSeconds' => $otpService->getRemainingSeconds($user, 'login'),
        ]);
    }

    public function verify(OtpVerifyRequest $request, OtpService $otpService): RedirectResponse
    {
        $userId = $request->session()->get('otp_user_id');

        if (! $userId) {
            return redirect()->route('login');
        }

        $user = User::find($userId);

        if (! $user) {
            return redirect()->route('login');
        }

        if (! $otpService->verify($user, $request->input('otp_code'), 'login')) {
            return back()->withErrors([
                'otp_code' => 'The provided OTP code is invalid or has expired.',
            ]);
        }

        Auth::login($user);

        $request->session()->forget(['otp_user_id', 'otp_required']);

        return redirect()->intended(route('dashboard'));
    }

    public function resend(Request $request, OtpService $otpService): RedirectResponse
    {
        $userId = $request->session()->get('otp_user_id');

        if (! $userId) {
            return redirect()->route('login');
        }

        $user = User::find($userId);

        if (! $user) {
            return redirect()->route('login');
        }

        if (! $otpService->canResend($user, 'login')) {
            $seconds = $otpService->getRemainingSeconds($user, 'login');

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => "Please wait {$seconds} seconds before requesting a new OTP.",
                    'remaining_seconds' => $seconds,
                ], 429);
            }

            return back()->withErrors([
                'resend' => "Please wait {$seconds} seconds before requesting a new OTP.",
            ]);
        }

        $otpService->generate($user, 'login');

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'A new OTP has been sent to your email.',
            ]);
        }

        return back()->with('status', 'A new OTP has been sent to your email.');
    }
}
