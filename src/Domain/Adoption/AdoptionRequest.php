<?php

declare(strict_types=1);

namespace PetMatch\Domain\Adoption;

final class AdoptionRequest
{
    public function __construct(
        public readonly ?int $id,
        public readonly int $userId,
        public readonly int $petId,
        public readonly string $status,
        public readonly ?string $message,
    ) {
    }
}
