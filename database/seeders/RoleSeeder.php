<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::create(['name' => 'Super Admin', 'description' => 'Full system access', 'rbac' => ['*']]);
        Role::create(['name' => 'Admin', 'description' => 'Administrative access', 'rbac' => ['*']]);
        Role::create(['name' => 'HR Staff', 'description' => 'HR module access', 'rbac' => ['employee.view', 'employee.create', 'employee.edit']]);
        Role::create(['name' => 'Employee', 'description' => 'Basic employee access', 'rbac' => ['employee.view']]);
    }
}
