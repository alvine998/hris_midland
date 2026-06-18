<?php

namespace App\Providers;

use App\Services\Ai\FunctionRegistry;
use Illuminate\Support\ServiceProvider;

class AiFunctionServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(FunctionRegistry::class);
    }

    public function boot(): void
    {
        $this->callAfterResolving(FunctionRegistry::class, function ($registry) {
            $functionsPath = app_path('Services/Ai/Functions');

            if (is_dir($functionsPath)) {
                $registry->scanDirectory($functionsPath);
            }
        });
    }
}
