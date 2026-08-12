<?php

namespace App\Http\Controllers\Company\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CompanyLoginController extends Controller
{
    public function showLoginForm(): View
    {
        return view('company.auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::guard('company')->attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withErrors(['email' => 'The provided credentials do not match our records.'])
                ->with('error', 'The provided credentials do not match our records.')
                ->onlyInput('email');
        }

        $company = Auth::guard('company')->user();

        if (! $company->is_active) {
            Auth::guard('company')->logout();

            return back()
                ->withErrors(['email' => 'Your account is not active. Please contact support.'])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->intended(route('company.dashboard'))
            ->with('success', 'Welcome, '.$company->name.'!');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('company')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('landing')->with('success', 'You have been logged out.');
    }
}
