<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contract extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'employee_id',
        'contract_number',
        'contract_type_id',
        'start_date',
        'end_date',
        'files',
    ];

    protected function casts(): array
    {
        return [
            'files' => 'array',
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function contractType()
    {
        return $this->belongsTo(ContractType::class);
    }
}
