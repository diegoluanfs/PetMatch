<?php

declare(strict_types=1);

namespace PetMatch\Presentation\Controllers;

use InvalidArgumentException;
use PetMatch\Application\Auth\NotAuthenticatedException;
use PetMatch\Application\Engagement\SwipePet;
use PetMatch\Infrastructure\Http\Request;
use PetMatch\Presentation\Responses\JsonResponse;

final class SwipePetController
{
    public function __construct(
        private readonly SwipePet $swipePet,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        try {
            $petId = (int) ($request->parameter('id') ?? 0);
            $action = (string) ($request->json()['action'] ?? '');

            return JsonResponse::ok([
                'data' => $this->swipePet->execute($petId, $action),
            ]);
        } catch (NotAuthenticatedException $exception) {
            return JsonResponse::unauthorized([
                'error' => $exception->getMessage(),
            ]);
        } catch (InvalidArgumentException $exception) {
            return JsonResponse::unprocessableEntity([
                'error' => $exception->getMessage(),
            ]);
        }
    }
}
