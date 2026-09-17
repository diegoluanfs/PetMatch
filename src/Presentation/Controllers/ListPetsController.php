<?php

declare(strict_types=1);

namespace PetMatch\Presentation\Controllers;

use PetMatch\Application\Pet\ListPets;
use PetMatch\Infrastructure\Http\Request;
use PetMatch\Presentation\Responses\JsonResponse;

final class ListPetsController
{
    public function __construct(
        private readonly ListPets $listPets,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        return JsonResponse::ok([
            'data' => $this->listPets->execute(),
        ]);
    }
}
