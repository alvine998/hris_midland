<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeaveApproval extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'leave_request_id',
        'approver_id',
        'notes',
        'status',
    ];

    public function leaveRequest()
    {
        return $this->belongsTo(LeaveRequest::class);
    }

    public function approver()
    {
        return $this->belongsTo(Employee::class, 'approver_id');
    }
}
