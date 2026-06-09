<?php

namespace Database\Seeders;

use App\Models\WorkLocation;
use Illuminate\Database\Seeder;

class WorkLocationSeeder extends Seeder
{
    public function run(): void
    {
        WorkLocation::create([
            'name' => 'Head Office Jakarta',
            'address' => 'Jl. Sudirman No. 1, Jakarta Pusat',
            'city' => 'Jakarta Pusat',
            'province' => 'DKI Jakarta',
            'type' => 'head_office',
        ]);
    }
}
