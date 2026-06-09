<?php

namespace Database\Seeders;

use App\Models\Employee;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        Employee::create([
            'nip' => 'EMP-001',
            'nik' => '3273010101990001',
            'name' => 'Budi Santoso',
            'email' => 'budi@midlandcorp.com',
            'phone' => '0812-3456-7890',
            'address' => 'Jl. Merdeka No. 10, Jakarta',
            'birth_place' => 'Jakarta',
            'birth_date' => '1990-01-01',
            'join_date' => '2024-01-01',
            'marital_status' => 'kawin',
            'religion_id' => 1,
            'job_position_id' => 1,
            'company_id' => 1,
            'department_id' => 1,
            'division_id' => 1,
            'section_id' => 1,
            'work_location_id' => 1,
            'status' => 'active',
        ]);
    }
}
