<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreWorkLocationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
            'city' => ['nullable', 'string', 'max:255'],
            'province' => ['nullable', 'string', 'max:255'],
            'type' => ['required', 'string', 'in:head_office,branch,site'],
            'latitude' => ['nullable', 'string', 'max:100'],
            'longitude' => ['nullable', 'string', 'max:100'],
            'radius' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
