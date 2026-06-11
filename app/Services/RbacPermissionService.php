<?php

namespace App\Services;

use Illuminate\Support\Collection;

class RbacPermissionService
{
    public static function groups(): array
    {
        return [
            'System' => [
                '*' => 'Full system access',
            ],
            'Dashboard' => [
                'dashboard.view' => 'View dashboard',
            ],
            'Employees' => [
                'employee.view' => 'View employees',
                'employee.create' => 'Create employees',
                'employee.edit' => 'Edit employees',
                'employee.delete' => 'Delete employees',
            ],
            'Organization' => [
                'organization.view' => 'View organization data',
                'organization.create' => 'Create organization data',
                'organization.edit' => 'Edit organization data',
                'organization.delete' => 'Delete organization data',
            ],
            'References' => [
                'reference.view' => 'View reference data',
                'reference.create' => 'Create reference data',
                'reference.edit' => 'Edit reference data',
                'reference.delete' => 'Delete reference data',
            ],
            'User Roles' => [
                'user-role.view' => 'View user role assignments',
                'user-role.create' => 'Assign user roles',
                'user-role.edit' => 'Edit user role assignments',
                'user-role.delete' => 'Remove user role assignments',
            ],
            'Feedback 360' => [
                'feedback360.view' => 'View 360 feedback',
                'feedback360.create' => 'Give 360 feedback',
                'feedback360.edit' => 'Edit 360 feedback',
                'feedback360.delete' => 'Delete 360 feedback',
            ],
            'Contracts' => [
                'contract.view' => 'View contracts',
                'contract.create' => 'Create contracts',
                'contract.edit' => 'Edit contracts',
                'contract.delete' => 'Delete contracts',
            ],
            'Reports' => [
                'report.performance.view' => 'View performance report',
                'report.attendance.view' => 'View attendance report',
                'report.leave.view' => 'View leave report',
            ],
            'Settings' => [
                'settings.view' => 'View settings',
                'settings.edit' => 'Edit settings',
            ],
        ];
    }

    public static function keys(): array
    {
        return Collection::make(self::groups())
            ->flatMap(fn (array $permissions) => array_keys($permissions))
            ->values()
            ->all();
    }
}
