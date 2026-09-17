<?php

declare(strict_types=1);

namespace PetMatch\Infrastructure\Http;

use PetMatch\Presentation\Responses\JsonResponse;

final class ApplicationKernel
{
    public function __construct(
        private readonly Router $router,
    ) {
    }

    public function handle(Request $request): JsonResponse
    {
        return $this->router->dispatch($request);
    }
}
