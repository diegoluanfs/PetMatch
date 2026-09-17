<?php

declare(strict_types=1);

namespace PetMatch\Presentation\Controllers;

use PetMatch\Application\Pet\GetPet;
use PetMatch\Application\Pet\PetNotFoundException;
use PetMatch\Infrastructure\Http\Request;
use PetMatch\Presentation\Responses\JsonResponse;

final class GetPetController
{
    public function __construct(
        private readonly GetPet $getPet,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        try {
            $petId = (int) ($request->parameter('id') ?? 0);

            return JsonResponse::ok([
                'data' => $this->getPet->execute($petId),
            ]);
        } catch (PetNotFoundException $exception) {
            return JsonResponse::notFoundWithMessage($exception->getMessage());
        }
    }
}
