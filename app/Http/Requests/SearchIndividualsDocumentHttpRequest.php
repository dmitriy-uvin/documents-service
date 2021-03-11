<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchIndividualsDocumentHttpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'inn_number' => 'string',
            'snils_number' => 'string',
            'passport_number' => 'string',
        ];
    }

    public function messages(): array
    {
        return [
            'inn_number.string' => 'ИНН должен быть строкой!',
            'snils_number.string' => 'СНИЛС должен быть строкой!',
            'passport_number.string' => 'Паспорт должен быть строкой!',
        ];
    }
}
