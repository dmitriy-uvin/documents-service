<?php

namespace App\Actions\User;

use App\Exceptions\User\UserWithEmailAlreadyExistsException;
use App\Models\Role;
use App\Models\User;
use App\Repositories\User\Criterion\EmailCriterion;
use App\Repositories\User\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;

class AddUserAction
{
    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function execute(AddUserRequest $request): void
    {
        $criteria = [new EmailCriterion($request->getEmail())];
        $users = $this->userRepository->findByCriteria($criteria);

        if ($users->count()) {
            throw new UserWithEmailAlreadyExistsException();
        }

        $user = new User([
            'name' => $request->getName(),
            'email' => $request->getEmail(),
            'unhashed_password' => $request->getPassword(),
            'password' => Hash::make($request->getPassword()),
            'department' => $request->getDepartment(),
        ]);
        $user = $this->userRepository->save($user);
        $role = Role::findOrFail($request->getRoleId());
        $this->userRepository->attachRole($user, $role);
    }
}
