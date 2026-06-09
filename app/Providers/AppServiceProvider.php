<?php

namespace App\Providers;

use App\Models\Chat;
use App\Models\User;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
            ]);
        });
    }
}
