<?php

namespace Database\Seeders;

use App\Models\Section;
use Illuminate\Database\Seeder;

class SectionSeeder extends Seeder
{
    public function run(): void
    {
        Section::create(['name' => 'Talent Acquisition', 'division_id' => 1]);
    }
}
