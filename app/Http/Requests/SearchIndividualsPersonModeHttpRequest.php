<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchIndividualsPersonModeHttpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'string',
            'surname' => 'string',
            'patronymic' => 'string',
            'date_birth' => 'date_format:d.m.Y'
        ];
    }

    public function messages(): array
    {
        return [
            'name.string' => 'Имя должно быть строкой!',
            'surname.string' => 'Фамилия должна быть строкой!',
            'patronymic.string' => 'Отчество должно быть строкой!',
            'date_birth.date_format' => 'Дата должна быть в формате дд-мм-гггг!',
        ];
    }
}
