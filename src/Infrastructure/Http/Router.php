<?php

declare(strict_types=1);

namespace PetMatch\Infrastructure\Http;

use PetMatch\Presentation\Responses\JsonResponse;

final class Router
{
    /**
     * @param array<string, callable(Request): JsonResponse> $routes
     */
    public function __construct(
        private readonly array $routes,
    ) {
    }

    public function dispatch(Request $request): JsonResponse
    {
        $routeKey = $request->method . ' ' . $request->path;

        if (array_key_exists($routeKey, $this->routes)) {
            $handler = $this->routes[$routeKey];

            return $handler($request);
        }

        return JsonResponse::notFound();
    }
}
