<?php

declare(strict_types=1);

namespace PetMatch\Domain\Organization;

final class Organization
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $name,
        public readonly ?string $description,
        public readonly string $email,
        public readonly string $phone,
        public readonly string $status,
    ) {
    }
}
