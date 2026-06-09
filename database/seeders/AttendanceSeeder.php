<?php

namespace Database\Seeders;

use App\Models\Attendance;
use Illuminate\Database\Seeder;

class AttendanceSeeder extends Seeder
{
    public function run(): void
    {
        Attendance::create([
            'employee_id' => 1,
            'clock_in' => '2024-01-15 08:00:00',
            'clock_out' => '2024-01-15 17:00:00',
            'work_hours' => 8,
            'status' => 'present',
            'location_in' => ['longitude' => 106.8650, 'latitude' => -6.2088],
            'location_out' => ['longitude' => 106.8650, 'latitude' => -6.2088],
        ]);
    }
}
