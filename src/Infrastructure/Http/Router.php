<?php

declare(strict_types=1);

namespace PetMatch\Infrastructure\Http;

use PetMatch\Presentation\Responses\Response;
use PetMatch\Presentation\Responses\JsonResponse;

final class Router
{
    /**
        * @param array<string, callable(Request): Response> $routes
     */
    public function __construct(
        private readonly array $routes,
    ) {
    }

    public function dispatch(Request $request): Response
    {
        $routeKey = $request->method . ' ' . $request->path;

        if (array_key_exists($routeKey, $this->routes)) {
            $handler = $this->routes[$routeKey];

            return $handler($request);
        }

        foreach ($this->routes as $definition => $handler) {
            [$method, $pattern] = explode(' ', $definition, 2);

            if ($method !== $request->method || !str_contains($pattern, '{')) {
                continue;
            }

            $regex = preg_replace('/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/', '(?P<$1>[^/]+)', $pattern);

            if ($regex === null) {
                continue;
            }

            if (!preg_match('#^' . $regex . '$#', $request->path, $matches)) {
                continue;
            }

            $parameters = [];

            foreach ($matches as $key => $value) {
                if (is_string($key)) {
                    $parameters[$key] = $value;
                }
            }

            return $handler($request->withParameters($parameters));
        }

        return JsonResponse::notFound();
    }
}
