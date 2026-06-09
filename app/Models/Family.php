<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Family extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'employee_id',
        'name',
        'relationship_id',
        'family_type_id',
        'phone',
        'status',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function familyType()
    {
        return $this->belongsTo(FamilyType::class);
    }

    public function relationship()
    {
        return $this->belongsTo(Relationship::class);
    }
}
