<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nip',
        'nik',
        'npwp',
        'name',
        'email',
        'phone',
        'address',
        'birth_place',
        'birth_date',
        'join_date',
        'bpjs_kes',
        'bpjs_tk',
        'photo',
        'marital_status',
        'resign_date',
        'religion_id',
        'job_position_id',
        'company_id',
        'department_id',
        'division_id',
        'section_id',
        'work_location_id',
        'facility_ids',
        'blood_type',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'join_date' => 'date',
            'resign_date' => 'date',
            'facility_ids' => 'array',
        ];
    }

    public function religion()
    {
        return $this->belongsTo(Religion::class);
    }

    public function jobPosition()
    {
        return $this->belongsTo(JobPosition::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function workLocation()
    {
        return $this->belongsTo(WorkLocation::class);
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }

    public function families(): HasMany
    {
        return $this->hasMany(Family::class);
    }

    public function educationHistories(): HasMany
    {
        return $this->hasMany(EducationHistory::class);
    }

    public function workHistories(): HasMany
    {
        return $this->hasMany(WorkHistory::class);
    }

    public function salary(): HasOne
    {
        return $this->hasOne(Salary::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function leaveBalance(): HasOne
    {
        return $this->hasOne(LeaveBalance::class);
    }

    public function emergencyContacts(): HasMany
    {
        return $this->hasMany(EmergencyContact::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function leaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public function employeeShifts(): HasMany
    {
        return $this->hasMany(EmployeeShift::class);
    }
}
