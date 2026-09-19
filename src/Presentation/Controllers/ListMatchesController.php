<?php

declare(strict_types=1);

namespace PetMatch\Presentation\Controllers;

use PetMatch\Application\Auth\NotAuthenticatedException;
use PetMatch\Application\Engagement\ListMatches;
use PetMatch\Infrastructure\Http\Request;
use PetMatch\Presentation\Responses\JsonResponse;

final class ListMatchesController
{
    public function __construct(private readonly ListMatches $listMatches) {}

    public function __invoke(Request $request): JsonResponse
    {
        try {
            return JsonResponse::ok(['data' => $this->listMatches->execute()]);
        } catch (NotAuthenticatedException $exception) {
            return JsonResponse::unauthorized(['error' => $exception->getMessage()]);
        }
    }
}
