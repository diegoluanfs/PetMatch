<?php

declare(strict_types=1);

namespace PetMatch\Presentation\Controllers;

use PetMatch\Application\Auth\NotAuthenticatedException;
use PetMatch\Application\Engagement\ListFavorites;
use PetMatch\Infrastructure\Http\Request;
use PetMatch\Presentation\Responses\JsonResponse;

final class ListFavoritesController
{
    public function __construct(private readonly ListFavorites $listFavorites) {}

    public function __invoke(Request $request): JsonResponse
    {
        try {
            return JsonResponse::ok(['data' => $this->listFavorites->execute()]);
        } catch (NotAuthenticatedException $exception) {
            return JsonResponse::unauthorized(['error' => $exception->getMessage()]);
        }
    }
}
