<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeNotification extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'notifications';

    protected $fillable = ['company_id', 'department_id', 'division_id', 'section_id', 'work_location_id', 'user_ids', 'title', 'message', 'file', 'is_read', 'status'];

    protected function casts(): array
    {
        return [
            'user_ids' => 'array',
            'is_read' => 'boolean',
        ];
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
