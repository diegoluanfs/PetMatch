<?php

declare(strict_types=1);

namespace PetMatch\Domain\User;

final class User
{
    public function __construct(
        public readonly ?int $id,
        public readonly ?int $organizationId,
        public readonly string $name,
        public readonly string $email,
        public readonly string $passwordHash,
        public readonly string $role,
        public readonly string $status,
    ) {
    }
}
