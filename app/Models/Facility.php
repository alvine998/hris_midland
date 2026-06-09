<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Facility extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['facility_criteria_ids', 'name', 'description'];

    protected function casts(): array
    {
        return ['facility_criteria_ids' => 'array'];
    }
}
