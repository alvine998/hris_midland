<?php

namespace Database\Seeders;

use App\Models\EducationHistory;
use Illuminate\Database\Seeder;

class EducationHistorySeeder extends Seeder
{
    public function run(): void
    {
        EducationHistory::create([
            'employee_id' => 1,
            'education_type_id' => 1,
            'start_year' => '2010',
            'end_year' => '2014',
            'notes' => 'Bachelor of Computer Science',
        ]);
    }
}
