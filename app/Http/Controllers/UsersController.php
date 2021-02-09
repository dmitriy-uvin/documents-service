<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddUserRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{
    public function addUser(AddUserRequest $request)
    {
        if (!is_null(User::where('email', '=', $request->email)->get())) {

        }

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->unhashed_password = $request->password;
        $user->password = Hash::make($request->password);
        $user->department = $request->department;
        $user->save();

        $user->role()->attach($request->role_id);

        return redirect()
            ->route('home')
            ->with('user-added', 'Пользователь был создан!');
    }
}
