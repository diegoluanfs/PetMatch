<?php

declare(strict_types=1);

namespace PetMatch\Presentation\Requests\Pet;

use PetMatch\Infrastructure\Http\Request;

final class AddPetPhotoRequest
{
    public function __construct(
        public readonly array $input,
    ) {
    }

    public static function fromRequest(Request $request): self
    {
        return new self(array_merge($request->json(), $request->form()));
    }
}
