<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFamilyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'employee_id' => ['required', 'exists:employees,id'],
            'name' => ['required', 'string', 'max:255'],
            'relationship_id' => ['nullable', 'exists:relationships,id'],
            'family_type_id' => ['nullable', 'exists:family_types,id'],
            'phone' => ['nullable', 'string', 'max:50'],
            'status' => ['required', 'string', 'in:hidup,wafat,meninggal'],
        ];
    }
}
