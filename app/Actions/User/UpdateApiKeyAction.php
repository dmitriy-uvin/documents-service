<?php

namespace App\Actions\User;

use App\Exceptions\Api\ApiKeyAlreadyExistsException;
use App\Repositories\User\Criterion\ApiKeyCriterion;
use App\Repositories\User\UserRepositoryInterface;

final class UpdateApiKeyAction
{
    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function execute(UpdateApiKeyRequest $request): UpdateApiKeyResponse
    {
        $user = $this->userRepository->findByCriteria(new ApiKeyCriterion($request->getToken()));

        if (count($user)) {
            throw new ApiKeyAlreadyExistsException();
        }

        $user = $this->userRepository->me();

        $user->api_key = $request->getToken();
        $user = $this->userRepository->save($user);

        return new UpdateApiKeyResponse($user);
    }
}
