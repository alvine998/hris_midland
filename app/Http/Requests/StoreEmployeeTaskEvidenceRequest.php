<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreEmployeeTaskEvidenceRequest extends FormRequest
{
    private const MAX_TOTAL_KILOBYTES = 20480;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'evidence_files' => ['required', 'array', 'min:1'],
            'evidence_files.*' => ['required', 'file', 'max:'.self::MAX_TOTAL_KILOBYTES],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $totalKilobytes = collect($this->file('evidence_files', []))
                    ->sum(fn ($file): float => $file->getSize() / 1024);

                if ($totalKilobytes > self::MAX_TOTAL_KILOBYTES) {
                    $validator->errors()->add('evidence_files', 'The total evidence file size must not be greater than 20 MB.');
                }
            },
        ];
    }
}
