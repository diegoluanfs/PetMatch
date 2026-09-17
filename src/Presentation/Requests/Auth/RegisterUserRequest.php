<?php

declare(strict_types=1);

namespace PetMatch\Presentation\Requests\Auth;

use InvalidArgumentException;
use PetMatch\Infrastructure\Http\Request;

final class RegisterUserRequest
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly string $password,
    ) {
    }

    public static function fromRequest(Request $request): self
    {
        $payload = $request->json();

        return new self(
            (string) ($payload['name'] ?? ''),
            (string) ($payload['email'] ?? ''),
            (string) ($payload['password'] ?? ''),
        );
    }
}
