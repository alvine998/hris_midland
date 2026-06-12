<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\EmployeeTask;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class EmployeeTaskSeeder extends Seeder
{
    public function run(): void
    {
        $employee = Employee::query()->first();

        if (! $employee) {
            return;
        }

        $admin = User::query()
            ->whereIn('email', ['admin@example.com', 'superadmin@example.com'])
            ->first();
        $today = Carbon::today();

        $tasks = [
            [
                'title' => 'Submit daily work summary',
                'description' => 'Write today\'s work progress and blockers.',
                'period_type' => 'daily',
                'period_start' => $today,
                'period_end' => $today,
                'priority' => 'normal',
                'status' => 'pending',
            ],
            [
                'title' => 'Review weekly attendance exceptions',
                'description' => 'Check missing clock-in and clock-out records for this week.',
                'period_type' => 'weekly',
                'period_start' => $today->copy()->startOfWeek(),
                'period_end' => $today->copy()->endOfWeek(),
                'priority' => 'high',
                'status' => 'in_progress',
            ],
            [
                'title' => 'Prepare monthly HR report',
                'description' => 'Collect employee movement, attendance, and leave summary data.',
                'period_type' => 'monthly',
                'period_start' => $today->copy()->startOfMonth(),
                'period_end' => $today->copy()->endOfMonth(),
                'priority' => 'high',
                'status' => 'pending',
            ],
            [
                'title' => 'Update yearly personal development plan',
                'description' => 'Refresh yearly development goals and training target.',
                'period_type' => 'yearly',
                'period_start' => $today->copy()->startOfYear(),
                'period_end' => $today->copy()->endOfYear(),
                'priority' => 'normal',
                'status' => 'pending',
            ],
        ];

        foreach ($tasks as $task) {
            EmployeeTask::updateOrCreate(
                [
                    'employee_id' => $employee->id,
                    'title' => $task['title'],
                    'period_type' => $task['period_type'],
                ],
                [
                    ...$task,
                    'employee_id' => $employee->id,
                    'created_by_user_id' => $admin?->id,
                    'assigned_by_user_id' => $admin?->id,
                    'completed_at' => $task['status'] === 'completed' ? now() : null,
                ],
            );
        }
    }
}
