<?php

declare(strict_types=1);

namespace PetMatch\Presentation\Controllers;

use PetMatch\Application\Adoption\ListOrganizationAdoptionRequests;
use PetMatch\Infrastructure\Http\Request;
use PetMatch\Presentation\Responses\JsonResponse;

final class ListOrganizationAdoptionRequestsController
{
    public function __construct(
        private readonly ListOrganizationAdoptionRequests $listOrganizationAdoptionRequests,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        return JsonResponse::ok([
            'data' => $this->listOrganizationAdoptionRequests->execute(),
        ]);
    }
}
