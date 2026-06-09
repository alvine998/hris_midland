<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkLocation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'address',
        'city',
        'province',
        'type',
        'latitude',
        'longitude',
        'radius',
    ];

    protected function casts(): array
    {
        return ['radius' => 'integer'];
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }
}
