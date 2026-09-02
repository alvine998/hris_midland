<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectStock extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'project_id',
        'unit_code',
        'type',
        'block',
        'land_size',
        'building_size',
        'bedrooms',
        'bathrooms',
        'price',
        'status',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'land_size' => 'decimal:2',
            'building_size' => 'decimal:2',
            'price' => 'decimal:2',
            'bedrooms' => 'integer',
            'bathrooms' => 'integer',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }
}
