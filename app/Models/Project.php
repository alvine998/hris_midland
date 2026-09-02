<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'company_id',
        'location',
        'status',
        'start_date',
        'end_date',
        'budget',
        'progress',
        'manager_id',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'budget' => 'decimal:2',
            'progress' => 'integer',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'manager_id');
    }

    public function stocks(): HasMany
    {
        return $this->hasMany(ProjectStock::class);
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }

    public function surveyCustomers(): HasMany
    {
        return $this->hasMany(SurveyCustomer::class);
    }

    public function closingCustomers(): HasMany
    {
        return $this->hasMany(ClosingCustomer::class);
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['planning', 'ongoing']);
    }
}
