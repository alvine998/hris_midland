<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeaveRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'employee_id',
        'leave_type_id',
        'employee_delegation_id',
        'title',
        'start_date',
        'end_date',
        'inclusive_days',
        'evidence',
        'reason',
        'notes',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'datetime',
            'end_date' => 'datetime',
        ];
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class);
    }

    public function delegatedEmployee()
    {
        return $this->belongsTo(Employee::class, 'employee_delegation_id');
    }

    public function approvals(): HasMany
    {
        return $this->hasMany(LeaveApproval::class);
    }
}
