<?php

declare(strict_types=1);

namespace PetMatch\Infrastructure\Http;

final class Request
{
    public function __construct(
        public readonly string $method,
        public readonly string $path,
        public readonly string $body,
        private readonly array $parameters = [],
    ) {
    }

    public static function fromGlobals(): self
    {
        $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

        return new self(
            strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET'),
            $path,
            file_get_contents('php://input') ?: '',
        );
    }

    public function parameter(string $name): ?string
    {
        $value = $this->parameters[$name] ?? null;

        if (!is_scalar($value)) {
            return null;
        }

        return (string) $value;
    }

    /**
     * @param array<string, scalar> $parameters
     */
    public function withParameters(array $parameters): self
    {
        return new self(
            $this->method,
            $this->path,
            $this->body,
            $parameters,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function json(): array
    {
        if ($this->body === '') {
            return [];
        }

        $decoded = json_decode($this->body, true);

        if (!is_array($decoded)) {
            return [];
        }

        return $decoded;
    }
}
