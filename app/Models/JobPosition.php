<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobPosition extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'level_id'];

    public function level(): BelongsTo
    {
        return $this->belongsTo(Level::class);
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }
}
