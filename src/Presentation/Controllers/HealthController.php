<?php

declare(strict_types=1);

namespace PetMatch\Presentation\Controllers;

use PetMatch\Application\System\GetHealthStatus;
use PetMatch\Infrastructure\Http\Request;
use PetMatch\Presentation\Responses\JsonResponse;

final class HealthController
{
    public function __construct(
        private readonly GetHealthStatus $getHealthStatus,
    ) {
    }

    public function __invoke(Request $request): JsonResponse
    {
        return JsonResponse::ok($this->getHealthStatus->execute());
    }
}
