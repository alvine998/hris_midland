<?php

use App\Http\Middleware\AdminAuthenticate;
use App\Http\Middleware\CheckPermission;
use App\Http\Middleware\CompanyAuthenticate;
use App\Http\Middleware\RedirectIfAdminAuthenticated;
use App\Http\Middleware\RedirectIfCompanyAuthenticated;
use App\Http\Middleware\SecurityHeaders;
use App\Providers\AiFunctionServiceProvider;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withProviders([
        AiFunctionServiceProvider::class,
    ])
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function (): void {
            Route::middleware('web')
                ->prefix('admin')
                ->name('admin.')
                ->group(base_path('routes/admin.php'));

            Route::middleware('web')
                ->prefix('company')
                ->name('company.')
                ->group(base_path('routes/company.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(SecurityHeaders::class);

        $middleware->validateCsrfTokens(except: [
            'webhooks/*',
        ]);

        $middleware->alias([
            'permission' => CheckPermission::class,
            'admin' => AdminAuthenticate::class,
            'admin.guest' => RedirectIfAdminAuthenticated::class,
            'company' => CompanyAuthenticate::class,
            'company.guest' => RedirectIfCompanyAuthenticated::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
