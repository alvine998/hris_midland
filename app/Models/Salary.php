<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Salary extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'employee_id',
        'basic_salary',
        'allowance',
        'bpjs_kes',
        'bpjs_tk',
        'tax_status',
        'bank_name',
        'bank_account_number',
        'bank_account_name',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
