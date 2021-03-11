<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchIndivDocumentModeCollectionHttpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'individuals' => 'required|array',
            'individuals.*.inn_number' => 'string',
            'individuals.*.snils_number' => 'string',
            'individuals.*.passport_number' => 'string',
        ];
    }

    public function messages(): array
    {
        return [
            'individuals.*.inn_number.string' => 'ИНН должен быть строкой!',
            'individuals.*.snils_number.string' => 'СНИЛС должен быть строкой!',
            'individuals.*.passport_number.string' => 'Паспорт должен быть строкой!',
        ];
    }
}
