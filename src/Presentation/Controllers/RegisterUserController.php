<?php

declare(strict_types=1);

namespace PetMatch\Presentation\Controllers;

use PetMatch\Application\Auth\EmailAlreadyRegisteredException;
use InvalidArgumentException;
use PetMatch\Application\Auth\RegisterUser;
use PetMatch\Infrastructure\Http\Request;
use PetMatch\Presentation\Requests\Auth\RegisterUserRequest;
use PetMatch\Presentation\Responses\JsonResponse;

final class RegisterUserController
{
    public function __construct(
        private readonly RegisterUser $registerUser,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        try {
            $input = RegisterUserRequest::fromRequest($request);
            $user = $this->registerUser->execute($input->name, $input->email, $input->password);

            return JsonResponse::created([
                'data' => $user,
            ]);
        } catch (EmailAlreadyRegisteredException $exception) {
            return JsonResponse::conflict([
                'error' => $exception->getMessage(),
            ]);
        } catch (InvalidArgumentException $exception) {
            return JsonResponse::unprocessableEntity([
                'error' => $exception->getMessage(),
            ]);
        }
    }
}
