<?php

namespace Database\Seeders;

use App\Models\LeaveBalance;
use Illuminate\Database\Seeder;

class LeaveBalanceSeeder extends Seeder
{
    public function run(): void
    {
        LeaveBalance::create([
            'employee_id' => 1,
            'total' => 12,
            'used' => 2,
            'remaining' => 10,
            'extra' => 0,
        ]);
    }
}
