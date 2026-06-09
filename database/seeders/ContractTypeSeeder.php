<?php

namespace Database\Seeders;

use App\Models\ContractType;
use Illuminate\Database\Seeder;

class ContractTypeSeeder extends Seeder
{
    public function run(): void
    {
        ContractType::create([
            'name' => 'Permanent',
            'description' => 'Full-time permanent employment',
        ]);
    }
}
