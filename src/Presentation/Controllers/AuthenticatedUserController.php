<?php

declare(strict_types=1);

namespace PetMatch\Presentation\Controllers;

use PetMatch\Application\Auth\GetAuthenticatedUser;
use PetMatch\Application\Auth\ForbiddenException;
use PetMatch\Application\Auth\NotAuthenticatedException;
use PetMatch\Infrastructure\Http\Request;
use PetMatch\Presentation\Responses\JsonResponse;

final class AuthenticatedUserController
{
    public function __construct(
        private readonly GetAuthenticatedUser $getAuthenticatedUser,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        try {
            return JsonResponse::ok([
                'data' => $this->getAuthenticatedUser->execute(),
            ]);
        } catch (ForbiddenException $exception) {
            return JsonResponse::forbidden([
                'error' => $exception->getMessage(),
            ]);
        } catch (NotAuthenticatedException $exception) {
            return JsonResponse::unauthorized([
                'error' => $exception->getMessage(),
            ]);
        }
    }
}
