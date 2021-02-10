<?php

namespace App\Http\Controllers;

use App\Constants\Roles;
use App\Exceptions\User\DeleteDeveloperException;
use App\Exceptions\User\DeleteUserWithSameRoleException;
use App\Exceptions\User\DeleteYourselfException;
use App\Exceptions\User\UserNotFoundException;
use App\Exceptions\User\UserWithEmailAlreadyExistsException;
use App\Http\Requests\AddUserRequest;
use App\Http\Requests\CreateManagerRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

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

    public function getAllUsers()
    {
        $users = User::all();
        return response()->json($users);
    }

    public function usersList()
    {
        return view('users');
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
            throw new UserWithEmailAlreadyExistsException();
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

        return redirect()->route('editor');
    }

    public function deleteUser(string $id)
    {
        $user = User::find($id);
        $user->is_blocked = true;
        $user->save();

//        if (!$user) {
//            throw new UserNotFoundException();
//        }
//
//        if ((int)$id === (int)Auth::id()) {
//            throw new DeleteYourselfException();
//        }
//
//        if ($user->getRole()->alias === Auth::user()->getRole()->alias) {
//            throw new DeleteUserWithSameRoleException();
//        }
//
//        if ($user->getRole()->alias === Roles::DEVELOPER_ALIAS && Auth::user()->getRole()->alias === Roles::ADMINISTRATOR_ALIAS) {
//            throw new DeleteDeveloperException();
//        }
//
//        $user->delete();

//        return redirect()->route('users');
    }

    public function blockUser(string $id)
    {

    }
}
