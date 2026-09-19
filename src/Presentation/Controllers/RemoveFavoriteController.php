<?php

declare(strict_types=1);

namespace PetMatch\Presentation\Controllers;

use PetMatch\Application\Auth\NotAuthenticatedException;
use PetMatch\Application\Engagement\RemoveFavorite;
use PetMatch\Infrastructure\Http\Request;
use PetMatch\Presentation\Responses\JsonResponse;

final class RemoveFavoriteController
{
    public function __construct(private readonly RemoveFavorite $removeFavorite) {}

    public function __invoke(Request $request): JsonResponse
    {
        try {
            return JsonResponse::ok(['data' => $this->removeFavorite->execute((int) ($request->parameter('id') ?? 0))]);
        } catch (NotAuthenticatedException $exception) {
            return JsonResponse::unauthorized(['error' => $exception->getMessage()]);
        }
    }
}
