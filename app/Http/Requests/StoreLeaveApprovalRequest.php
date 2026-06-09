<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLeaveApprovalRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'leave_request_id' => ['required', 'exists:leave_requests,id'],
            'approver_id' => ['nullable', 'exists:employees,id'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'status' => ['required', Rule::in(['pending', 'approved', 'rejected', 'cancelled'])],
        ];
    }
}
