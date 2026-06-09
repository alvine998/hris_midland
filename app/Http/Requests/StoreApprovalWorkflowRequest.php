<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreApprovalWorkflowRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'type' => ['nullable', 'string', 'max:255'],
            'company_id' => ['nullable', 'exists:companies,id'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'division_id' => ['nullable', 'exists:divisions,id'],
            'section_id' => ['nullable', 'exists:sections,id'],
            'work_location_id' => ['nullable', 'exists:work_locations,id'],
            'employee_ids' => ['array'],
            'employee_ids.*' => ['exists:employees,id'],
        ];
    }
}
