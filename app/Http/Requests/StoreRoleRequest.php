<?php

namespace App\Http\Requests;

use App\Services\RbacPermissionService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRoleRequest extends FormRequest
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
            'rbac' => ['array'],
            'rbac.*' => ['string', Rule::in(RbacPermissionService::keys())],
        ];
    }

    protected function prepareForValidation(): void
    {
        $permissions = collect($this->input('rbac', []))
            ->filter()
            ->unique()
            ->values();

        if ($permissions->contains('*')) {
            $permissions = collect(['*']);
        }

        $this->merge([
            'rbac' => $permissions->all(),
        ]);
    }
}
