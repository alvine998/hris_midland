<?php

namespace Database\Seeders;

use App\Models\WorkHistory;
use Illuminate\Database\Seeder;

class WorkHistorySeeder extends Seeder
{
    public function run(): void
    {
        WorkHistory::create([
            'employee_id' => 1,
            'company_name' => 'PT Teknologi Maju',
            'position' => 'Junior Developer',
            'description' => 'Worked on web application development',
            'start_date' => '2015-01-01',
            'end_date' => '2019-12-31',
        ]);
    }
}
