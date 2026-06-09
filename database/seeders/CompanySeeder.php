<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        Company::create([
            'name' => 'Midland Corporation',
            'email' => 'info@midlandcorp.com',
            'phone' => '021-5550199',
            'address' => 'Jl. Sudirman No. 1, Jakarta Pusat',
            'status' => 'active',
        ]);
    }
}
