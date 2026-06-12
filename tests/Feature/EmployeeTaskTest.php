<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\EmployeeTask;
use App\Models\Role;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EmployeeTaskTest extends TestCase
{
    use RefreshDatabase;

    public function test_employee_can_create_own_task(): void
    {
        $employee = $this->employee('Employee User', 'employee@example.test');
        $user = User::factory()->create([
            'employee_id' => $employee->id,
            'email' => 'employee.user@example.test',
        ]);

        $response = $this->actingAs($user)->post(route('employee-tasks.store'), [
            'title' => 'Daily check-in',
            'description' => 'Submit the daily work summary.',
            'period_type' => 'daily',
            'period_start' => '2026-06-11',
            'period_end' => '2026-06-11',
            'priority' => 'normal',
            'status' => 'pending',
        ]);

        $response->assertRedirect(route('employee-tasks.index'));
        $this->assertDatabaseHas('employee_tasks', [
            'employee_id' => $employee->id,
            'title' => 'Daily check-in',
            'period_type' => 'daily',
            'status' => 'pending',
            'assigned_by_user_id' => null,
        ]);
    }

    public function test_admin_can_assign_task_to_employee(): void
    {
        $admin = User::factory()->create(['email' => 'admin@example.test']);
        $role = Role::create(['name' => 'Task Admin', 'description' => 'Task assignment access', 'rbac' => ['task.assign']]);
        UserRole::create(['user_id' => $admin->id, 'role_id' => $role->id]);
        $employee = $this->employee('Assigned Employee', 'assigned@example.test');

        $response = $this->actingAs($admin)->post(route('employee-tasks.store'), [
            'employee_id' => $employee->id,
            'title' => 'Monthly report',
            'description' => 'Prepare the monthly department report.',
            'period_type' => 'monthly',
            'period_start' => '2026-06-01',
            'period_end' => '2026-06-30',
            'priority' => 'high',
            'status' => 'in_progress',
        ]);

        $response->assertRedirect(route('employee-tasks.index'));
        $this->assertDatabaseHas('employee_tasks', [
            'employee_id' => $employee->id,
            'title' => 'Monthly report',
            'period_type' => 'monthly',
            'assigned_by_user_id' => $admin->id,
        ]);
    }

    public function test_employee_can_mark_own_dashboard_task_as_done(): void
    {
        Storage::fake('public');
        $employee = $this->employee('Task Owner', 'task-owner@example.test');
        $user = User::factory()->create([
            'employee_id' => $employee->id,
            'email' => 'task.owner@example.test',
        ]);
        $task = EmployeeTask::create([
            'employee_id' => $employee->id,
            'created_by_user_id' => $user->id,
            'title' => 'Finish daily handover',
            'period_type' => 'daily',
            'period_start' => '2026-06-11',
            'period_end' => '2026-06-11',
            'priority' => 'normal',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($user)->patch(route('employee-tasks.complete', $task), [
            'evidence_files' => [
                UploadedFile::fake()->create('handover.pdf', 128, 'application/pdf'),
            ],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('employee_tasks', [
            'id' => $task->id,
            'status' => 'completed',
        ]);
        $task->refresh();
        $this->assertNotNull($task->completed_at);
        $this->assertCount(1, $task->evidence_files);
        Storage::disk('public')->assertExists($task->evidence_files[0]['path']);
    }

    public function test_employee_can_undo_completed_dashboard_task(): void
    {
        $employee = $this->employee('Undo Owner', 'undo-owner@example.test');
        $user = User::factory()->create([
            'employee_id' => $employee->id,
            'email' => 'undo.owner@example.test',
        ]);
        $task = EmployeeTask::create([
            'employee_id' => $employee->id,
            'created_by_user_id' => $user->id,
            'title' => 'Re-check completed task',
            'period_type' => 'daily',
            'period_start' => '2026-06-11',
            'period_end' => '2026-06-11',
            'priority' => 'normal',
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        $response = $this->actingAs($user)->patch(route('employee-tasks.reopen', $task));

        $response->assertRedirect();
        $this->assertDatabaseHas('employee_tasks', [
            'id' => $task->id,
            'status' => 'pending',
            'completed_at' => null,
        ]);
    }

    public function test_task_completion_evidence_total_must_not_exceed_20_mb(): void
    {
        Storage::fake('public');
        $employee = $this->employee('Evidence Owner', 'evidence-owner@example.test');
        $user = User::factory()->create([
            'employee_id' => $employee->id,
            'email' => 'evidence.owner@example.test',
        ]);
        $task = EmployeeTask::create([
            'employee_id' => $employee->id,
            'created_by_user_id' => $user->id,
            'title' => 'Upload oversized evidence',
            'period_type' => 'daily',
            'period_start' => '2026-06-11',
            'period_end' => '2026-06-11',
            'priority' => 'normal',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($user)
            ->from(route('dashboard'))
            ->patch(route('employee-tasks.complete', $task), [
                'evidence_files' => [
                    UploadedFile::fake()->create('part-one.pdf', 12000, 'application/pdf'),
                    UploadedFile::fake()->create('part-two.pdf', 9000, 'application/pdf'),
                ],
            ]);

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHasErrors('evidence_files');
        $this->assertDatabaseHas('employee_tasks', [
            'id' => $task->id,
            'status' => 'pending',
            'completed_at' => null,
        ]);
    }

    private function employee(string $name, string $email): Employee
    {
        return Employee::create([
            'name' => $name,
            'email' => $email,
            'phone' => '08123456789',
            'status' => 'active',
        ]);
    }
}
