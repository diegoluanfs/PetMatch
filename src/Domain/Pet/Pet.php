<?php

declare(strict_types=1);

namespace PetMatch\Domain\Pet;

final class Pet
{
    public function __construct(
        public readonly ?int $id,
        public readonly int $organizationId,
        public readonly string $name,
        public readonly string $description,
        public readonly string $animalType,
        public readonly string $breed,
        public readonly string $gender,
        public readonly ?string $birthDate,
        public readonly string $size,
        public readonly string $status,
        public readonly string $city,
        public readonly string $state,
        public readonly ?string $latitude,
        public readonly ?string $longitude,
    ) {
    }
}
