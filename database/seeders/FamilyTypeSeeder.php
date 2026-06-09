<?php

namespace Database\Seeders;

use App\Models\FamilyType;
use Illuminate\Database\Seeder;

class FamilyTypeSeeder extends Seeder
{
    public function run(): void
    {
        FamilyType::create([
            'name' => 'Spouse',
            'description' => 'Husband or Wife',
        ]);
    }
}
