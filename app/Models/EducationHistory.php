<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EducationHistory extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'employee_id',
        'education_type_id',
        'start_year',
        'end_year',
        'major',
        'degree',
        'notes',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function educationType()
    {
        return $this->belongsTo(EducationType::class);
    }
}
