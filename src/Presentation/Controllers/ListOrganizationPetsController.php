<?php

declare(strict_types=1);

namespace PetMatch\Presentation\Controllers;

use PetMatch\Application\Auth\ForbiddenException;
use PetMatch\Application\Auth\NotAuthenticatedException;
use PetMatch\Application\Pet\ListOrganizationPets;
use PetMatch\Infrastructure\Http\Request;
use PetMatch\Presentation\Responses\JsonResponse;

final class ListOrganizationPetsController
{
    public function __construct(
        private readonly ListOrganizationPets $listOrganizationPets,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        try {
            return JsonResponse::ok([
                'data' => $this->listOrganizationPets->execute(),
            ]);
        } catch (NotAuthenticatedException $exception) {
            return JsonResponse::unauthorized(['error' => $exception->getMessage()]);
        } catch (ForbiddenException $exception) {
            return JsonResponse::forbidden(['error' => $exception->getMessage()]);
        }
    }
}
