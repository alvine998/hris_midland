<?php

namespace Database\Seeders;

use App\Models\Contract;
use Illuminate\Database\Seeder;

class ContractSeeder extends Seeder
{
    public function run(): void
    {
        Contract::create([
            'name' => 'Employment Contract',
            'employee_id' => 1,
            'contract_number' => 'CTR-001',
            'contract_type_id' => 1,
            'start_date' => '2024-01-01',
            'end_date' => null,
            'files' => null,
        ]);
    }
}
