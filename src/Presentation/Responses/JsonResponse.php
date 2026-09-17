<?php

declare(strict_types=1);

namespace PetMatch\Presentation\Responses;

final class JsonResponse
{
    /**
     * @param array<string, mixed> $payload
     */
    public function __construct(
        private readonly int $statusCode,
        private readonly array $payload,
    ) {
    }

    public static function ok(array $payload): self
    {
        return new self(200, $payload);
    }

    public static function created(array $payload): self
    {
        return new self(201, $payload);
    }

    public static function unprocessableEntity(array $payload): self
    {
        return new self(422, $payload);
    }

    public static function conflict(array $payload): self
    {
        return new self(409, $payload);
    }

    public static function unauthorized(array $payload): self
    {
        return new self(401, $payload);
    }

    public static function notFound(): self
    {
        return new self(404, ['error' => 'Not Found']);
    }

    public function send(): void
    {
        http_response_code($this->statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(
            $this->payload,
            JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
        );
    }
}
