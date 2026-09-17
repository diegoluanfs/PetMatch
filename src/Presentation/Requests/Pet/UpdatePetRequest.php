<?php

declare(strict_types=1);

namespace PetMatch\Presentation\Requests\Pet;

use PetMatch\Infrastructure\Http\Request;

final class UpdatePetRequest
{
    public function __construct(
        public readonly array $input,
    ) {
    }

    public static function fromRequest(Request $request): self
    {
        return new self($request->json());
    }
}
