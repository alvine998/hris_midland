<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            CompanySeeder::class,
            WorkLocationSeeder::class,
            DepartmentSeeder::class,
            DivisionSeeder::class,
            SectionSeeder::class,
            LevelSeeder::class,
            ReligionSeeder::class,
            JobPositionSeeder::class,
            ModuleSeeder::class,
            RoleSeeder::class,
            ContractTypeSeeder::class,
            EducationTypeSeeder::class,
            FamilyTypeSeeder::class,
            EmployeeSeeder::class,
            ContractSeeder::class,
            EducationHistorySeeder::class,
            FamilySeeder::class,
            WorkHistorySeeder::class,
            SalarySeeder::class,
            AttendanceSeeder::class,
            LeaveBalanceSeeder::class,
            NotificationSeeder::class,
        ]);

        $users = [
            ['name' => 'Admin', 'email' => 'admin@example.com'],
            ['name' => 'Super Admin', 'email' => 'superadmin@example.com'],
            ['name' => 'Staff', 'email' => 'staff@example.com'],
        ];

        foreach ($users as $user) {
            User::factory()->create([
                'name' => $user['name'],
                'email' => $user['email'],
                'password' => bcrypt('admin1234'),
            ]);
        }

        $this->call([
            UserRoleSeeder::class,
            EmployeeTaskSeeder::class,
        ]);
    }
}
