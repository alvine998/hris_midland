<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Database\Seeder;

class UserRoleSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::where('name', 'Admin')->first();
        $superAdminRole = Role::where('name', 'Super Admin')->first();
        $staffRole = Role::where('name', 'Staff')->first();

        $admin = User::where('email', 'admin@example.com')->first();
        $superAdmin = User::where('email', 'superadmin@example.com')->first();
        $staff = User::where('email', 'staff@example.com')->first();

        if ($admin && $adminRole) {
            UserRole::create(['user_id' => $admin->id, 'role_id' => $adminRole->id]);
        }

        if ($superAdmin && $superAdminRole) {
            UserRole::create(['user_id' => $superAdmin->id, 'role_id' => $superAdminRole->id]);
        }

        if ($staff && $staffRole) {
            UserRole::create(['user_id' => $staff->id, 'role_id' => $staffRole->id]);
        }
    }
}
