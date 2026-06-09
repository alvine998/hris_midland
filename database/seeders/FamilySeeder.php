<?php

namespace Database\Seeders;

use App\Models\Family;
use Illuminate\Database\Seeder;

class FamilySeeder extends Seeder
{
    public function run(): void
    {
        Family::create([
            'employee_id' => 1,
            'name' => 'Sari Dewi',
            'family_type_id' => 1,
            'phone' => '0812-9876-5432',
            'status' => 'hidup',
        ]);
    }
}
