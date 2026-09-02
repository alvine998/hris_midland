<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SurveyCustomer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'lead_id',
        'project_id',
        'project_stock_id',
        'customer_name',
        'customer_phone',
        'customer_email',
        'survey_date',
        'surveyor_id',
        'rating',
        'interest_level',
        'feedback',
        'next_action',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'survey_date' => 'date',
            'rating' => 'integer',
        ];
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function projectStock(): BelongsTo
    {
        return $this->belongsTo(ProjectStock::class);
    }

    public function surveyor(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'surveyor_id');
    }
}
