<?php

namespace App\Actions\User;

use App\Repositories\User\UserRepositoryInterface;

class GetUsersCollectionAction
{
    private UserRepositoryInterface $userRepository;

    public function execute(): GetUsersCollectionResponse
    {
        $users = $this->userRepository->getAll();

        return new GetUsersCollectionResponse($users);
    }
}
