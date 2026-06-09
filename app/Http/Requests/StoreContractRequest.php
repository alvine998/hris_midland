<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreContractRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'employee_id' => ['required', 'exists:employees,id'],
            'contract_number' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'contract_type_id' => ['required', 'exists:contract_types,id'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'files' => ['nullable', 'string'],
        ];
    }
}
