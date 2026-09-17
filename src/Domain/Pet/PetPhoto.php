<?php

declare(strict_types=1);

namespace PetMatch\Domain\Pet;

final class PetPhoto
{
    public function __construct(
        public readonly ?int $id,
        public readonly int $petId,
        public readonly string $path,
        public readonly int $sortOrder,
    ) {
    }
}
