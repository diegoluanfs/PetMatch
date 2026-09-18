<?php

declare(strict_types=1);

namespace PetMatch\Presentation\Controllers;

use PetMatch\Application\Auth\ListUserVerifications;
use PetMatch\Application\Auth\NotAuthenticatedException;
use PetMatch\Infrastructure\Http\Request;
use PetMatch\Presentation\Responses\JsonResponse;

final class ListUserVerificationsController
{
    public function __construct(
        private readonly ListUserVerifications $listUserVerifications,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        try {
            return JsonResponse::ok([
                'data' => $this->listUserVerifications->execute(),
            ]);
        } catch (NotAuthenticatedException $exception) {
            return JsonResponse::unauthorized([
                'error' => $exception->getMessage(),
            ]);
        }
    }
}
