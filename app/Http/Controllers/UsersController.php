<?php

namespace App\Http\Controllers;

use App\Actions\User\AddUserAction;
use App\Actions\User\BlockUserByIdAction;
use App\Actions\User\BlockUserByIdRequest;
use App\Actions\User\DeleteUserByIdAction;
use App\Actions\User\DeleteUserByIdRequest;
use App\Actions\User\GetUserByIdAction;
use App\Actions\User\GetUserByIdRequest;
use App\Actions\User\GetUsersCollectionAction;
use App\Http\Requests\CreateUserRequest;

class UsersController extends Controller
{
    private AddUserAction $addUserAction;
    private GetUsersCollectionAction $getUsersCollectionAction;
    private GetUserByIdAction $getUserByIdAction;
    private DeleteUserByIdAction $deleteUserByIdAction;
    private BlockUserByIdAction $blockUserByIdAction;

    public function __construct(
        AddUserAction $addUserAction,
        GetUsersCollectionAction $getUsersCollectionAction,
        GetUserByIdAction $getUserByIdAction,
        DeleteUserByIdAction $deleteUserByIdAction,
        BlockUserByIdAction $blockUserByIdAction
    ) {
        $this->addUserAction = $addUserAction;
        $this->getUsersCollectionAction = $getUsersCollectionAction;
        $this->getUserByIdAction = $getUserByIdAction;
        $this->deleteUserByIdAction = $deleteUserByIdAction;
        $this->blockUserByIdAction = $blockUserByIdAction;
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
        $user = $this->getUserByIdAction->execute(
            new GetUserByIdRequest((int)$id)
        );

        return view('user', [
            'user' => $user
        ]);
    }


    public function createUser(CreateUserRequest $request): \Illuminate\Http\RedirectResponse
    {
        $this->addUserAction->execute(
            new \App\Actions\User\AddUserRequest(
                $request->first_name,
                $request->second_name,
                $request->patronymic,
                $request->email,
                $request->password,
                $request->department,
                $request->role
            )
        );

        return redirect()->route('editor');
    }

    public function deleteUser(string $id): \Illuminate\Http\JsonResponse
    {
        $this->deleteUserByIdAction->execute(
            new DeleteUserByIdRequest((int)$id)
        );

        return response()->json();
    }

    public function changeUserBlockStatus(string $id): \Illuminate\Http\JsonResponse
    {
        $response = $this->blockUserByIdAction->execute(
            new BlockUserByIdRequest((int)$id)
        );

        return response()->json($response->getUser());
    }
}
