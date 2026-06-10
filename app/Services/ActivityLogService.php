<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Module;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ActivityLogService
{
    public function created(Model $model, ?string $moduleName = null): void
    {
        $this->record('created', $model, null, $this->snapshot($model), $moduleName);
    }

    public function updated(Model $model, array $oldData, ?string $moduleName = null): void
    {
        $this->record('updated', $model, $this->redactSensitiveData($oldData), $this->snapshot($model), $moduleName);
    }

    public function deleted(Model $model, array $oldData, ?string $moduleName = null): void
    {
        $this->record('deleted', $model, $this->redactSensitiveData($oldData), null, $moduleName);
    }

    private function record(string $verb, Model $model, ?array $oldData, ?array $newData, ?string $moduleName): void
    {
        if ($model instanceof ActivityLog) {
            return;
        }

        $actor = Auth::user();
        $actorName = $actor?->name ?? 'System';

        ActivityLog::create([
            'action' => "{$actorName} {$verb} {$this->targetLabel($model)}",
            'user_id' => $actor?->id,
            'module_id' => $this->moduleId($moduleName ?? $this->defaultModuleName($model)),
            'old_data' => $oldData,
            'new_data' => $newData,
        ]);
    }

    private function snapshot(Model $model): array
    {
        return $this->redactSensitiveData($model->fresh()?->attributesToArray() ?? $model->attributesToArray());
    }

    private function redactSensitiveData(array $data): array
    {
        foreach (['password', 'remember_token'] as $key) {
            if (array_key_exists($key, $data)) {
                $data[$key] = '[redacted]';
            }
        }

        return $data;
    }

    private function targetLabel(Model $model): string
    {
        $label = Str::headline(class_basename($model));
        $key = $model->getKey();
        $name = collect(['name', 'title', 'email', 'nip', 'room_id'])
            ->map(fn (string $attribute) => $model->getAttribute($attribute))
            ->first(fn ($value): bool => filled($value));

        return trim("{$label} #{$key}".($name ? " ({$name})" : ''));
    }

    private function moduleId(?string $moduleName): ?int
    {
        if (! $moduleName) {
            return null;
        }

        return Module::query()->where('name', $moduleName)->value('id');
    }

    private function defaultModuleName(Model $model): ?string
    {
        $class = class_basename($model);

        return match (true) {
            Str::contains($class, ['Employee', 'Contract', 'Family', 'Education', 'WorkHistory', 'Document', 'EmergencyContact', 'Relationship']) => 'Employee Management',
            Str::contains($class, ['Attendance', 'Shift']) => 'Attendance',
            Str::contains($class, ['Payroll']) => 'Payroll',
            Str::contains($class, ['User', 'Role', 'Module']) => 'User Management',
            default => null,
        };
    }
}
