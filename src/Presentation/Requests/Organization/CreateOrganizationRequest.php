<?php

declare(strict_types=1);

namespace PetMatch\Presentation\Requests\Organization;

use PetMatch\Infrastructure\Http\Request;

final class CreateOrganizationRequest
{
    public function __construct(
        public readonly string $name,
        public readonly string $description,
        public readonly string $email,
        public readonly string $phone,
    ) {
    }

    public static function fromRequest(Request $request): self
    {
        $payload = $request->json();

        return new self(
            (string) ($payload['name'] ?? ''),
            (string) ($payload['description'] ?? ''),
            (string) ($payload['email'] ?? ''),
            (string) ($payload['phone'] ?? ''),
        );
    }
}
