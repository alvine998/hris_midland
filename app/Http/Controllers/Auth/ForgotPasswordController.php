<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class ForgotPasswordController extends Controller
{
    public function showForgotForm(): View
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(ForgotPasswordRequest $request, OtpService $otpService): RedirectResponse
    {
        $user = User::where('email', $request->input('email'))->first();

        if ($user) {
            $otpService->generate($user, 'password_reset');

            Password::sendResetLink(
                $request->only('email')
            );
        }

        return back()->with(['status' => __(Password::RESET_LINK_SENT)]);
    }
}
