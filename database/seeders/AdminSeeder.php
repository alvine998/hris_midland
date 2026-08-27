<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@targetin.com'],
            [
                'name' => 'Super Admin',
                'password' => 'password',
                'status' => 'active',
            ]
        );

        $role = Role::where('name', 'Super Admin')->first();

        if ($role && ! $admin->userRoles()->where('role_id', $role->id)->exists()) {
            UserRole::create(['user_id' => $admin->id, 'role_id' => $role->id]);
        }
    }
}
