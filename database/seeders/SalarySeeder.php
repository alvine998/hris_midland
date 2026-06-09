<?php

namespace Database\Seeders;

use App\Models\Salary;
use Illuminate\Database\Seeder;

class SalarySeeder extends Seeder
{
    public function run(): void
    {
        Salary::create([
            'employee_id' => 1,
            'basic_salary' => 5000000,
            'allowance' => 1000000,
            'bpjs_kes' => 150000,
            'bpjs_tk' => 200000,
            'tax_status' => 'TK/0',
            'bank_name' => 'Bank Mandiri',
            'bank_account_number' => '1234567890',
            'bank_account_name' => 'Budi Santoso',
        ]);
    }
}
