<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeaveSetting extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['company_id', 'is_advance_leave', 'max_advance_leave', 'is_rollover', 'max_rollover'];

    protected function casts(): array
    {
        return [
            'is_advance_leave' => 'boolean',
            'is_rollover' => 'boolean',
        ];
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
