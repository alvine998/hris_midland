<?php

namespace App\Services;

use App\Models\EmployeeTask;
use App\Models\User;

class EmployeeTaskService
{
    public function create(array $data, User $user, bool $canAssign): EmployeeTask
    {
        $employeeId = $this->employeeId($data, $user, $canAssign);
        $data['employee_id'] = $employeeId;
        $data['created_by_user_id'] = $user->id;
        $data['assigned_by_user_id'] = $canAssign && $employeeId !== $user->employee_id ? $user->id : null;
        $data['completed_at'] = ($data['status'] ?? null) === 'completed' ? now() : null;

        return EmployeeTask::create($data);
    }

    public function update(EmployeeTask $task, array $data, User $user, bool $canAssign): EmployeeTask
    {
        $employeeId = $this->employeeId($data, $user, $canAssign);
        $data['employee_id'] = $employeeId;

        if ($canAssign && $employeeId !== $task->employee_id && $employeeId !== $user->employee_id) {
            $data['assigned_by_user_id'] = $user->id;
        }

        if (($data['status'] ?? null) === 'completed' && $task->completed_at === null) {
            $data['completed_at'] = now();
        }

        if (($data['status'] ?? null) !== 'completed') {
            $data['completed_at'] = null;
        }

        $task->update($data);

        return $task;
    }

    private function employeeId(array $data, User $user, bool $canAssign): int
    {
        if ($canAssign && filled($data['employee_id'] ?? null)) {
            return (int) $data['employee_id'];
        }

        abort_if(! $user->employee_id, 422, 'Your user account is not linked to an employee profile.');

        return (int) $user->employee_id;
    }
}
