<?php

namespace App\Http\Controllers;

use App\Constants\Roles;
use App\Exceptions\User\UserAlreadyExistsException;
use App\Http\Requests\AddUserRequest;
use App\Http\Requests\CreateManagerRequest;
use App\Models\Role;
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

    public function usersList()
    {
        $users = User::all();

        return view('users', [
            'users' => $users
        ]);
    }

    public function getUserById(string $id)
    {
        $user = User::find((int)$id);

        return view('user', [
            'user' => $user
        ]);
    }


    public function createManager(CreateManagerRequest $request)
    {
        $user = User::where('email', '=', $request->email)->get()->first();
        if ($user) {
            throw new UserAlreadyExistsException();
        }

        $user = new User();
        $user->first_name = $request->first_name;
        $user->second_name = $request->second_name;
        $user->patronymic = $request->patronymic;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->unhashed_password = $request->password;
        $user->department = $request->department;
        $user->save();
        $user->role()->attach(Role::where('alias', '=', Roles::MANAGER_ALIAS)->get()->first());

//        return redirect()->route('editor');
    }

}
