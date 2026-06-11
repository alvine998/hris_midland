<?php

namespace App\Providers;

use App\Models\Chat;
use App\Models\EmployeeNotification;
use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('login', function (Request $request) {
            $email = Str::lower((string) $request->input('email'));

            return Limit::perMinute(5)->by($email.'|'.$request->ip());
        });

        RateLimiter::for('otp', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('otp_user_id', 'guest').'|'.$request->ip());
        });

        RateLimiter::for('otp-resend', function (Request $request) {
            return Limit::perMinute(2)->by($request->session()->get('otp_user_id', 'guest').'|'.$request->ip());
        });

        RateLimiter::for('password-reset', function (Request $request) {
            $email = Str::lower((string) $request->input('email'));

            return Limit::perMinute(3)->by($email.'|'.$request->ip());
        });

        View::composer('layouts.app', function ($view): void {
            $userId = auth()->id();

            $view->with([
                'globalChats' => $userId
                    ? Chat::with(['userOne', 'userTwo'])
                        ->where(fn ($query) => $query->where('user_one_id', $userId)->orWhere('user_two_id', $userId))
                        ->latest()
                        ->get()
                    : collect(),
                'globalChatUsers' => $userId
                    ? User::whereKeyNot($userId)->orderBy('name')->get()
                    : collect(),
                'globalNotifications' => $userId
                    ? EmployeeNotification::query()
                        ->where('status', 'sent')
                        ->where(function ($query) use ($userId): void {
                            $query->whereNull('user_ids')
                                ->orWhereJsonContains('user_ids', (string) $userId)
                                ->orWhereJsonContains('user_ids', (int) $userId);
                        })
                        ->latest()
                        ->limit(5)
                        ->get()
                    : collect(),
            ]);
        });
    }
}
