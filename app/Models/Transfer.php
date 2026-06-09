<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transfer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['employee_id', 'transfer_type_id', 'reason', 'transfer_from', 'transfer_to', 'status'];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function transferType()
    {
        return $this->belongsTo(TransferType::class);
    }
}
