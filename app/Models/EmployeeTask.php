<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeTask extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'employee_id',
        'created_by_user_id',
        'assigned_by_user_id',
        'title',
        'description',
        'period_type',
        'period_start',
        'period_end',
        'priority',
        'status',
        'completed_at',
        'evidence_files',
    ];

    protected function casts(): array
    {
        return [
            'period_start' => 'date',
            'period_end' => 'date',
            'completed_at' => 'datetime',
            'evidence_files' => 'array',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by_user_id');
    }
}
