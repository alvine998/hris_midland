<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attendance extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'employee_id',
        'clock_in',
        'clock_out',
        'work_hours',
        'status',
        'location_in',
        'location_out',
    ];

    protected function casts(): array
    {
        return [
            'clock_in' => 'datetime',
            'clock_out' => 'datetime',
            'location_in' => 'array',
            'location_out' => 'array',
        ];
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
