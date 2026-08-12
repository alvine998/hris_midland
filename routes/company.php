<?php

use App\Http\Controllers\Company\Auth\CompanyLoginController;
use Illuminate\Support\Facades\Route;

Route::middleware('company.guest')->group(function () {
    Route::get('/login', [CompanyLoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [CompanyLoginController::class, 'login'])->name('login.attempt');
});

Route::middleware('company')->group(function () {
    Route::post('/logout', [CompanyLoginController::class, 'logout'])->name('logout');

    Route::get('/dashboard', function () {
        return view('company.dashboard');
    })->name('dashboard');
});
