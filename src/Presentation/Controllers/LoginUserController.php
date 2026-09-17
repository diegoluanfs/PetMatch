<?php

declare(strict_types=1);

namespace PetMatch\Presentation\Controllers;

use PetMatch\Application\Auth\InvalidCredentialsException;
use PetMatch\Application\Auth\LoginUser;
use PetMatch\Infrastructure\Http\Request;
use PetMatch\Infrastructure\Security\SessionManager;
use PetMatch\Presentation\Requests\Auth\LoginUserRequest;
use PetMatch\Presentation\Responses\JsonResponse;

final class LoginUserController
{
    public function __construct(
        private readonly LoginUser $loginUser,
        private readonly SessionManager $sessionManager,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        try {
            $input = LoginUserRequest::fromRequest($request);
            $user = $this->loginUser->execute($input->email, $input->password);

            $this->sessionManager->regenerateId();
            $this->sessionManager->setUserId($user['id']);
            $this->sessionManager->setUserRole($user['role']);

            return JsonResponse::ok([
                'data' => $user,
            ]);
        } catch (InvalidCredentialsException $exception) {
            return JsonResponse::unauthorized([
                'error' => $exception->getMessage(),
            ]);
        }
    }
}
