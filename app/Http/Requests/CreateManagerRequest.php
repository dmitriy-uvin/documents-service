<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateManagerRequest extends FormRequest
{
    public function rules()
    {
        return [
            'first_name' => 'required|string',
            'second_name' => 'required|string',
            'patronymic' => 'required|string',
            'email' => 'required|string|email',
            'password' => 'required|string|min:8',
        ];
    }
}
