<?php

namespace App\Http\Controllers;

use App\Services\ActivityLogService;
use Illuminate\Database\Eloquent\Model;

abstract class Controller
{
    protected function logCreated(Model $model, ?string $moduleName = null): void
    {
        app(ActivityLogService::class)->created($model, $moduleName);
    }

    protected function logUpdated(Model $model, array $oldData, ?string $moduleName = null): void
    {
        app(ActivityLogService::class)->updated($model, $oldData, $moduleName);
    }

    protected function logDeleted(Model $model, array $oldData, ?string $moduleName = null): void
    {
        app(ActivityLogService::class)->deleted($model, $oldData, $moduleName);
    }
}
