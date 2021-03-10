<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchIndividualsPersonModeHttpRequest extends FormRequest
{
    public function authorize()
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
}
