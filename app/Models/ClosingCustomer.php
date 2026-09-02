<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClosingCustomer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'lead_id',
        'survey_customer_id',
        'customer_name',
        'customer_phone',
        'customer_email',
        'project_id',
        'project_stock_id',
        'closing_date',
        'amount',
        'payment_method',
        'status',
        'sales_person_id',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'closing_date' => 'date',
            'amount' => 'decimal:2',
        ];
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function surveyCustomer(): BelongsTo
    {
        return $this->belongsTo(SurveyCustomer::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function projectStock(): BelongsTo
    {
        return $this->belongsTo(ProjectStock::class);
    }

    public function salesPerson(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'sales_person_id');
    }
}
