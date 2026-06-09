<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmergencyContact extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'employee_id',
        'name',
        'address',
        'phone',
        'email',
        'relationship_id',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function relationship()
    {
        return $this->belongsTo(Relationship::class);
    }
}
