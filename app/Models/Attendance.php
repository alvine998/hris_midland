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
        'selfie_in',
        'selfie_out',
        'gps_accuracy_in',
        'gps_accuracy_out',
        'is_mock_location_in',
        'is_mock_location_out',
        'check_in_method',
        'ip_address_in',
        'ip_address_out',
    ];

    protected function casts(): array
    {
        return [
            'clock_in' => 'datetime',
            'clock_out' => 'datetime',
            'location_in' => 'array',
            'location_out' => 'array',
            'gps_accuracy_in' => 'decimal:2',
            'gps_accuracy_out' => 'decimal:2',
            'is_mock_location_in' => 'boolean',
            'is_mock_location_out' => 'boolean',
        ];
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
