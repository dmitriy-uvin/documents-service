<?php

namespace App\Http\Controllers;

use App\Actions\User\AddUserAction;
use App\Actions\User\GetUsersCollectionAction;
use App\Constants\Roles;
use App\Exceptions\User\BlockDeveloperException;
use App\Exceptions\User\BlockUserWithSameRoleException;
use App\Exceptions\User\BlockYourselfException;
use App\Exceptions\User\DeleteDeveloperException;
use App\Exceptions\User\DeleteUserWithSameRoleException;
use App\Exceptions\User\DeleteYourselfException;
use App\Exceptions\User\UserNotFoundException;
use App\Exceptions\User\UserWithEmailAlreadyExistsException;
use App\Http\Requests\AddUserRequest;
use App\Http\Requests\CreateUserRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{
    private AddUserAction $addUserAction;
    private GetUsersCollectionAction $getUsersCollectionAction;

    public function __construct(
        AddUserAction $addUserAction,
        GetUsersCollectionAction $getUsersCollectionAction
    ) {
        $this->addUserAction = $addUserAction;
        $this->getUsersCollectionAction = $getUsersCollectionAction;
    }

    public function addUser(AddUserRequest $request): \Illuminate\Http\RedirectResponse
    {
        $this->addUserAction->execute(
            new \App\Actions\User\AddUserRequest(
                $request->name,
                $request->email,
                $request->password,
                $request->department,
                $request->role_id
            )
        );

        return redirect()
            ->route('home')
            ->with('user-added', 'Пользователь был создан!');
    }

    public function getAllUsers(): \Illuminate\Http\JsonResponse
    {
        $users = $this->getUsersCollectionAction->execute()->getUsers();

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


    public function createUser(CreateUserRequest $request): \Illuminate\Http\RedirectResponse
    {
        $user = User::where('email', '=', $request->email)
            ->get()
            ->first();

        if ($user) {
            throw new UserWithEmailAlreadyExistsException();
        }

        $user = User::create([
            'first_name' => $request->first_name,
            'second_name' => $request->second_name,
            'patronymic' => $request->patronymic,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'unhashed_password' => $request->password,
            'department' => $request->department,
        ]);

        $user
            ->role()
            ->attach(Role::where('alias', '=', $request->role)
                ->get()
                ->first()
            );

        return redirect()->route('editor');
    }

    public function deleteUser(string $id): void
    {
        $user = User::find($id);

        $this->isAvailableToBlockOrDeleteUser($user, $id, 'delete');

        $user->delete();
    }

    public function changeUserBlockStatus(string $id): void
    {
        $user = User::find($id);

        $this->isAvailableToBlockOrDeleteUser($user, $id, 'block');

        $user->is_blocked = !$user->is_blocked;
        $user->save();
    }

    protected function isAvailableToBlockOrDeleteUser($user, $id, string $action): void
    {
        if (!$user) {
            throw new UserNotFoundException();
        }

        $authUser = Auth::user();

        if (!$authUser) {
            throw new AuthenticationException();
        }

        if ((int)$id === (int)$authUser->id) {
            if ($action === 'delete') {
                throw new DeleteYourselfException();
            }
            if ($action === 'block') {
                throw new BlockYourselfException();
            }
        }

        if ($user->getRole()->alias === $authUser->getRole()->alias) {
            if ($action === 'delete') {
                throw new DeleteUserWithSameRoleException();
            }
            if ($action === 'block') {
                throw new BlockUserWithSameRoleException();
            }
        }

        if (
            $user->getRole()->alias === Roles::DEVELOPER_ALIAS
            && $authUser->getRole()->alias === Roles::ADMINISTRATOR_ALIAS
        ) {
            if ($action === 'delete') {
                throw new DeleteDeveloperException();
            }
            if ($action === 'block') {
                throw new BlockDeveloperException();
            }
        }
    }
}
