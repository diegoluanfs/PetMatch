<?php

declare(strict_types=1);

namespace PetMatch\Presentation\Controllers;

use PetMatch\Application\Adoption\ListAdoptionRequests;
use PetMatch\Infrastructure\Http\Request;
use PetMatch\Presentation\Responses\JsonResponse;

final class ListAdoptionRequestsController
{
    public function __construct(
        private readonly ListAdoptionRequests $listAdoptionRequests,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        return JsonResponse::ok([
            'data' => $this->listAdoptionRequests->execute(),
        ]);
    }
}
