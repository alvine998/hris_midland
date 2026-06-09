<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nip' => ['nullable', 'string', 'max:50'],
            'nik' => ['nullable', 'string', 'max:50'],
            'npwp' => ['nullable', 'string', 'max:50'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:500'],
            'birth_place' => ['nullable', 'string', 'max:255'],
            'birth_date' => ['nullable', 'date'],
            'join_date' => ['nullable', 'string', 'max:50'],
            'bpjs_kes' => ['nullable', 'string', 'max:50'],
            'bpjs_tk' => ['nullable', 'string', 'max:50'],
            'marital_status' => ['nullable', 'string', 'in:kawin,belum kawin,pisah'],
            'religion_id' => ['nullable', 'exists:religions,id'],
            'job_position_id' => ['nullable', 'exists:job_positions,id'],
            'company_id' => ['nullable', 'exists:companies,id'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'division_id' => ['nullable', 'exists:divisions,id'],
            'section_id' => ['nullable', 'exists:sections,id'],
            'work_location_id' => ['nullable', 'exists:work_locations,id'],
            'facility_ids' => ['nullable', 'array'],
            'facility_ids.*' => ['exists:facilities,id'],
            'blood_type' => ['nullable', 'string', 'max:10'],
            'status' => ['nullable', 'string', 'in:active,inactive'],
        ];
    }
}
