<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use App\Services\LoginAttemptService;
use App\Services\OtpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request, OtpService $otpService, LoginAttemptService $loginAttemptService): RedirectResponse
    {
        $credentials = $request->only('email', 'password');
        $user = User::where('email', $credentials['email'])->first();

        if ($user && $loginAttemptService->isLocked($user)) {
            $seconds = $loginAttemptService->remainingSeconds($user);

            return back()
                ->withErrors(['email' => "Too many failed login attempts. Please wait {$seconds} seconds before trying again."])
                ->with('warning', "Too many failed login attempts. Please wait {$seconds} seconds before trying again.")
                ->onlyInput('email');
        }

        if (! Auth::validate($credentials)) {
            $loginAttemptService->recordFailed($user);
            $message = 'The provided credentials do not match our records.';

            if ($user && $loginAttemptService->isLocked($user)) {
                $seconds = $loginAttemptService->remainingSeconds($user);
                $message = "Too many failed login attempts. Please wait {$seconds} seconds before trying again.";

                return back()
                    ->withErrors(['email' => $message])
                    ->with('warning', $message)
                    ->onlyInput('email');
            }

            return back()->withErrors([
                'email' => $message,
            ])->with('error', $message)->onlyInput('email');
        }

        $otpService->generate($user, 'login');

        $request->session()->put('otp_user_id', $user->id);
        $request->session()->put('otp_required', true);

        return redirect()->route('verify-otp')->with('success', 'OTP code has been sent to your email.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
