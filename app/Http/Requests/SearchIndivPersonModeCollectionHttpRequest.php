<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchIndivPersonModeCollectionHttpRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'individuals' => 'required|array',
            'individuals.*.name' => 'string',
            'individuals.*.surname' => 'string',
            'individuals.*.patronymic' => 'string',
            'individuals.*.date_birth' => 'date_format:d-m-Y',
        ];
    }

    public function messages(): array
    {
        return [
            'individuals.*.name.string' => 'Имя должно быть строкой!',
            'individuals.*.surname.string' => 'Фамилия должна быть строкой!',
            'individuals.*.patronymic.string' => 'Отчество должно быть строкой!',
            'individuals.*.date_birth.date_format' => 'Дата должна быть в формате дд-мм-гггг!',
        ];
    }
}
