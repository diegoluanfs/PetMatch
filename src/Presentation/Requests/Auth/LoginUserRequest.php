<?php

declare(strict_types=1);

namespace PetMatch\Presentation\Requests\Auth;

use PetMatch\Infrastructure\Http\Request;

final class LoginUserRequest
{
    public function __construct(
        public readonly string $email,
        public readonly string $password,
    ) {
    }

    public static function fromRequest(Request $request): self
    {
        $payload = $request->json();

        return new self(
            (string) ($payload['email'] ?? ''),
            (string) ($payload['password'] ?? ''),
        );
    }
}
