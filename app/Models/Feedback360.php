<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Feedback360 extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'feedback360s';

    protected $fillable = [
        'employee_id',
        'reviewer_employee_id',
        'reviewer_name',
        'reviewer_type',
        'period',
        'communication_score',
        'teamwork_score',
        'leadership_score',
        'technical_score',
        'overall_score',
        'strengths',
        'improvements',
        'comments',
        'status',
        'reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'reviewed_at' => 'date',
        ];
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function reviewerEmployee()
    {
        return $this->belongsTo(Employee::class, 'reviewer_employee_id');
    }
}
