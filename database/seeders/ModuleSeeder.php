<?php

namespace Database\Seeders;

use App\Models\Module;
use Illuminate\Database\Seeder;

class ModuleSeeder extends Seeder
{
    public function run(): void
    {
        Module::create(['name' => 'Employee Management']);
        Module::create(['name' => 'Payroll']);
        Module::create(['name' => 'Attendance']);
        Module::create(['name' => 'User Management']);
    }
}
