<?php

declare(strict_types=1);

namespace PetMatch\Presentation\Controllers;

use PetMatch\Application\Auth\NotAuthenticatedException;
use PetMatch\Application\Engagement\CreateFavorite;
use PetMatch\Application\Engagement\FavoritePetUnavailableException;
use PetMatch\Infrastructure\Http\Request;
use PetMatch\Presentation\Responses\JsonResponse;

final class CreateFavoriteController
{
    public function __construct(private readonly CreateFavorite $createFavorite) {}

    public function __invoke(Request $request): JsonResponse
    {
        try {
            return JsonResponse::created(['data' => $this->createFavorite->execute((int) ($request->parameter('id') ?? 0))]);
        } catch (NotAuthenticatedException $exception) {
            return JsonResponse::unauthorized(['error' => $exception->getMessage()]);
        } catch (FavoritePetUnavailableException $exception) {
            return JsonResponse::unprocessableEntity(['error' => $exception->getMessage()]);
        }
    }
}
