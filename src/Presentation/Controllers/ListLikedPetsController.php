<?php

declare(strict_types=1);

namespace PetMatch\Presentation\Controllers;

use PetMatch\Application\Auth\NotAuthenticatedException;
use PetMatch\Application\Engagement\ListLikedPets;
use PetMatch\Infrastructure\Http\Request;
use PetMatch\Presentation\Responses\JsonResponse;

final class ListLikedPetsController
{
    public function __construct(
        private readonly ListLikedPets $listLikedPets,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        try {
            return JsonResponse::ok([
                'data' => $this->listLikedPets->execute(),
            ]);
        } catch (NotAuthenticatedException $exception) {
            return JsonResponse::unauthorized([
                'error' => $exception->getMessage(),
            ]);
        }
    }
}
