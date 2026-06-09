<?php

namespace Database\Seeders;

use App\Models\UserRole;
use Illuminate\Database\Seeder;

class UserRoleSeeder extends Seeder
{
    public function run(): void
    {
        UserRole::create(['user_id' => 1, 'role_id' => 2]);
        UserRole::create(['user_id' => 2, 'role_id' => 1]);
        UserRole::create(['user_id' => 3, 'role_id' => 4]);
    }
}
