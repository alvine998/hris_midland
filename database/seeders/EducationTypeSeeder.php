<?php

namespace Database\Seeders;

use App\Models\EducationType;
use Illuminate\Database\Seeder;

class EducationTypeSeeder extends Seeder
{
    public function run(): void
    {
        EducationType::create([
            'name' => 'Bachelor Degree',
        ]);
    }
}
