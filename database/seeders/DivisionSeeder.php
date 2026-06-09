<?php

namespace Database\Seeders;

use App\Models\Division;
use Illuminate\Database\Seeder;

class DivisionSeeder extends Seeder
{
    public function run(): void
    {
        Division::create(['name' => 'Recruitment', 'department_id' => 1]);
    }
}
