<?php

namespace App\Http\Requests;

use App\Constants\Roles;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class AddUserRequest extends FormRequest
{
    public function authorize()
    {
        $userRole = Auth::user()->role[0]->name;
        return $userRole === Roles::ADMINISTRATOR_ALIAS || Roles::DEVELOPER_ALIAS;
    }

    public function rules()
    {
        return [
            'name' => 'required',
            'email' => 'required|string|email',
            'password' => 'required|string|min:8'
        ];
    }
}